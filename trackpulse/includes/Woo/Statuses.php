<?php
namespace TrackPulse\Woo;

use TrackPulse\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Statuses {
	public function init() {
		add_action( 'init', array( $this, 'register_statuses' ) );
		add_filter( 'wc_order_statuses', array( $this, 'add_statuses' ) );
		add_filter( 'woocommerce_order_actions', array( $this, 'add_mark_actions' ) );
		add_action( 'woocommerce_order_action_trackpulse_mark_shipped', array( $this, 'mark_shipped' ) );
		add_action( 'woocommerce_order_action_trackpulse_mark_delivered', array( $this, 'mark_delivered' ) );

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'handle_order_placed' ), 10, 1 );
		add_action( 'woocommerce_payment_complete', array( $this, 'handle_payment_complete' ), 10, 1 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'handle_status_change' ), 10, 4 );
	}

	public function register_statuses() {
		$settings = Helpers::get_settings();
		if ( empty( $settings['statuses']['enable_custom_statuses'] ) ) {
			return;
		}

		register_post_status( 'wc-shipped', array(
			'label'                     => _x( 'Shipped', 'Order status', 'trackpulse' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Shipped (%s)', 'Shipped (%s)', 'trackpulse' ),
		) );

		register_post_status( 'wc-delivered', array(
			'label'                     => _x( 'Delivered', 'Order status', 'trackpulse' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Delivered (%s)', 'Delivered (%s)', 'trackpulse' ),
		) );
	}

	public function add_statuses( $statuses ) {
		$settings = Helpers::get_settings();
		if ( empty( $settings['statuses']['enable_custom_statuses'] ) ) {
			return $statuses;
		}

		$statuses['wc-shipped'] = _x( 'Shipped', 'Order status', 'trackpulse' );
		$statuses['wc-delivered'] = _x( 'Delivered', 'Order status', 'trackpulse' );
		return $statuses;
	}

	public function add_mark_actions( $actions ) {
		$settings = Helpers::get_settings();
		if ( empty( $settings['statuses']['enable_custom_statuses'] ) ) {
			return $actions;
		}

		$actions['trackpulse_mark_shipped'] = __( 'Mark as Shipped', 'trackpulse' );
		$actions['trackpulse_mark_delivered'] = __( 'Mark as Delivered', 'trackpulse' );
		return $actions;
	}

	public function mark_shipped( $order ) {
		$order->update_status( 'shipped' );
	}

	public function mark_delivered( $order ) {
		$order->update_status( 'delivered' );
	}

	public function handle_order_placed( $order_id ) {
		do_action( 'trackpulse_send_whatsapp_event', $order_id, 'order_placed', array( 'source' => 'checkout' ) );
	}

	public function handle_payment_complete( $order_id ) {
		do_action( 'trackpulse_send_whatsapp_event', $order_id, 'payment_complete', array( 'source' => 'payment' ) );
	}

	public function handle_status_change( $order_id, $old_status, $new_status, $order ) {
		switch ( $new_status ) {
			case 'processing':
				do_action( 'trackpulse_send_whatsapp_event', $order_id, 'status_processing', array( 'source' => 'status_change' ) );
				break;
			case 'shipped':
				do_action( 'trackpulse_send_whatsapp_event', $order_id, 'shipped', array( 'source' => 'status_change' ) );
				break;
			case 'delivered':
				do_action( 'trackpulse_send_whatsapp_event', $order_id, 'delivered', array( 'source' => 'status_change' ) );
				break;
			case 'cancelled':
				do_action( 'trackpulse_send_whatsapp_event', $order_id, 'cancelled', array( 'source' => 'status_change' ) );
				break;
			case 'refunded':
				do_action( 'trackpulse_send_whatsapp_event', $order_id, 'refunded', array( 'source' => 'status_change' ) );
				break;
		}
	}
}
