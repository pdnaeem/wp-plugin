# PhDBeacon Europe

PhDBeacon Europe (Owned by Naeem Ur Rehman) is a lightweight PhD positions database focused on European universities. It ingests RSS feeds, normalizes metadata, and serves a fast, mobile-first UI.

## Features
- Express + SQLite API with robust filtering and facets
- RSS ingestion pipeline with connector interface for future parsers
- Country, university, subject normalization + deduplication
- Tailwind-powered frontend with search, filters, sorting, pagination
- Admin page for managing sources (protected by `ADMIN_KEY`)

## Requirements
- Node.js 18+
- npm

## Setup
```bash
cd phdbeacon/server
npm install
```

Create a `.env` file in `phdbeacon/server`:
```
PORT=4000
ADMIN_KEY=change-me
CORS_ORIGIN=http://localhost:5173
DB_PATH=phdbeacon.sqlite
```

## Run the API
```bash
cd phdbeacon/server
npm start
```

## Run the frontend
Serve the `web` folder with any static server. Example:
```bash
cd phdbeacon/web
npx serve -l 5173
```

## Add RSS sources
1. Open `http://localhost:5173/admin.html`.
2. Enter the admin key.
3. Add RSS feed details and run ingestion.

You can also use the API directly:
```bash
curl -X POST http://localhost:4000/api/sources \
  -H "Content-Type: application/json" \
  -H "x-admin-key: change-me" \
  -d '{"name":"Example", "type":"rss", "url":"https://example.edu/phd/rss", "country_code":"DE"}'
```

## Extend normalization
- **Countries**: add variants to `server/normalize/country.js`.
- **Universities**: insert aliases into `universities`/`university_aliases` tables.
- **Subjects**: update keyword mappings in `server/normalize/subject.js`.

## Daily expiry job
Mark expired positions as closed:
```bash
cd phdbeacon/server
npm run prune:expired
```

Example cron entry:
```
0 2 * * * cd /path/to/phdbeacon/server && npm run prune:expired >> /var/log/phdbeacon-prune.log 2>&1
```

## Connector development
See `server/ingest/connectors/connector.interface.md` and the sample RSS connector for guidance on custom parsers.
