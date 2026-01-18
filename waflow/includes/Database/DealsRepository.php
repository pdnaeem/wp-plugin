<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DealsRepository {
	public function create( $data ) {
		global $wpdb;

		$defaults = array(
			'contact_id' => 0,
			'title' => '',
			'stage' => 'Lead',
			'value' => null,
			'expected_close' => null,
			'created_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'waflow_deals',
			array(
				'contact_id' => $data['contact_id'],
				'title' => $data['title'],
				'stage' => $data['stage'],
				'value' => $data['value'],
				'expected_close' => $data['expected_close'],
				'created_at' => $data['created_at'],
				'updated_at' => $data['updated_at'],
			),
			array( '%d', '%s', '%s', '%f', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function update_stage( $deal_id, $stage ) {
		global $wpdb;

		return $wpdb->update(
			$wpdb->prefix . 'waflow_deals',
			array(
				'stage' => $stage,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => $deal_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}
}
