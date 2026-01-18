<?php
namespace TrackPulse\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QueueRepository {
	public static function enqueue( $run_at, $order_id, $event, $payload ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_queue';

		$wpdb->insert(
			$table,
			array(
				'run_at'     => $run_at,
				'order_id'   => $order_id,
				'event'      => $event,
				'payload'    => wp_json_encode( $payload ),
				'attempts'   => 0,
				'status'     => 'pending',
				'last_error' => null,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%d', '%s', '%s', '%d', '%s', '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	public static function get_pending_jobs( $limit = 20 ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_queue';
		$now = current_time( 'mysql' );

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE status = %s AND run_at <= %s ORDER BY run_at ASC LIMIT %d",
				'pending',
				$now,
				$limit
			),
			ARRAY_A
		);
	}

	public static function mark_processing( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_queue';
		$wpdb->update( $table, array( 'status' => 'processing' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	}

	public static function mark_done( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_queue';
		$wpdb->update( $table, array( 'status' => 'done' ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	}

	public static function mark_failed( $id, $attempts, $error ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_queue';
		$wpdb->update(
			$table,
			array(
				'status'     => 'failed',
				'attempts'   => $attempts,
				'last_error' => $error,
			),
			array( 'id' => $id ),
			array( '%s', '%d', '%s' ),
			array( '%d' )
		);
	}

	public static function reschedule( $id, $attempts, $run_at, $error ) {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_queue';
		$wpdb->update(
			$table,
			array(
				'status'     => 'pending',
				'attempts'   => $attempts,
				'run_at'     => $run_at,
				'last_error' => $error,
			),
			array( 'id' => $id ),
			array( '%s', '%d', '%s', '%s' ),
			array( '%d' )
		);
	}
}
