<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class System_Status {
    public static function get(): array {
        return [
            __('WordPress Version', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) => get_bloginfo('version'),
            __('PHP Version', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) => PHP_VERSION,
            __('MySQL Version', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) => self::get_mysql_version(),
            __('Upload Max Filesize', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) => ini_get('upload_max_filesize'),
            __('Cron Enabled', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) => wp_next_scheduled('ecolepedia_marketplace_cron_check') ? __('Yes', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) : __('No', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            __('Uploads Writable', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) => wp_is_writable(wp_upload_dir()['basedir']) ? __('Yes', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) : __('No', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
        ];
    }

    private static function get_mysql_version(): string {
        global $wpdb;
        return $wpdb->db_version();
    }
}
