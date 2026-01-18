<?php
namespace TrackPulse\Admin;

use TrackPulse\Capabilities;
use TrackPulse\WhatsApp\Sender;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class OrderActions {
	public function init() {
		add_filter( 'woocommerce_order_actions', array( $this, 'add_order_actions' ) );
		add_action( 'woocommerce_order_action_trackpulse_send_update', array( $this, 'handle_send_update' ) );
		add_action( 'woocommerce_order_action_trackpulse_send_tracking', array( $this, 'handle_send_tracking' ) );
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_action' ) );
		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_action' ), 10, 3 );
	}

	public function add_order_actions( $actions ) {
		$actions['trackpulse_send_update'] = __( 'Send WhatsApp: Order Update', 'trackpulse' );
		$actions['trackpulse_send_tracking'] = __( 'Send WhatsApp: Tracking Info', 'trackpulse' );
		return $actions;
	}

	public function handle_send_update( $order ) {
		do_action( 'trackpulse_send_whatsapp_event', $order->get_id(), 'status_processing', array( 'manual' => true ) );
	}

	public function handle_send_tracking( $order ) {
		do_action( 'trackpulse_send_whatsapp_event', $order->get_id(), 'shipped', array( 'manual' => true ) );
	}

	public function add_bulk_action( $actions ) {
		$actions['trackpulse_send_whatsapp'] = __( 'TrackPulse: Send WhatsApp (Selected orders)', 'trackpulse' );
		return $actions;
	}

	public function handle_bulk_action( $redirect_to, $action, $post_ids ) {
		if ( 'trackpulse_send_whatsapp' !== $action ) {
			return $redirect_to;
		}

		foreach ( $post_ids as $order_id ) {
			do_action( 'trackpulse_send_whatsapp_event', $order_id, 'status_processing', array( 'manual' => true ) );
		}

		return add_query_arg( 'trackpulse_bulk_sent', count( $post_ids ), $redirect_to );
	}
}
