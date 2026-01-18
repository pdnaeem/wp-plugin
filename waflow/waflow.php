<?php
/**
 * Plugin Name: WAFlow – WhatsApp Chat, CRM & Automations
 * Description: WhatsApp chat widget, mini CRM, and automations for WordPress.
 * Version: 1.0.0
 * Author: WAFlow
 * Text Domain: waflow
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WAFLOW_VERSION', '1.0.0' );

define( 'WAFLOW_PLUGIN_FILE', __FILE__ );

define( 'WAFLOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define( 'WAFLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WAFLOW_PLUGIN_DIR . 'includes/Autoloader.php';

WAFlow\Autoloader::register();

register_activation_hook( __FILE__, array( 'WAFlow\\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WAFlow\\Deactivator', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WAFlow\\Plugin', 'init' ) );
