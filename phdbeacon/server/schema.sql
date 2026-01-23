PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS universities (
  id INTEGER PRIMARY KEY,
  canonical_name TEXT NOT NULL UNIQUE,
  country_code TEXT,
  homepage TEXT,
  created_at TEXT DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS university_aliases (
  alias TEXT PRIMARY KEY,
  university_id INTEGER NOT NULL,
  FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sources (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL,
  type TEXT NOT NULL,
  url TEXT NOT NULL UNIQUE,
  country_code TEXT,
  university_id INTEGER,
  enabled INTEGER DEFAULT 1,
  last_run_at TEXT,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS positions (
  id INTEGER PRIMARY KEY,
  title TEXT NOT NULL,
  url TEXT NOT NULL UNIQUE,
  source TEXT,
  source_type TEXT,
  posted_at TEXT,
  deadline TEXT,
  deadline_text TEXT,
  status TEXT DEFAULT 'open',
  country TEXT,
  country_code TEXT,
  city TEXT,
  university_id INTEGER,
  university TEXT,
  domain TEXT,
  field TEXT,
  funding TEXT,
  employment_type TEXT,
  fingerprint TEXT UNIQUE,
  created_at TEXT DEFAULT (datetime('now')),
  FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_positions_deadline ON positions(deadline);
CREATE INDEX IF NOT EXISTS idx_positions_country_code ON positions(country_code);
CREATE INDEX IF NOT EXISTS idx_positions_university_id ON positions(university_id);
CREATE INDEX IF NOT EXISTS idx_positions_domain ON positions(domain);
CREATE INDEX IF NOT EXISTS idx_positions_field ON positions(field);
CREATE INDEX IF NOT EXISTS idx_positions_status ON positions(status);
