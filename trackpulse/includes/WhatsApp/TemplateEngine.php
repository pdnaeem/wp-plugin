<?php
namespace TrackPulse\WhatsApp;

use TrackPulse\Helpers;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TemplateEngine {
	public function render_message( $order, $event ) {
		$settings = Helpers::get_settings();
		$template = $settings['whatsapp']['templates'][ $event ] ?? '';

		if ( ! $order instanceof WC_Order ) {
			$replacements = $this->get_default_replacements();
		} else {
			$replacements = $this->get_order_replacements( $order );
		}

		$message = strtr( $template, $replacements );
		return wp_strip_all_tags( $message );
	}

	private function get_default_replacements() {
		return array(
			'{site_name}'          => get_bloginfo( 'name' ),
			'{order_id}'           => '',
			'{order_number}'       => '',
			'{order_status}'       => '',
			'{order_total}'        => '',
			'{currency}'           => '',
			'{billing_first_name}' => '',
			'{billing_last_name}'  => '',
			'{billing_phone}'      => '',
			'{billing_email}'      => '',
			'{shipping_city}'      => '',
			'{shipping_country}'   => '',
			'{items}'              => '',
			'{tracking_number}'    => '',
			'{courier_name}'       => '',
			'{tracking_url}'       => '',
			'{shipped_date}'       => '',
			'{estimated_delivery}' => '',
			'{support_phone}'      => '',
			'{order_view_url}'     => '',
		);
	}

	private function get_order_replacements( WC_Order $order ) {
		$meta = Helpers::get_order_tracking_meta( $order );
		$settings = Helpers::get_settings();
		$support_phone = $meta['support_phone'] ? $meta['support_phone'] : $settings['whatsapp']['support_phone'];

		$view_url = '';
		if ( is_user_logged_in() ) {
			$view_url = $order->get_view_order_url();
		}

		return array(
			'{site_name}'          => get_bloginfo( 'name' ),
			'{order_id}'           => (string) $order->get_id(),
			'{order_number}'       => $order->get_order_number(),
			'{order_status}'       => wc_get_order_status_name( $order->get_status() ),
			'{order_total}'        => wc_format_decimal( $order->get_total(), 2 ),
			'{currency}'           => $order->get_currency(),
			'{billing_first_name}' => $order->get_billing_first_name(),
			'{billing_last_name}'  => $order->get_billing_last_name(),
			'{billing_phone}'      => $order->get_billing_phone(),
			'{billing_email}'      => $order->get_billing_email(),
			'{shipping_city}'      => $order->get_shipping_city(),
			'{shipping_country}'   => $order->get_shipping_country(),
			'{items}'              => Helpers::get_order_items_list( $order ),
			'{tracking_number}'    => $meta['tracking_number'],
			'{courier_name}'       => $meta['courier_name'],
			'{tracking_url}'       => $meta['tracking_url'],
			'{shipped_date}'       => $meta['shipped_date'],
			'{estimated_delivery}' => '',
			'{support_phone}'      => $support_phone,
			'{order_view_url}'     => $view_url,
		);
	}
}
