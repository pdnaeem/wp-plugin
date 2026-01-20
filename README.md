# COMLEAM Web (Laravel + MySQL)

Production-ready, shared-hosting friendly implementation of a COMLEAM-like system for construction materials leaching/emissions modeling.

## Features
- Modules: Geometry, Weather, Substances, Materials, Emission Functions, Calculations, Reporting/Export.
- CSV ingestion (semicolon-separated) with validation + summary stats.
- Database-driven job queue with cron-based processing.
- PDF reports (dompdf) and ZIP exports (CSV + codebook).
- Shared hosting-friendly: no daemons, no long-running HTTP requests.

## Requirements
- PHP 8.x
- MySQL/MariaDB
- cPanel shared hosting with cron support

## Deployment on cPanel

1. **Upload Files**
   - Upload the repository contents to `/home/USER/public_html/comleam` (or similar).
   - Ensure the document root points to `/public`.

2. **Install Dependencies (from Terminal or SSH)**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Environment Setup**
   - Copy `.env.example` to `.env` and fill in database credentials.
   - Set an `INSTALLER_KEY` value that will be used by the installer wizard.
   - Generate app key:
     ```bash
     php artisan key:generate
     ```

4. **Storage Link**
   ```bash
   php artisan storage:link
   ```

5. **Run Migrations + Seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Cron Job (Queue Runner)**
   Add a cron job (every minute or every 5 minutes):
   ```
   */1 * * * * /usr/local/bin/php /home/USER/public_html/comleam/artisan comleam:run-queue --max=1
   ```

7. **File Upload Limits**
   - Ensure `upload_max_filesize` and `post_max_size` are large enough.

### Optional Install Wizard (No SSH)
If you do not have terminal access on cPanel, you can use the built-in installer:

1. Set `INSTALLER_KEY` in `.env` or `.env.example` before uploading.
2. Visit `/install` in the browser.
3. Provide the installer key, app URL, and database credentials.
4. The installer will write `.env`, generate the app key, run migrations/seeders, and create the storage link.
5. After completion it writes `storage/app/installed.lock` to prevent re-running.

If you do not want the installer exposed, remove the `/install` routes after setup.

## CSV Templates
Templates are located in `templates/`.

- `geometry_template.csv`: component_id;surface_area_m2;exposure_class;material_subtype_code
- `weather_template.csv`: timestamp;precipitation_mm;temperature_c;wind_speed_ms;humidity_pct
- `leaching_template.csv`: cumulative_runoff_l_m2;cumulative_emission_mg_m2

## Notes on Performance
- Weather rows are stored in a table and streamed in chunks during simulation.
- Calculations run via cron queue; browser requests never run long simulations.

## Testing
Run unit tests (requires composer install):
```bash
php artisan test
```

## Security
- Auth required for all pages.
- CSRF protection enabled.
- Uploaded files stored with random names under `storage/app/public`.
