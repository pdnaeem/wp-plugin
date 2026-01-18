<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_Forms_Integration {
	private $crm;

	public function __construct( WAFlow_CRM $crm ) {
		$this->crm = $crm;
	}

	public function init() {
		add_action( 'fluentform/submission_inserted', array( $this, 'handle_fluent_form_submission' ), 10, 3 );
		add_action( 'wpforms_process_complete', array( $this, 'handle_wpforms_submission' ), 10, 4 );
	}

	public function handle_fluent_form_submission( $entry_id, $form_data, $form ) {
		$mapped = $this->map_form_data( $form_data, $form->id, 'fluentforms' );
		if ( empty( $mapped['phone'] ) ) {
			return;
		}

		$contact_id = $this->upsert_contact_from_form( $mapped, 'fluentforms' );
		$this->crm->add_activity( $contact_id, 'fluentforms_submission', wp_json_encode( $mapped ) );

		do_action( 'waflow_fluentforms_submission', $contact_id, $form, $form_data );
	}

	public function handle_wpforms_submission( $fields, $entry, $form_data, $entry_id ) {
		$mapped = $this->map_form_data( $fields, $form_data['id'] ?? 0, 'wpforms' );
		if ( empty( $mapped['phone'] ) ) {
			return;
		}

		$contact_id = $this->upsert_contact_from_form( $mapped, 'wpforms' );
		$this->crm->add_activity( $contact_id, 'wpforms_submission', wp_json_encode( $mapped ) );

		do_action( 'waflow_wpforms_submission', $contact_id, $form_data, $fields );
	}

	private function map_form_data( $raw_fields, $form_id, $source ) {
		$map = apply_filters( 'waflow_form_field_map', array(), $form_id, $source );
		$mapped = array(
			'name' => '',
			'phone' => '',
			'email' => '',
			'notes' => '',
		);

		foreach ( $map as $key => $field_key ) {
			if ( isset( $raw_fields[ $field_key ] ) ) {
				$mapped[ $key ] = sanitize_text_field( wp_unslash( $raw_fields[ $field_key ] ) );
			}
		}

		if ( empty( $mapped['phone'] ) ) {
			$mapped['phone'] = $this->find_first_value_by_label( $raw_fields, 'phone' );
		}

		if ( empty( $mapped['email'] ) ) {
			$mapped['email'] = $this->find_first_value_by_label( $raw_fields, 'email' );
		}

		if ( empty( $mapped['name'] ) ) {
			$mapped['name'] = $this->find_first_value_by_label( $raw_fields, 'name' );
		}

		return apply_filters( 'waflow_form_submission_mapped', $mapped, $raw_fields, $form_id, $source );
	}

	private function find_first_value_by_label( $fields, $needle ) {
		foreach ( $fields as $field ) {
			if ( is_array( $field ) && isset( $field['name'] ) ) {
				$name = strtolower( $field['name'] );
				if ( false !== strpos( $name, $needle ) ) {
					return sanitize_text_field( wp_unslash( $field['value'] ?? '' ) );
				}
			}

			if ( is_string( $field ) && false !== strpos( strtolower( $field ), $needle ) ) {
				return sanitize_text_field( wp_unslash( $field ) );
			}
		}

		return '';
	}

	private function upsert_contact_from_form( $mapped, $source ) {
		$contact_id = $this->crm->find_contact_by_phone_or_email( $mapped['phone'], $mapped['email'] );
		$data = array(
			'name' => $mapped['name'],
			'phone' => $mapped['phone'],
			'email' => $mapped['email'],
			'source' => $source,
			'notes' => $mapped['notes'],
			'last_activity' => current_time( 'mysql' ),
		);

		if ( $contact_id ) {
			$this->crm->update_contact( $contact_id, $data );
			return $contact_id;
		}

		return $this->crm->create_contact( $data );
	}
}
