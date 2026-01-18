<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_WooCommerce_Integration {
	private $crm;

	public function __construct( WAFlow_CRM $crm ) {
		$this->crm = $crm;
	}

	public function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'handle_new_order' ), 10, 3 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'handle_status_changed' ), 10, 4 );
	}

	public function handle_new_order( $order_id, $posted_data, $order ) {
		$contact_id = $this->create_or_update_contact( $order );

		$this->crm->add_activity(
			$contact_id,
			'woo_new_order',
			json_encode(
				array(
					'order_id' => $order_id,
					'total' => $order->get_total(),
				)
			)
		);

		do_action( 'waflow_woo_new_order', $order_id, $contact_id, $order );
	}

	public function handle_status_changed( $order_id, $old_status, $new_status, $order ) {
		$contact_id = $this->create_or_update_contact( $order );

		$this->crm->add_activity(
			$contact_id,
			'woo_status_changed',
			json_encode(
				array(
					'order_id' => $order_id,
					'old_status' => $old_status,
					'new_status' => $new_status,
				)
			)
		);

		do_action( 'waflow_woo_status_changed', $order_id, $old_status, $new_status, $contact_id, $order );
	}

	private function create_or_update_contact( $order ) {
		$phone = $order->get_billing_phone();
		$email = $order->get_billing_email();
		$name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );

		$contact_id = $this->crm->find_contact_by_phone_or_email( $phone, $email );

		$data = array(
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'source' => 'woocommerce',
			'last_activity' => current_time( 'mysql' ),
		);

		if ( $contact_id ) {
			$this->crm->update_contact( $contact_id, $data );
			return $contact_id;
		}

		return $this->crm->create_contact( $data );
	}
}
