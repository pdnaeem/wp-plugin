import { db, initDb, runInTransaction } from '../db.js';
import { buildRssConnector } from './rss.js';

initDb();

const selectSources = db.prepare('SELECT * FROM sources WHERE enabled = 1');
const updateSourceRun = db.prepare('UPDATE sources SET last_run_at = datetime(\'now\') WHERE id = ?');

const insertPosition = db.prepare(`
  INSERT INTO positions (
    title, url, source, source_type, posted_at, deadline, deadline_text, status,
    country, country_code, university_id, university, domain, field, funding,
    employment_type, fingerprint
  ) VALUES (
    @title, @url, @source, @source_type, @posted_at, @deadline, @deadline_text, @status,
    @country, @country_code, @university_id, @university, @domain, @field, @funding,
    @employment_type, @fingerprint
  )
`);

const sourceUrlExists = db.prepare('SELECT 1 FROM positions WHERE url = ?');
const fingerprintExists = db.prepare('SELECT 1 FROM positions WHERE fingerprint = ?');

const runSource = async (source) => {
  if (source.type !== 'rss') {
    return { source, inserted: 0, skipped: 0, error: 'Unsupported source type' };
  }

  const connector = buildRssConnector({
    name: source.name,
    url: source.url,
    countryCode: source.country_code,
    universityName: null
  });

  const items = await connector.fetch();
  let inserted = 0;
  let skipped = 0;

  runInTransaction(() => {
    for (const item of items) {
      const position = connector.parse(item);
      if (!position.url) {
        skipped += 1;
        continue;
      }
      if (sourceUrlExists.get(position.url) || fingerprintExists.get(position.fingerprint)) {
        skipped += 1;
        continue;
      }
      insertPosition.run({
        ...position,
        funding: position.funding || null,
        employment_type: position.employment_type || 'PhD position'
      });
      inserted += 1;
    }
  });

  updateSourceRun.run(source.id);
  return { source, inserted, skipped };
};

const runAll = async () => {
  const sources = selectSources.all();
  const results = [];
  for (const source of sources) {
    results.push(await runSource(source));
  }
  return results;
};

if (process.argv.includes('--all')) {
  runAll().then((results) => {
    console.log(JSON.stringify({ results }, null, 2));
  });
}

export { runSource, runAll };
