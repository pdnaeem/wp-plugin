# Ecolepedia Writing Marketplace

A production-ready WordPress plugin for running a writing-service marketplace with orders, author applications, dashboards, and configurable settings.

## Requirements
- WordPress 6.2+
- PHP 8.0+
- MySQL 5.7+/MariaDB equivalent

## Installation
1. Upload the `ecolepedia-marketplace` folder to `/wp-content/plugins/`.
2. Activate **Ecolepedia Writing Marketplace**.
3. Visit **Ecolepedia > Settings** to configure branding, uploads, and payment keys.
4. Use the shortcodes below to build pages.

## Shortcodes
- `[ecolepedia_customer_register]`
- `[ecolepedia_author_register]`
- `[ecolepedia_login]`
- `[ecolepedia_place_order]`
- `[ecolepedia_checkout]`
- `[ecolepedia_customer_dashboard]`
- `[ecolepedia_author_dashboard]`
- `[ecolepedia_order_details id=""]`
- `[ecolepedia_how_it_works]`
- `[ecolepedia_pricing_table]`

## Template Overrides
Copy any template from `ecolepedia-marketplace/templates/` into:
`/wp-content/themes/your-theme/ecolepedia-marketplace/`

## Demo Data
Navigate to **Ecolepedia > Demo Mode** and click **Generate Demo Data**.

## Support
All plugin text is internationalization-ready using the `ecolepedia-marketplace` textdomain.
