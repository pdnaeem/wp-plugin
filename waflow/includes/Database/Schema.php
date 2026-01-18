<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Schema {
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$contacts = $wpdb->prefix . 'waflow_contacts';
		$activities = $wpdb->prefix . 'waflow_activities';
		$deals = $wpdb->prefix . 'waflow_deals';
		$automations = $wpdb->prefix . 'waflow_automations';
		$logs = $wpdb->prefix . 'waflow_automation_logs';
		$queue = $wpdb->prefix . 'waflow_queue';

		$sql_contacts = "CREATE TABLE {$contacts} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(190) DEFAULT NULL,
			phone VARCHAR(50) NOT NULL,
			email VARCHAR(190) DEFAULT NULL,
			source VARCHAR(50) DEFAULT NULL,
			assigned_user_id BIGINT UNSIGNED DEFAULT NULL,
			tags TEXT DEFAULT NULL,
			custom_fields LONGTEXT DEFAULT NULL,
			first_seen DATETIME NOT NULL,
			last_seen DATETIME NOT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY phone (phone),
			KEY email (email),
			KEY source (source)
		) {$charset_collate};";

		$sql_activities = "CREATE TABLE {$activities} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			contact_id BIGINT UNSIGNED NOT NULL,
			type VARCHAR(50) NOT NULL,
			payload LONGTEXT DEFAULT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY contact_id (contact_id),
			KEY type (type)
		) {$charset_collate};";

		$sql_deals = "CREATE TABLE {$deals} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			contact_id BIGINT UNSIGNED NOT NULL,
			title VARCHAR(190) NOT NULL,
			stage VARCHAR(50) NOT NULL,
			value DECIMAL(12,2) DEFAULT NULL,
			expected_close DATE DEFAULT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY contact_id (contact_id),
			KEY stage (stage)
		) {$charset_collate};";

		$sql_automations = "CREATE TABLE {$automations} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(190) NOT NULL,
			status TINYINT(1) NOT NULL DEFAULT 1,
			definition LONGTEXT NOT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$sql_logs = "CREATE TABLE {$logs} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			automation_id BIGINT UNSIGNED NOT NULL,
			contact_id BIGINT UNSIGNED DEFAULT NULL,
			status VARCHAR(20) NOT NULL,
			message TEXT DEFAULT NULL,
			payload LONGTEXT DEFAULT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY automation_id (automation_id),
			KEY contact_id (contact_id),
			KEY status (status)
		) {$charset_collate};";

		$sql_queue = "CREATE TABLE {$queue} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			run_at DATETIME NOT NULL,
			payload LONGTEXT NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
			last_error TEXT DEFAULT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY run_at (run_at),
			KEY status (status)
		) {$charset_collate};";

		dbDelta( $sql_contacts );
		dbDelta( $sql_activities );
		dbDelta( $sql_deals );
		dbDelta( $sql_automations );
		dbDelta( $sql_logs );
		dbDelta( $sql_queue );
	}
}
