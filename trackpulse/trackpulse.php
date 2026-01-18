<?php
/**
 * Plugin Name: TrackPulse – WooCommerce Order Tracking + WhatsApp Notifications
 * Description: Adds shipment tracking, customer tracking page, and WhatsApp notifications for WooCommerce orders.
 * Version: 1.0.0
 * Author: TrackPulse
 * Text Domain: trackpulse
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'TRACKPULSE_VERSION' ) ) {
	define( 'TRACKPULSE_VERSION', '1.0.0' );
}

if ( ! defined( 'TRACKPULSE_PLUGIN_FILE' ) ) {
	define( 'TRACKPULSE_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'TRACKPULSE_PLUGIN_DIR' ) ) {
	define( 'TRACKPULSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'TRACKPULSE_PLUGIN_URL' ) ) {
	define( 'TRACKPULSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

require_once TRACKPULSE_PLUGIN_DIR . 'includes/Autoloader.php';

TrackPulse\Autoloader::register();

register_activation_hook( __FILE__, array( 'TrackPulse\\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TrackPulse\\Deactivator', 'deactivate' ) );

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	TrackPulse\Plugin::instance();
} );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
