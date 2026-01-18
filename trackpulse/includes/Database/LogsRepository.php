<?php
namespace TrackPulse\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogsRepository {
	public static function insert( $data ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_logs';

		$defaults = array(
			'order_id'   => null,
			'channel'    => 'system',
			'event'      => 'manual',
			'recipient'  => '',
			'status'     => 'success',
			'message'    => '',
			'response'   => '',
			'created_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$wpdb->insert(
			$table,
			$data,
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	public static function get_counts_last_days( $days = 7, $status = 'success' ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_logs';
		$after = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE status = %s AND created_at >= %s",
				$status,
				$after
			)
		);
	}

	public static function get_top_couriers( $limit = 5 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_logs';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT message, COUNT(*) as total FROM {$table} WHERE event = %s GROUP BY message ORDER BY total DESC LIMIT %d",
				'shipped',
				$limit
			),
			ARRAY_A
		);
	}
}
