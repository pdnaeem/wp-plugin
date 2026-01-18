<?php

namespace WAFlow\Integrations;

use WAFlow\Database\ContactsRepository;
use WAFlow\Database\ActivitiesRepository;
use WAFlow\Helpers;
use WAFlow\Automations\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FluentForms {
	public function register() {
		$settings = Helpers::get_settings();
		if ( empty( $settings['integrations']['fluentforms'] ) ) {
			return;
		}

		add_action( 'fluentform/submission_inserted', array( $this, 'handle_submission' ), 10, 3 );
	}

	public function handle_submission( $entry_id, $form_data, $form ) {
		$settings = Helpers::get_settings();
		$map = $settings['integrations']['fluentforms_map'][ $form->id ?? 0 ] ?? array();
		$mapped = $this->map_fields( $form_data, $map );

		if ( empty( $mapped['phone'] ) ) {
			return;
		}

		$contacts = new ContactsRepository();
		$contact_id = $contacts->find_by_phone_or_email( $mapped['phone'], $mapped['email'] );
		$data = array(
			'name' => $mapped['name'],
			'phone' => $mapped['phone'],
			'email' => $mapped['email'],
			'source' => 'fluentforms',
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
		$activities->add( $contact_id, 'form_submit', array( 'form_id' => $form->id ?? 0, 'fields' => $mapped['fields'] ) );

		Engine::handle_trigger(
			'fluentforms_submit',
			array(
				'contact_id' => $contact_id,
				'form_id' => $form->id ?? 0,
				'payload' => array( 'fields' => $mapped['fields'] ),
			)
		);
	}

	private function map_fields( $form_data, $map ) {
		$result = array(
			'name' => '',
			'email' => '',
			'phone' => '',
			'message' => '',
			'tags' => '',
			'fields' => $form_data,
		);

		if ( ! empty( $map['name'] ) && isset( $form_data[ $map['name'] ] ) ) {
			$result['name'] = sanitize_text_field( $form_data[ $map['name'] ] );
		}
		if ( ! empty( $map['email'] ) && isset( $form_data[ $map['email'] ] ) ) {
			$result['email'] = sanitize_email( $form_data[ $map['email'] ] );
		}
		if ( ! empty( $map['phone'] ) && isset( $form_data[ $map['phone'] ] ) ) {
			$result['phone'] = sanitize_text_field( $form_data[ $map['phone'] ] );
		}
		if ( ! empty( $map['message'] ) && isset( $form_data[ $map['message'] ] ) ) {
			$result['message'] = sanitize_text_field( $form_data[ $map['message'] ] );
		}

		if ( empty( $result['phone'] ) ) {
			foreach ( $form_data as $value ) {
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
