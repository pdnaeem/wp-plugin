<?php

namespace WAFlow\Integrations;

use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WhatsAppCloud {
	public function send_message( $phone, $message ) {
		$settings = Helpers::get_settings();
		$cloud = $settings['cloud_api'];

		if ( empty( $cloud['access_token'] ) || empty( $cloud['phone_number_id'] ) ) {
			return array(
				'success' => false,
				'error' => __( 'Cloud API not configured.', 'waflow' ),
			);
		}

		$endpoint = sprintf( 'https://graph.facebook.com/v19.0/%s/messages', $cloud['phone_number_id'] );
		$payload = array(
			'messaging_product' => 'whatsapp',
			'to' => preg_replace( '/[^\d]/', '', $phone ),
			'type' => 'text',
			'text' => array(
				'body' => $message,
			),
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $cloud['access_token'],
					'Content-Type' => 'application/json',
				),
				'body' => wp_json_encode( $payload ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'error' => $response->get_error_message(),
			);
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code >= 200 && $code < 300 ) {
			return array(
				'success' => true,
				'response' => $body,
			);
		}

		return array(
			'success' => false,
			'error' => $body['error']['message'] ?? __( 'Cloud API error.', 'waflow' ),
			'response' => $body,
		);
	}
}
