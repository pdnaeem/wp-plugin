import { db } from '../db.js';

const selectAlias = db.prepare('SELECT university_id FROM university_aliases WHERE lower(alias) = lower(?)');
const insertUniversity = db.prepare(
  'INSERT INTO universities (canonical_name, country_code, homepage) VALUES (?, ?, ?)'
);
const insertAlias = db.prepare(
  'INSERT OR IGNORE INTO university_aliases (alias, university_id) VALUES (?, ?)'
);
const selectUniversity = db.prepare('SELECT id, canonical_name FROM universities WHERE id = ?');

export const normalizeUniversity = (rawName, countryCode = null, homepage = null) => {
  if (!rawName) return { id: null, name: null };
  const trimmed = rawName.trim();
  const alias = selectAlias.get(trimmed);
  if (alias) {
    const uni = selectUniversity.get(alias.university_id);
    return { id: uni?.id ?? null, name: uni?.canonical_name ?? trimmed };
  }
  const info = insertUniversity.run(trimmed, countryCode, homepage);
  insertAlias.run(trimmed, info.lastInsertRowid);
  return { id: info.lastInsertRowid, name: trimmed };
};
