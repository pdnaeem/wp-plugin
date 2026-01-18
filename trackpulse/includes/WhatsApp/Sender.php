<?php
namespace TrackPulse\WhatsApp;

use TrackPulse\Helpers;
use TrackPulse\Database\LogsRepository;
use TrackPulse\Database\QueueRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sender {
	public function __construct() {
		add_action( 'trackpulse_send_whatsapp_manual', array( $this, 'send_manual' ), 10, 3 );
	}

	public function queue_event( $order_id, $event, $args = array() ) {
		$settings = Helpers::get_settings();
		if ( empty( $settings['whatsapp']['enabled'] ) ) {
			return;
		}

		if ( empty( $settings['whatsapp']['events'][ $event ] ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$phone = $order->get_billing_phone();
		$phone = Helpers::normalize_phone( $phone, $settings['general']['default_country_code'] );
		if ( ! $phone ) {
			LogsRepository::insert( array(
				'order_id'  => $order_id,
				'channel'   => 'whatsapp',
				'event'     => $event,
				'recipient' => 'customer',
				'status'    => 'skipped',
				'message'   => 'Missing phone number',
			) );
			return;
		}

		$engine = new TemplateEngine();
		$message = $engine->render_message( $order, $event );

		if ( 'click_to_chat' === $settings['whatsapp']['mode'] ) {
			$this->send_click_to_chat( $order_id, $event, $phone, $message );
			return;
		}

		if ( 'cloud_api' === $settings['whatsapp']['mode'] ) {
			if ( empty( $settings['cloud_api']['phone_number_id'] ) || empty( $settings['cloud_api']['access_token'] ) ) {
				LogsRepository::insert( array(
					'order_id'  => $order_id,
					'channel'   => 'whatsapp',
					'event'     => $event,
					'recipient' => $phone,
					'status'    => 'skipped',
					'message'   => 'Cloud API not configured',
				) );
				return;
			}

			QueueRepository::enqueue( current_time( 'mysql' ), $order_id, $event, array(
				'phone'   => $phone,
				'message' => $message,
			) );

			LogsRepository::insert( array(
				'order_id'  => $order_id,
				'channel'   => 'whatsapp',
				'event'     => $event,
				'recipient' => $phone,
				'status'    => 'queued',
				'message'   => $message,
			) );
		}
	}

	private function send_click_to_chat( $order_id, $event, $phone, $message ) {
		$link = 'https://wa.me/' . rawurlencode( preg_replace( '/[^0-9+]/', '', $phone ) ) . '?text=' . rawurlencode( $message );
		LogsRepository::insert( array(
			'order_id'  => $order_id,
			'channel'   => 'whatsapp',
			'event'     => 'manual_send',
			'recipient' => $phone,
			'status'    => 'success',
			'message'   => $message,
			'response'  => $link,
		) );
	}

	public function process_queue() {
		$settings = Helpers::get_settings();
		$limit = absint( $settings['advanced']['rate_limit_per_minute'] );
		$jobs = QueueRepository::get_pending_jobs( $limit ? $limit : 10 );
		if ( ! $jobs ) {
			return;
		}

		$api = new CloudApi();
		foreach ( $jobs as $job ) {
			QueueRepository::mark_processing( $job['id'] );

			$payload = json_decode( $job['payload'], true );
			$phone = $payload['phone'] ?? '';
			$message = $payload['message'] ?? '';
			$response = $api->send_text( $phone, $message );
			$attempts = (int) $job['attempts'] + 1;

			if ( is_wp_error( $response ) ) {
				$this->handle_failure( $job, $attempts, $response->get_error_message() );
				continue;
			}

			$code = wp_remote_retrieve_response_code( $response );
			if ( $code >= 200 && $code < 300 ) {
				QueueRepository::mark_done( $job['id'] );
				LogsRepository::insert( array(
					'order_id'  => $job['order_id'],
					'channel'   => 'whatsapp',
					'event'     => $job['event'],
					'recipient' => $phone,
					'status'    => 'success',
					'message'   => $message,
					'response'  => wp_json_encode( $response ),
				) );
			} else {
				$error = wp_remote_retrieve_body( $response );
				$this->handle_failure( $job, $attempts, $error );
			}
		}
	}

	private function handle_failure( $job, $attempts, $error ) {
		if ( $attempts >= 3 ) {
			QueueRepository::mark_failed( $job['id'], $attempts, $error );
			LogsRepository::insert( array(
				'order_id'  => $job['order_id'],
				'channel'   => 'whatsapp',
				'event'     => $job['event'],
				'recipient' => 'customer',
				'status'    => 'fail',
				'message'   => 'Failed after retries',
				'response'  => $error,
			) );
			return;
		}

		$delay = $attempts === 1 ? 5 * MINUTE_IN_SECONDS : ( $attempts === 2 ? 30 * MINUTE_IN_SECONDS : 2 * HOUR_IN_SECONDS );
		$run_at = gmdate( 'Y-m-d H:i:s', time() + $delay );
		QueueRepository::reschedule( $job['id'], $attempts, $run_at, $error );
	}

	public function send_manual( $phone, $message, $event ) {
		$settings = Helpers::get_settings();
		$phone = Helpers::normalize_phone( $phone, $settings['general']['default_country_code'] );
		if ( ! $phone ) {
			LogsRepository::insert( array(
				'channel'   => 'whatsapp',
				'event'     => $event,
				'recipient' => 'manual',
				'status'    => 'skipped',
				'message'   => 'Missing phone number',
			) );
			return;
		}

		if ( 'cloud_api' === $settings['whatsapp']['mode'] ) {
			QueueRepository::enqueue( current_time( 'mysql' ), 0, $event, array(
				'phone'   => $phone,
				'message' => $message,
			) );
			return;
		}

		$this->send_click_to_chat( 0, $event, $phone, $message );
	}
}
