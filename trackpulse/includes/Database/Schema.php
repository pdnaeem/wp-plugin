<?php
namespace TrackPulse\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Schema {
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$logs_table = $wpdb->prefix . 'trackpulse_logs';
		$queue_table = $wpdb->prefix . 'trackpulse_queue';

		$logs_sql = "CREATE TABLE {$logs_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			order_id BIGINT UNSIGNED NULL,
			channel VARCHAR(20) NOT NULL,
			event VARCHAR(50) NOT NULL,
			recipient VARCHAR(100) NOT NULL,
			status VARCHAR(20) NOT NULL,
			message TEXT NULL,
			response LONGTEXT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY order_id (order_id)
		) {$charset_collate};";

		$queue_sql = "CREATE TABLE {$queue_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			run_at DATETIME NOT NULL,
			order_id BIGINT UNSIGNED NULL,
			event VARCHAR(50) NOT NULL,
			payload LONGTEXT NULL,
			attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL,
			last_error TEXT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY (id),
			KEY run_at (run_at),
			KEY order_id (order_id)
		) {$charset_collate};";

		dbDelta( $logs_sql );
		dbDelta( $queue_sql );
	}
}
