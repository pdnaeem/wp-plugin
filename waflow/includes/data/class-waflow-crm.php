<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_CRM {
	public function create_contact( $data ) {
		global $wpdb;

		$defaults = array(
			'name' => '',
			'phone' => '',
			'email' => '',
			'source' => '',
			'tags' => '',
			'notes' => '',
			'assigned_agent' => null,
			'first_seen' => current_time( 'mysql' ),
			'last_activity' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'waflow_contacts',
			array(
				'name' => $data['name'],
				'phone' => $data['phone'],
				'email' => $data['email'],
				'source' => $data['source'],
				'tags' => $data['tags'],
				'notes' => $data['notes'],
				'assigned_agent' => $data['assigned_agent'],
				'first_seen' => $data['first_seen'],
				'last_activity' => $data['last_activity'],
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function add_activity( $contact_id, $type, $payload = '' ) {
		global $wpdb;

		$wpdb->insert(
			$wpdb->prefix . 'waflow_activities',
			array(
				'contact_id' => $contact_id,
				'activity_type' => $type,
				'payload' => $payload,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s' )
		);
	}

	public function get_contacts( $limit = 50 ) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}waflow_contacts ORDER BY last_activity DESC LIMIT %d",
			$limit
		);

		return $wpdb->get_results( $query );
	}

	public function get_contact( $contact_id ) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}waflow_contacts WHERE id = %d",
			$contact_id
		);

		return $wpdb->get_row( $query );
	}

	public function get_activities( $contact_id ) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}waflow_activities WHERE contact_id = %d ORDER BY created_at DESC",
			$contact_id
		);

		return $wpdb->get_results( $query );
	}
}
