import { db, initDb } from '../db.js';

initDb();

const today = new Date().toISOString().slice(0, 10);

const stmt = db.prepare(
  "UPDATE positions SET status = 'closed' WHERE deadline IS NOT NULL AND deadline < ? AND status = 'open'"
);

const result = stmt.run(today);
console.log(`Updated ${result.changes} positions to closed.`);
