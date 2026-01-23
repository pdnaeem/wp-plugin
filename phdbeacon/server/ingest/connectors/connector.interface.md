# Connector Interface

Each connector should export an object with:

- `metadata`: `{ name, type, homepage }`
- `fetch()`: returns an array of raw items (RSS items, API responses, scraped entries)
- `parse(item)`: returns a normalized position object compatible with the `positions` table

The normalized object should include:

```
{
  title,
  url,
  source,
  source_type,
  posted_at,
  deadline,
  deadline_text,
  status,
  country,
  country_code,
  university_id,
  university,
  domain,
  field,
  funding,
  employment_type,
  fingerprint
}
```

Connectors can reuse helper functions in `normalize/` and `ingest/rss.js`.
