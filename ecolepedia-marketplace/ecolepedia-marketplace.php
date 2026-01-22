<?php
/**
 * Plugin Name: Ecolepedia Writing Marketplace
 * Description: A writing-service marketplace for Ecolepedia with orders, dashboards, and payment-ready architecture.
 * Version: 1.0.0
 * Author: Ecolepedia
 * Text Domain: ecolepedia-marketplace
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ECOLEPEDIA_MARKETPLACE_VERSION', '1.0.0');
define('ECOLEPEDIA_MARKETPLACE_PATH', plugin_dir_path(__FILE__));
define('ECOLEPEDIA_MARKETPLACE_URL', plugin_dir_url(__FILE__));
define('ECOLEPEDIA_MARKETPLACE_BASENAME', plugin_basename(__FILE__));

define('ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN', 'ecolepedia-marketplace');

define('ECOLEPEDIA_MARKETPLACE_TABLE_MESSAGES', 'ecolepedia_messages');
define('ECOLEPEDIA_MARKETPLACE_TABLE_TRANSACTIONS', 'ecolepedia_transactions');
define('ECOLEPEDIA_MARKETPLACE_TABLE_REVISIONS', 'ecolepedia_revisions');
define('ECOLEPEDIA_MARKETPLACE_TABLE_AUTHOR_PROFILES', 'ecolepedia_author_profiles');

define('ECOLEPEDIA_MARKETPLACE_UPLOAD_DIR', 'ecolepedia');

define('ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS', 'manage_ecolepedia_settings');
define('ECOLEPEDIA_MARKETPLACE_CAP_ORDERS', 'manage_ecolepedia_orders');
define('ECOLEPEDIA_MARKETPLACE_CAP_FINANCE', 'view_ecolepedia_finance');
define('ECOLEPEDIA_MARKETPLACE_CAP_AUTHORS', 'manage_ecolepedia_authors');

define('ECOLEPEDIA_MARKETPLACE_CAP_AUTHOR_VIEW', 'author_view_orders');
define('ECOLEPEDIA_MARKETPLACE_CAP_AUTHOR_SUBMIT', 'author_submit_work');
define('ECOLEPEDIA_MARKETPLACE_CAP_AUTHOR_CHAT', 'author_chat');

define('ECOLEPEDIA_MARKETPLACE_CAP_CUSTOMER_PLACE', 'customer_place_orders');
define('ECOLEPEDIA_MARKETPLACE_CAP_CUSTOMER_CHAT', 'customer_chat');
define('ECOLEPEDIA_MARKETPLACE_CAP_CUSTOMER_DOWNLOAD', 'customer_download_files');

require_once ECOLEPEDIA_MARKETPLACE_PATH . 'includes/class-autoloader.php';
Ecolepedia\Marketplace\Autoloader::register();

register_activation_hook(__FILE__, ['Ecolepedia\\Marketplace\\Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['Ecolepedia\\Marketplace\\Plugin', 'deactivate']);

add_action('plugins_loaded', static function () {
    Ecolepedia\Marketplace\Plugin::get_instance();
});
