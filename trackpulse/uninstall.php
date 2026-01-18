<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'trackpulse_settings', array() );
$delete = isset( $settings['general']['delete_on_uninstall'] ) && $settings['general']['delete_on_uninstall'];

if ( ! $delete ) {
	return;
}

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}trackpulse_logs" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}trackpulse_queue" );

delete_option( 'trackpulse_settings' );
delete_option( 'trackpulse_couriers' );
