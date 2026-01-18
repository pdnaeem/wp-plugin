<?php

namespace WAFlow\Integrations;

use WAFlow\Database\ContactsRepository;
use WAFlow\Database\ActivitiesRepository;
use WAFlow\Helpers;
use WAFlow\Automations\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerce {
	public function register() {
		$settings = Helpers::get_settings();
		if ( empty( $settings['integrations']['woocommerce'] ) ) {
			return;
		}

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'handle_new_order' ), 10, 3 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'handle_status_changed' ), 10, 4 );
	}

	public function handle_new_order( $order_id, $posted_data, $order ) {
		$contact_id = $this->upsert_contact( $order, 'woocommerce' );
		$payload = $this->build_payload( $order );

		$activities = new ActivitiesRepository();
		$activities->add( $contact_id, 'order_created', $payload );

		Engine::handle_trigger( 'woocommerce_order_created', array( 'contact_id' => $contact_id, 'payload' => $payload ) );
	}

	public function handle_status_changed( $order_id, $old_status, $new_status, $order ) {
		$contact_id = $this->upsert_contact( $order, 'woocommerce' );
		$payload = $this->build_payload( $order );
		$payload['from_status'] = $old_status;
		$payload['to_status'] = $new_status;

		$activities = new ActivitiesRepository();
		$activities->add( $contact_id, 'order_status', $payload );

		Engine::handle_trigger(
			'woocommerce_order_status_changed',
			array(
				'contact_id' => $contact_id,
				'from_status' => $old_status,
				'to_status' => $new_status,
				'payload' => $payload,
			)
		);
	}

	private function upsert_contact( $order, $source ) {
		$contacts = new ContactsRepository();
		$phone = $order->get_billing_phone();
		$email = $order->get_billing_email();
		$name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );

		$contact_id = $contacts->find_by_phone_or_email( $phone, $email );
		$data = array(
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'source' => $source,
			'last_seen' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		if ( $contact_id ) {
			$contacts->update( $contact_id, $data );
			return $contact_id;
		}

		$data['first_seen'] = current_time( 'mysql' );
		$data['created_at'] = current_time( 'mysql' );
		return $contacts->create( $data );
	}

	private function build_payload( $order ) {
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$items[] = $item->get_name();
		}

		return array(
			'order_id' => $order->get_id(),
			'order_total' => $order->get_total(),
			'currency' => $order->get_currency(),
			'product_names' => implode( ', ', $items ),
			'country' => $order->get_billing_country(),
			'status' => $order->get_status(),
		);
	}
}
