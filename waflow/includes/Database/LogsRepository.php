<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogsRepository {
	public function add( $data ) {
		global $wpdb;

		$defaults = array(
			'automation_id' => 0,
			'contact_id' => null,
			'status' => 'success',
			'message' => '',
			'payload' => array(),
			'created_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'waflow_automation_logs',
			array(
				'automation_id' => $data['automation_id'],
				'contact_id' => $data['contact_id'],
				'status' => $data['status'],
				'message' => $data['message'],
				'payload' => wp_json_encode( $data['payload'] ),
				'created_at' => $data['created_at'],
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function query( $args ) {
		global $wpdb;

		$defaults = array(
			'per_page' => 20,
			'offset' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}waflow_automation_logs ORDER BY created_at DESC LIMIT %d OFFSET %d",
			(int) $args['per_page'],
			(int) $args['offset']
		);

		return $wpdb->get_results( $sql );
	}

	public function count() {
		global $wpdb;
		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_automation_logs" );
	}
}
