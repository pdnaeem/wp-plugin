# Developer Guide: Ecolepedia Writing Marketplace

## Architecture
- **Main bootstrap**: `ecolepedia-marketplace.php`
- **Core classes**: `/includes` with a simple PSR-4-style autoloader
- **Admin UI**: `/admin` (reserved for additional list tables)
- **Frontend templates**: `/templates` with override support
- **Assets**: `/assets/css` and `/assets/js`

## Required Pages
On activation, `Pages::create_required_pages()` creates the customer, author, checkout, and dashboard pages with their shortcodes pre-filled.

## Extending Payment Gateways
1. Add a gateway class under `/includes` (e.g., `class-gateway-stripe.php`).
2. Implement a `charge()` method and hook into checkout handling.
3. Store configuration in `Settings::OPTION_KEY`.
4. Use server-side API calls only; never expose secret keys on the frontend.

## Data Model Notes
- Orders are a CPT (`ecolepedia_order`).
- Subjects and document types are seeded on activation via `Orders::seed_taxonomies()`.
- Heavy relational data (messages, transactions, revisions, author profiles) uses custom tables created via `Database::install()`.

## Security Checklist
- Sanitize/escape all input/output.
- Use nonces for form and AJAX actions.
- Check capabilities before rendering admin pages.
- Validate file types and sizes using `Uploads::validate_file()`.

## Adding New Add-ons or Pricing Rules
1. Add new settings to `Settings::defaults()`.
2. Create UI controls in `Settings::register_settings()`.
3. Update checkout summary template and any pricing logic.

## Demo Data
Use `Demo_Data::generate()` to seed demo content and users.
