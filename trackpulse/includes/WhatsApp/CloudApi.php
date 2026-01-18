<?php
namespace TrackPulse\WhatsApp;

use TrackPulse\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CloudApi {
	public function send_text( $phone, $message ) {
		$settings = Helpers::get_settings();
		$cloud = $settings['cloud_api'];

		if ( empty( $cloud['phone_number_id'] ) || empty( $cloud['access_token'] ) ) {
			return new \WP_Error( 'trackpulse_config', __( 'WhatsApp Cloud API is not configured.', 'trackpulse' ) );
		}

		$url = sprintf( 'https://graph.facebook.com/%s/%s/messages', $cloud['api_version'], $cloud['phone_number_id'] );

		$payload = array(
			'messaging_product' => 'whatsapp',
			'to'                => $phone,
			'type'              => 'text',
			'text'              => array( 'body' => $message ),
		);

		$response = wp_remote_post( $url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $cloud['access_token'],
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $payload ),
			'timeout' => 20,
		) );

		return $response;
	}
}
