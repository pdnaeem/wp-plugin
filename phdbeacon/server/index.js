import express from 'express';
import cors from 'cors';
import helmet from 'helmet';
import rateLimit from 'express-rate-limit';
import Joi from 'joi';
import dotenv from 'dotenv';
import { db, initDb } from './db.js';
import { runSource, runAll } from './ingest/index.js';

dotenv.config();
initDb();

const app = express();
const PORT = process.env.PORT || 4000;
const ADMIN_KEY = process.env.ADMIN_KEY || '';

app.use(helmet());
app.use(cors({ origin: process.env.CORS_ORIGIN || '*', methods: ['GET', 'POST', 'PATCH'] }));
app.use(express.json({ limit: '200kb' }));
app.use(rateLimit({ windowMs: 60 * 1000, max: 120 }));

const requireAdmin = (req, res, next) => {
  const key = req.header('x-admin-key') || req.query.admin_key;
  if (!ADMIN_KEY || key !== ADMIN_KEY) {
    return res.status(401).json({ error: 'Unauthorized' });
  }
  return next();
};

const limitNumber = (value, max, fallback) => {
  const parsed = Number.parseInt(value, 10);
  if (Number.isNaN(parsed)) return fallback;
  return Math.min(parsed, max);
};

app.get('/api/health', (req, res) => {
  res.json({ status: 'ok' });
});

app.get('/api/positions', (req, res) => {
  const schema = Joi.object({
    q: Joi.string().allow('').optional(),
    country_code: Joi.string().length(2).optional(),
    university_id: Joi.number().integer().optional(),
    domain: Joi.string().optional(),
    field: Joi.string().optional(),
    status: Joi.string().valid('open', 'closed', 'unknown').optional(),
    deadline_from: Joi.string().optional(),
    deadline_to: Joi.string().optional(),
    sort: Joi.string().valid('deadline_asc', 'deadline_desc', 'newest').optional(),
    limit: Joi.number().integer().min(1).max(200).optional(),
    offset: Joi.number().integer().min(0).optional()
  });

  const { value, error } = schema.validate(req.query);
  if (error) {
    return res.status(400).json({ error: 'Invalid query parameters' });
  }

  const filters = [];
  const params = {};

  if (value.q) {
    filters.push('(title LIKE @q OR university LIKE @q OR field LIKE @q OR domain LIKE @q)');
    params.q = `%${value.q}%`;
  }
  if (value.country_code) {
    filters.push('country_code = @country_code');
    params.country_code = value.country_code;
  }
  if (value.university_id) {
    filters.push('university_id = @university_id');
    params.university_id = value.university_id;
  }
  if (value.domain) {
    filters.push('domain = @domain');
    params.domain = value.domain;
  }
  if (value.field) {
    filters.push('field = @field');
    params.field = value.field;
  }
  if (value.status) {
    filters.push('status = @status');
    params.status = value.status;
  }
  if (value.deadline_from) {
    filters.push('(deadline IS NOT NULL AND deadline >= @deadline_from)');
    params.deadline_from = value.deadline_from;
  }
  if (value.deadline_to) {
    filters.push('(deadline IS NOT NULL AND deadline <= @deadline_to)');
    params.deadline_to = value.deadline_to;
  }

  const whereClause = filters.length ? `WHERE ${filters.join(' AND ')}` : '';
  const sort = value.sort === 'deadline_desc'
    ? 'ORDER BY deadline DESC'
    : value.sort === 'deadline_asc'
      ? 'ORDER BY deadline ASC'
      : 'ORDER BY created_at DESC';

  const limit = limitNumber(value.limit, 200, 20);
  const offset = limitNumber(value.offset, 100000, 0);

  const totalStmt = db.prepare(`SELECT COUNT(*) as count FROM positions ${whereClause}`);
  const rowsStmt = db.prepare(
    `SELECT * FROM positions ${whereClause} ${sort} LIMIT @limit OFFSET @offset`
  );

  const total = totalStmt.get(params).count;
  const rows = rowsStmt.all({ ...params, limit, offset });

  return res.json({ total, rows });
});

app.get('/api/facets', (req, res) => {
  const countries = db
    .prepare(
      `SELECT country_code as code, country as name, COUNT(*) as count
       FROM positions WHERE country_code IS NOT NULL
       GROUP BY country_code, country ORDER BY count DESC`
    )
    .all();

  const universities = db
    .prepare(
      `SELECT university_id as id, university as name, country_code, COUNT(*) as count
       FROM positions WHERE university_id IS NOT NULL
       GROUP BY university_id, university, country_code ORDER BY count DESC`
    )
    .all();

  const domains = db
    .prepare(
      `SELECT domain as name, COUNT(*) as count FROM positions
       WHERE domain IS NOT NULL GROUP BY domain ORDER BY count DESC`
    )
    .all();

  const fields = db
    .prepare(
      `SELECT field as name, COUNT(*) as count FROM positions
       WHERE field IS NOT NULL GROUP BY field ORDER BY count DESC`
    )
    .all();

  const statuses = db
    .prepare(
      `SELECT status as name, COUNT(*) as count FROM positions
       GROUP BY status ORDER BY count DESC`
    )
    .all();

  res.json({ countries, universities, domains, fields, statuses });
});

app.get('/api/position/:id', (req, res) => {
  const id = Number.parseInt(req.params.id, 10);
  if (Number.isNaN(id)) {
    return res.status(400).json({ error: 'Invalid ID' });
  }
  const position = db.prepare('SELECT * FROM positions WHERE id = ?').get(id);
  if (!position) {
    return res.status(404).json({ error: 'Not found' });
  }
  return res.json(position);
});

app.get('/api/sources', requireAdmin, (req, res) => {
  const sources = db.prepare('SELECT * FROM sources ORDER BY created_at DESC').all();
  res.json({ sources });
});

app.post('/api/sources', requireAdmin, (req, res) => {
  const schema = Joi.object({
    name: Joi.string().min(2).required(),
    type: Joi.string().valid('rss').required(),
    url: Joi.string().uri().required(),
    country_code: Joi.string().length(2).allow(null, '').optional(),
    university_id: Joi.number().integer().allow(null).optional()
  });
  const { value, error } = schema.validate(req.body);
  if (error) {
    return res.status(400).json({ error: 'Invalid payload' });
  }
  const stmt = db.prepare(
    `INSERT INTO sources (name, type, url, country_code, university_id)
     VALUES (@name, @type, @url, @country_code, @university_id)`
  );
  const result = stmt.run({
    ...value,
    country_code: value.country_code || null,
    university_id: value.university_id || null
  });
  res.status(201).json({ id: result.lastInsertRowid });
});

app.patch('/api/sources/:id', requireAdmin, (req, res) => {
  const id = Number.parseInt(req.params.id, 10);
  if (Number.isNaN(id)) {
    return res.status(400).json({ error: 'Invalid ID' });
  }
  const schema = Joi.object({
    enabled: Joi.boolean().required()
  });
  const { value, error } = schema.validate(req.body);
  if (error) {
    return res.status(400).json({ error: 'Invalid payload' });
  }
  db.prepare('UPDATE sources SET enabled = ? WHERE id = ?').run(value.enabled ? 1 : 0, id);
  res.json({ status: 'ok' });
});

app.post('/api/run/:sourceId', requireAdmin, async (req, res) => {
  const sourceId = Number.parseInt(req.params.sourceId, 10);
  if (Number.isNaN(sourceId)) {
    return res.status(400).json({ error: 'Invalid ID' });
  }
  const source = db.prepare('SELECT * FROM sources WHERE id = ?').get(sourceId);
  if (!source) {
    return res.status(404).json({ error: 'Source not found' });
  }
  try {
    const result = await runSource(source);
    return res.json({ result });
  } catch (error) {
    return res.status(500).json({ error: 'Ingestion failed' });
  }
});

app.post('/api/run/all', requireAdmin, async (req, res) => {
  try {
    const results = await runAll();
    return res.json({ results });
  } catch (error) {
    return res.status(500).json({ error: 'Ingestion failed' });
  }
});

app.use((req, res) => res.status(404).json({ error: 'Not found' }));

app.listen(PORT, () => {
  console.log(`PhDBeacon Europe server running on :${PORT}`);
});
