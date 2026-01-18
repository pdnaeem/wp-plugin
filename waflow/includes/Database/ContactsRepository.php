<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactsRepository {
	public function create( $data ) {
		global $wpdb;

		$defaults = array(
			'name' => '',
			'phone' => '',
			'email' => '',
			'source' => '',
			'assigned_user_id' => null,
			'tags' => '',
			'custom_fields' => '',
			'first_seen' => current_time( 'mysql' ),
			'last_seen' => current_time( 'mysql' ),
			'created_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'waflow_contacts',
			array(
				'name' => $data['name'],
				'phone' => $data['phone'],
				'email' => $data['email'],
				'source' => $data['source'],
				'assigned_user_id' => $data['assigned_user_id'],
				'tags' => $data['tags'],
				'custom_fields' => $data['custom_fields'],
				'first_seen' => $data['first_seen'],
				'last_seen' => $data['last_seen'],
				'created_at' => $data['created_at'],
				'updated_at' => $data['updated_at'],
			),
			array( '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function update( $contact_id, $data ) {
		global $wpdb;

		$allowed = array_intersect_key(
			$data,
			array_flip( array( 'name', 'phone', 'email', 'source', 'assigned_user_id', 'tags', 'custom_fields', 'last_seen', 'updated_at' ) )
		);

		if ( empty( $allowed ) ) {
			return false;
		}

		$formats = array();
		foreach ( $allowed as $key => $value ) {
			$formats[] = ( 'assigned_user_id' === $key ) ? '%d' : '%s';
		}

		return $wpdb->update(
			$wpdb->prefix . 'waflow_contacts',
			$allowed,
			array( 'id' => $contact_id ),
			$formats,
			array( '%d' )
		);
	}

	public function find_by_phone_or_email( $phone, $email ) {
		global $wpdb;

		$phone = sanitize_text_field( $phone );
		$email = sanitize_email( $email );

		if ( empty( $phone ) && empty( $email ) ) {
			return 0;
		}

		if ( $phone && $email ) {
			$sql = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}waflow_contacts WHERE phone = %s OR email = %s LIMIT 1",
				$phone,
				$email
			);
		} elseif ( $phone ) {
			$sql = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}waflow_contacts WHERE phone = %s LIMIT 1",
				$phone
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}waflow_contacts WHERE email = %s LIMIT 1",
				$email
			);
		}

		return (int) $wpdb->get_var( $sql );
	}

	public function get( $contact_id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}waflow_contacts WHERE id = %d", $contact_id )
		);
	}

	public function query( $args ) {
		global $wpdb;

		$defaults = array(
			'search' => '',
			'source' => '',
			'per_page' => 20,
			'offset' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$where = 'WHERE 1=1';
		$params = array();

		if ( $args['search'] ) {
			$where .= ' AND (name LIKE %s OR phone LIKE %s OR email LIKE %s)';
			$like = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		if ( $args['source'] ) {
			$where .= ' AND source = %s';
			$params[] = $args['source'];
		}

		$sql = "SELECT * FROM {$wpdb->prefix}waflow_contacts {$where} ORDER BY last_seen DESC LIMIT %d OFFSET %d";
		$params[] = (int) $args['per_page'];
		$params[] = (int) $args['offset'];

		return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
	}

	public function count( $args ) {
		global $wpdb;

		$defaults = array(
			'search' => '',
			'source' => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$where = 'WHERE 1=1';
		$params = array();

		if ( $args['search'] ) {
			$where .= ' AND (name LIKE %s OR phone LIKE %s OR email LIKE %s)';
			$like = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		if ( $args['source'] ) {
			$where .= ' AND source = %s';
			$params[] = $args['source'];
		}

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_contacts {$where}";
		return (int) $wpdb->get_var( $wpdb->prepare( $sql, $params ) );
	}
}
