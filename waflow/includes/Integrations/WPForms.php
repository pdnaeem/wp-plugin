<?php

namespace WAFlow\Integrations;

use WAFlow\Database\ContactsRepository;
use WAFlow\Database\ActivitiesRepository;
use WAFlow\Helpers;
use WAFlow\Automations\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPForms {
	public function register() {
		$settings = Helpers::get_settings();
		if ( empty( $settings['integrations']['wpforms'] ) ) {
			return;
		}

		add_action( 'wpforms_process_complete', array( $this, 'handle_submission' ), 10, 4 );
	}

	public function handle_submission( $fields, $entry, $form_data, $entry_id ) {
		$settings = Helpers::get_settings();
		$map = $settings['integrations']['wpforms_map'][ $form_data['id'] ?? 0 ] ?? array();

		$mapped = $this->map_fields( $fields, $map );
		if ( empty( $mapped['phone'] ) ) {
			return;
		}

		$contacts = new ContactsRepository();
		$contact_id = $contacts->find_by_phone_or_email( $mapped['phone'], $mapped['email'] );
		$data = array(
			'name' => $mapped['name'],
			'phone' => $mapped['phone'],
			'email' => $mapped['email'],
			'source' => 'wpforms',
			'tags' => $mapped['tags'],
			'last_seen' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		if ( $contact_id ) {
			$contacts->update( $contact_id, $data );
		} else {
			$data['first_seen'] = current_time( 'mysql' );
			$data['created_at'] = current_time( 'mysql' );
			$contact_id = $contacts->create( $data );
		}

		$activities = new ActivitiesRepository();
		$activities->add( $contact_id, 'form_submit', array( 'form_id' => $form_data['id'], 'fields' => $mapped['fields'] ) );

		Engine::handle_trigger(
			'wpforms_submit',
			array(
				'contact_id' => $contact_id,
				'form_id' => $form_data['id'],
				'payload' => array( 'fields' => $mapped['fields'] ),
			)
		);
	}

	private function map_fields( $fields, $map ) {
		$result = array(
			'name' => '',
			'email' => '',
			'phone' => '',
			'message' => '',
			'tags' => '',
			'fields' => array(),
		);

		foreach ( $fields as $field ) {
			$result['fields'][ $field['id'] ] = $field['value'];
		}

		if ( ! empty( $map['name'] ) && isset( $result['fields'][ $map['name'] ] ) ) {
			$result['name'] = sanitize_text_field( $result['fields'][ $map['name'] ] );
		}
		if ( ! empty( $map['email'] ) && isset( $result['fields'][ $map['email'] ] ) ) {
			$result['email'] = sanitize_email( $result['fields'][ $map['email'] ] );
		}
		if ( ! empty( $map['phone'] ) && isset( $result['fields'][ $map['phone'] ] ) ) {
			$result['phone'] = sanitize_text_field( $result['fields'][ $map['phone'] ] );
		}
		if ( ! empty( $map['message'] ) && isset( $result['fields'][ $map['message'] ] ) ) {
			$result['message'] = sanitize_text_field( $result['fields'][ $map['message'] ] );
		}

		if ( empty( $result['phone'] ) ) {
			foreach ( $result['fields'] as $value ) {
				if ( is_string( $value ) && preg_match( '/\+?[0-9\s\-\(\)]+/', $value ) ) {
					$result['phone'] = sanitize_text_field( $value );
					break;
				}
			}
		}

		if ( ! empty( $map['tags'] ) ) {
			$result['tags'] = sanitize_text_field( $map['tags'] );
		}

		return $result;
	}
}
