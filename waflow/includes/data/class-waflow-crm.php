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

		$contact_id = (int) $wpdb->insert_id;

		do_action( 'waflow_contact_created', $contact_id, $data );

		return $contact_id;
	}

	public function update_contact( $contact_id, $data ) {
		global $wpdb;

		$allowed = array_intersect_key(
			$data,
			array_flip( array( 'name', 'phone', 'email', 'source', 'tags', 'notes', 'assigned_agent', 'last_activity' ) )
		);

		if ( empty( $allowed ) ) {
			return false;
		}

		$formats = array();
		foreach ( $allowed as $key => $value ) {
			$formats[] = ( 'assigned_agent' === $key ) ? '%d' : '%s';
		}

		$updated = $wpdb->update(
			$wpdb->prefix . 'waflow_contacts',
			$allowed,
			array( 'id' => $contact_id ),
			$formats,
			array( '%d' )
		);

		if ( false !== $updated ) {
			do_action( 'waflow_contact_updated', $contact_id, $allowed );
		}

		return $updated;
	}

	public function find_contact_by_phone_or_email( $phone, $email ) {
		global $wpdb;

		$phone = sanitize_text_field( $phone );
		$email = sanitize_email( $email );

		if ( empty( $phone ) && empty( $email ) ) {
			return 0;
		}

		if ( ! empty( $phone ) && ! empty( $email ) ) {
			$query = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}waflow_contacts WHERE phone = %s OR email = %s LIMIT 1",
				$phone,
				$email
			);
		} elseif ( ! empty( $phone ) ) {
			$query = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}waflow_contacts WHERE phone = %s LIMIT 1",
				$phone
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}waflow_contacts WHERE email = %s LIMIT 1",
				$email
			);
		}

		return (int) $wpdb->get_var( $query );
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
