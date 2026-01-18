<?php
namespace TrackPulse\Api;

use TrackPulse\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rest {
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route( 'trackpulse/v1', '/track', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle_track' ),
			'permission_callback' => '__return_true',
		) );
	}

	public function handle_track( $request ) {
		$order_id = absint( $request->get_param( 'order_id' ) );
		$email = sanitize_email( $request->get_param( 'email' ) );
		$order = $order_id ? wc_get_order( $order_id ) : null;

		if ( ! $order || strtolower( $order->get_billing_email() ) !== strtolower( $email ) ) {
			return new \WP_REST_Response( array( 'message' => __( 'Order not found.', 'trackpulse' ) ), 404 );
		}

		$meta = Helpers::get_order_tracking_meta( $order );
		return new \WP_REST_Response( array(
			'order_id'        => $order->get_id(),
			'order_status'    => $order->get_status(),
			'tracking_number' => $meta['tracking_number'],
			'courier'         => $meta['courier_name'],
			'tracking_url'    => $meta['tracking_url'],
			'shipped_date'    => $meta['shipped_date'],
			'delivery_status' => $meta['delivery_status'],
		), 200 );
	}
}
