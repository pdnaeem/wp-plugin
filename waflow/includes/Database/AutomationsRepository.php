<?php

namespace WAFlow\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AutomationsRepository {
	public function create( $data ) {
		global $wpdb;

		$defaults = array(
			'name' => '',
			'status' => 1,
			'definition' => array(),
			'created_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		$data = wp_parse_args( $data, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'waflow_automations',
			array(
				'name' => $data['name'],
				'status' => $data['status'],
				'definition' => wp_json_encode( $data['definition'] ),
				'created_at' => $data['created_at'],
				'updated_at' => $data['updated_at'],
			),
			array( '%s', '%d', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	public function update( $automation_id, $data ) {
		global $wpdb;

		$update = array(
			'name' => $data['name'],
			'status' => $data['status'],
			'definition' => wp_json_encode( $data['definition'] ),
			'updated_at' => current_time( 'mysql' ),
		);

		return $wpdb->update(
			$wpdb->prefix . 'waflow_automations',
			$update,
			array( 'id' => $automation_id ),
			array( '%s', '%d', '%s', '%s' ),
			array( '%d' )
		);
	}

	public function get( $automation_id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}waflow_automations WHERE id = %d", $automation_id )
		);
	}

	public function all_enabled_by_trigger( $trigger ) {
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}waflow_automations WHERE status = 1" );
		$filtered = array();

		foreach ( $rows as $row ) {
			$definition = json_decode( $row->definition, true );
			if ( isset( $definition['trigger']['type'] ) && $definition['trigger']['type'] === $trigger ) {
				$row->definition = $definition;
				$filtered[] = $row;
			}
		}

		return $filtered;
	}
}
