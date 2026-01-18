<?php
/**
 * Plugin Name: WAFlow – WhatsApp Chat, CRM & Automations
 * Description: Add a WhatsApp chat widget, capture leads, and manage lightweight CRM automations.
 * Version: 0.1.0
 * Author: WAFlow
 * Text Domain: waflow
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WAFLOW_VERSION', '0.1.0' );

define( 'WAFLOW_PLUGIN_FILE', __FILE__ );

define( 'WAFLOW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

define( 'WAFLOW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once WAFLOW_PLUGIN_DIR . 'includes/class-waflow-settings.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/class-waflow-activator.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/class-waflow-plugin.php';

register_activation_hook( __FILE__, array( 'WAFlow_Activator', 'activate' ) );

$waflow_plugin = new WAFlow_Plugin();
$waflow_plugin->init();
