<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'waflow_settings', array() );
$delete = $settings['advanced']['delete_on_uninstall'] ?? false;

if ( ! $delete ) {
	return;
}

global $wpdb;

$tables = array(
	$wpdb->prefix . 'waflow_contacts',
	$wpdb->prefix . 'waflow_activities',
	$wpdb->prefix . 'waflow_deals',
	$wpdb->prefix . 'waflow_automations',
	$wpdb->prefix . 'waflow_automation_logs',
	$wpdb->prefix . 'waflow_queue',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
}

delete_option( 'waflow_settings' );
delete_option( 'waflow_setup_complete' );
delete_option( 'waflow_round_robin_index' );
