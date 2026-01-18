<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_Activator {
	public static function activate() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$contacts_table = $wpdb->prefix . 'waflow_contacts';
		$activities_table = $wpdb->prefix . 'waflow_activities';
		$deals_table = $wpdb->prefix . 'waflow_deals';
		$automations_table = $wpdb->prefix . 'waflow_automations';
		$automation_logs_table = $wpdb->prefix . 'waflow_automation_logs';
		$agents_table = $wpdb->prefix . 'waflow_agents';
		$teams_table = $wpdb->prefix . 'waflow_teams';
		$contact_meta_table = $wpdb->prefix . 'waflow_contact_meta';

		$sql_contacts = "CREATE TABLE {$contacts_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(190) NOT NULL,
			phone varchar(50) NOT NULL,
			email varchar(190) DEFAULT NULL,
			source varchar(100) DEFAULT NULL,
			tags longtext DEFAULT NULL,
			notes longtext DEFAULT NULL,
			assigned_agent bigint(20) unsigned DEFAULT NULL,
			first_seen datetime NOT NULL,
			last_activity datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY phone (phone),
			KEY email (email)
		) {$charset_collate};";

		$sql_activities = "CREATE TABLE {$activities_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			contact_id bigint(20) unsigned NOT NULL,
			activity_type varchar(100) NOT NULL,
			payload longtext DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY contact_id (contact_id)
		) {$charset_collate};";

		$sql_deals = "CREATE TABLE {$deals_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			contact_id bigint(20) unsigned NOT NULL,
			stage varchar(100) NOT NULL DEFAULT 'Lead',
			value decimal(10,2) DEFAULT NULL,
			expected_close datetime DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY contact_id (contact_id)
		) {$charset_collate};";

		$sql_automations = "CREATE TABLE {$automations_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(190) NOT NULL,
			enabled tinyint(1) NOT NULL DEFAULT 1,
			config longtext NOT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$sql_automation_logs = "CREATE TABLE {$automation_logs_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			automation_id bigint(20) unsigned NOT NULL,
			status varchar(50) NOT NULL,
			message longtext DEFAULT NULL,
			payload longtext DEFAULT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY automation_id (automation_id)
		) {$charset_collate};";

		$sql_agents = "CREATE TABLE {$agents_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(190) NOT NULL,
			title varchar(190) DEFAULT NULL,
			phone varchar(50) NOT NULL,
			email varchar(190) DEFAULT NULL,
			department varchar(190) DEFAULT NULL,
			avatar varchar(255) DEFAULT NULL,
			schedule longtext DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$sql_teams = "CREATE TABLE {$teams_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			name varchar(190) NOT NULL,
			description longtext DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$sql_contact_meta = "CREATE TABLE {$contact_meta_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			contact_id bigint(20) unsigned NOT NULL,
			meta_key varchar(190) NOT NULL,
			meta_value longtext DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY contact_id (contact_id),
			KEY meta_key (meta_key)
		) {$charset_collate};";

		dbDelta( $sql_contacts );
		dbDelta( $sql_activities );
		dbDelta( $sql_deals );
		dbDelta( $sql_automations );
		dbDelta( $sql_automation_logs );
		dbDelta( $sql_agents );
		dbDelta( $sql_teams );
		dbDelta( $sql_contact_meta );

		if ( false === get_option( 'waflow_settings' ) ) {
			add_option( 'waflow_settings', WAFlow_Settings::defaults() );
		}
	}
}
