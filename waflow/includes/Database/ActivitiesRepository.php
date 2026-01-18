<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActivitiesRepository {
	public function add( $contact_id, $type, $payload = array() ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'waflow_activities',
			array(
				'contact_id' => $contact_id,
				'type' => $type,
				'payload' => wp_json_encode( $payload ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function list_for_contact( $contact_id, $limit = 50 ) {
		global $wpdb;

		$sql = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}waflow_activities WHERE contact_id = %d ORDER BY created_at DESC LIMIT %d",
			$contact_id,
			$limit
		);

		return $wpdb->get_results( $sql );
	}
}
