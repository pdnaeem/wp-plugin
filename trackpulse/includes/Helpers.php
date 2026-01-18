<?php
namespace TrackPulse;

use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helpers {
	public static function get_settings() {
		$defaults = self::default_settings();
		$saved = get_option( 'trackpulse_settings', array() );
		return wp_parse_args( $saved, $defaults );
	}

	public static function update_settings( $settings ) {
		update_option( 'trackpulse_settings', $settings, false );
	}

	public static function default_settings() {
		return array(
			'general'  => array(
				'enabled'                => true,
				'default_country_code'   => '1',
				'delete_on_uninstall'    => false,
			),
			'tracking' => array(
				'enable_panel'           => true,
				'show_items'             => true,
				'show_totals'            => false,
				'show_support_button'    => true,
			),
			'whatsapp' => array(
				'enabled'                => false,
				'mode'                   => 'click_to_chat',
				'support_phone'          => '',
				'admin_phone'            => '',
				'events'                 => array(
					'order_placed'      => true,
					'payment_complete'  => true,
					'status_processing' => true,
					'shipped'           => true,
					'delivered'         => true,
					'cancelled'         => false,
					'refunded'          => false,
				),
				'templates'              => self::default_templates(),
				'template_settings'      => array(),
			),
			'cloud_api' => array(
				'phone_number_id'        => '',
				'access_token'           => '',
				'api_version'            => 'v19.0',
				'message_mode'           => 'text',
			),
			'advanced' => array(
				'rate_limit_per_minute'  => 10,
				'debug_logging'          => false,
			),
			'statuses' => array(
				'enable_custom_statuses' => true,
			),
		);
	}

	public static function default_templates() {
		return array(
			'order_placed'     => 'Hi {billing_first_name}, we received your order #{order_number} at {site_name}. Total: {order_total} {currency}. We\'ll update you soon.',
			'payment_complete' => 'Payment confirmed for order #{order_number}. Thank you! We\'ll start processing your order.',
			'shipped'          => 'Good news! Your order #{order_number} has been shipped via {courier_name}. Tracking: {tracking_number}. Track here: {tracking_url}',
			'delivered'        => 'Your order #{order_number} has been delivered. Thank you for shopping with {site_name}!',
			'cancelled'        => 'Your order #{order_number} has been cancelled. If you have questions, contact us at {support_phone}.',
			'refunded'         => 'Your order #{order_number} has been refunded. Please contact support if you need assistance.',
		);
	}

	public static function get_order_tracking_meta( WC_Order $order ) {
		return array(
			'courier_id'     => $order->get_meta( '_trackpulse_courier_id' ),
			'courier_name'   => $order->get_meta( '_trackpulse_courier_name' ),
			'tracking_number'=> $order->get_meta( '_trackpulse_tracking_number' ),
			'tracking_url'   => $order->get_meta( '_trackpulse_tracking_url' ),
			'shipped_date'   => $order->get_meta( '_trackpulse_shipped_date' ),
			'delivery_status'=> $order->get_meta( '_trackpulse_delivery_status' ),
			'support_phone'  => $order->get_meta( '_trackpulse_support_phone' ),
		);
	}

	public static function normalize_phone( $phone, $default_country_code ) {
		$phone = preg_replace( '/[^0-9+]/', '', (string) $phone );
		if ( '' === $phone ) {
			return '';
		}

		if ( 0 === strpos( $phone, '+' ) ) {
			return $phone;
		}

		$phone = ltrim( $phone, '0' );
		if ( '' === $default_country_code ) {
			return $phone;
		}

		return '+' . preg_replace( '/[^0-9]/', '', $default_country_code ) . $phone;
	}

	public static function get_order_items_list( WC_Order $order ) {
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$items[] = $item->get_name();
		}
		return implode( ', ', $items );
	}

	public static function get_tracking_url_from_courier( $courier_id, $tracking_number ) {
		$couriers = get_option( 'trackpulse_couriers', array() );
		foreach ( $couriers as $courier ) {
			if ( $courier_id === $courier['id'] ) {
				$template = isset( $courier['template'] ) ? $courier['template'] : '';
				if ( $template && $tracking_number ) {
					return str_replace( '{tracking_number}', rawurlencode( $tracking_number ), $template );
				}
			}
		}
		return '';
	}
}
