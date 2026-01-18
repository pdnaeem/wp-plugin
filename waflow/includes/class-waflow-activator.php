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

		dbDelta( $sql_contacts );
		dbDelta( $sql_activities );
		dbDelta( $sql_deals );
		dbDelta( $sql_automations );
		dbDelta( $sql_automation_logs );

		if ( false === get_option( 'waflow_settings' ) ) {
			$defaults = array(
				'enabled' => true,
				'phone' => '',
				'prefill' => __( 'Hi! I need help.', 'waflow' ),
				'position' => 'right',
				'color' => '#25d366',
				'greeting' => __( 'Hi! How can we help?', 'waflow' ),
				'show_pre_chat' => true,
				'consent_label' => __( 'I agree to be contacted via WhatsApp.', 'waflow' ),
				'delete_on_uninstall' => false,
			);
			add_option( 'waflow_settings', $defaults );
		}
	}
}
