<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QueueRepository {
	public function enqueue( $run_at, $payload ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'waflow_queue',
			array(
				'run_at' => $run_at,
				'payload' => wp_json_encode( $payload ),
				'status' => 'pending',
				'attempts' => 0,
				'created_at' => current_time( 'mysql' ),
				'updated_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%d', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function fetch_due( $limit = 10 ) {
		global $wpdb;

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}waflow_queue WHERE status = 'pending' AND run_at <= %s ORDER BY run_at ASC LIMIT %d",
			current_time( 'mysql' ),
			$limit
		);

		return $wpdb->get_results( $sql );
	}

	public function mark_processing( $job_id ) {
		global $wpdb;
		return $wpdb->update(
			$wpdb->prefix . 'waflow_queue',
			array( 'status' => 'processing', 'updated_at' => current_time( 'mysql' ) ),
			array( 'id' => $job_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}

	public function mark_complete( $job_id ) {
		global $wpdb;
		return $wpdb->update(
			$wpdb->prefix . 'waflow_queue',
			array( 'status' => 'complete', 'updated_at' => current_time( 'mysql' ) ),
			array( 'id' => $job_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}

	public function mark_failed( $job_id, $error, $attempts ) {
		global $wpdb;
		return $wpdb->update(
			$wpdb->prefix . 'waflow_queue',
			array(
				'status' => 'failed',
				'attempts' => $attempts,
				'last_error' => $error,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => $job_id ),
			array( '%s', '%d', '%s', '%s' ),
			array( '%d' )
		);
	}
}
