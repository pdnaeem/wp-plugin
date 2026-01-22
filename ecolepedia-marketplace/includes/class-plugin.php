<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Plugin {
    private static ?Plugin $instance = null;

    public static function get_instance(): Plugin {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init(): void {
        load_plugin_textdomain(ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN, false, dirname(ECOLEPEDIA_MARKETPLACE_BASENAME) . '/languages');

        (new Assets())->register();
        (new Orders())->register();
        (new Settings())->register();
        (new Shortcodes())->register();
        (new Admin())->register();
        (new Uploads())->register();
        (new Cron())->register();
        (new Demo_Data())->register();
    }

    public static function activate(): void {
        Roles::add_roles();
        Database::install();
        Orders::register_post_type();
        Orders::register_taxonomies();
        Settings::register_settings();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }
}
