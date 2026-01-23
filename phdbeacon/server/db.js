import Database from 'better-sqlite3';
import fs from 'fs';
import path from 'path';

const dbPath = process.env.DB_PATH || path.resolve('phdbeacon.sqlite');
const schemaPath = path.resolve('schema.sql');

export const db = new Database(dbPath);

db.pragma('foreign_keys = ON');

export const initDb = () => {
  const schema = fs.readFileSync(schemaPath, 'utf-8');
  db.exec(schema);
};

export const runInTransaction = (fn) => {
  const transaction = db.transaction(fn);
  return transaction();
};
