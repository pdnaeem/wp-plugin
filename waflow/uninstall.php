<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'waflow_settings', array() );

if ( empty( $settings['delete_on_uninstall'] ) ) {
	return;
}

global $wpdb;

$tables = array(
	$wpdb->prefix . 'waflow_contacts',
	$wpdb->prefix . 'waflow_activities',
	$wpdb->prefix . 'waflow_deals',
	$wpdb->prefix . 'waflow_automations',
	$wpdb->prefix . 'waflow_automation_logs',
	$wpdb->prefix . 'waflow_agents',
	$wpdb->prefix . 'waflow_teams',
	$wpdb->prefix . 'waflow_contact_meta',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
}

delete_option( 'waflow_settings' );
