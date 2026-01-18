<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_Settings {
	public static function defaults() {
		return array(
			'enabled' => true,
			'phone' => '',
			'prefill' => __( 'Hi! I need help.', 'waflow' ),
			'position' => 'right',
			'color' => '#25d366',
			'greeting' => __( 'Hi! How can we help?', 'waflow' ),
			'greeting_morning' => __( 'Good morning! How can we help?', 'waflow' ),
			'greeting_afternoon' => __( 'Good afternoon! How can we help?', 'waflow' ),
			'greeting_evening' => __( 'Good evening! How can we help?', 'waflow' ),
			'offline_message' => __( 'We are currently offline. Please leave a message and we will reply soon.', 'waflow' ),
			'show_pre_chat' => true,
			'consent_label' => __( 'I agree to be contacted via WhatsApp.', 'waflow' ),
			'display_include' => array(),
			'display_exclude' => array(),
			'business_hours' => array(
				'mon' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
				'tue' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
				'wed' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
				'thu' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
				'fri' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
				'sat' => array( 'enabled' => false, 'open' => '09:00', 'close' => '13:00' ),
				'sun' => array( 'enabled' => false, 'open' => '09:00', 'close' => '13:00' ),
			),
			'widget_mode' => 'single',
			'agents' => array(),
			'delete_on_uninstall' => false,
		);
	}

	public static function get_settings() {
		$defaults = self::defaults();
		$settings = get_option( 'waflow_settings', array() );

		return wp_parse_args( $settings, $defaults );
	}

	public static function sanitize_agents( $value ) {
		if ( empty( $value ) ) {
			return array();
		}

		$decoded = json_decode( wp_unslash( $value ), true );
		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$agents = array();
		foreach ( $decoded as $agent ) {
			if ( empty( $agent['name'] ) || empty( $agent['phone'] ) ) {
				continue;
			}

			$agents[] = array(
				'name' => sanitize_text_field( $agent['name'] ),
				'title' => sanitize_text_field( $agent['title'] ?? '' ),
				'phone' => sanitize_text_field( $agent['phone'] ),
				'department' => sanitize_text_field( $agent['department'] ?? '' ),
				'avatar' => esc_url_raw( $agent['avatar'] ?? '' ),
				'prefill' => sanitize_text_field( $agent['prefill'] ?? '' ),
				'schedule' => is_array( $agent['schedule'] ?? null ) ? $agent['schedule'] : array(),
			);
		}

		return $agents;
	}

	public static function is_business_hours( $settings ) {
		$hours = $settings['business_hours'] ?? array();
		if ( empty( $hours ) ) {
			return true;
		}

		$timezone = wp_timezone();
		$now = new DateTimeImmutable( 'now', $timezone );
		$day = strtolower( $now->format( 'D' ) );
		$map = array(
			'mon' => 'mon',
			'tue' => 'tue',
			'wed' => 'wed',
			'thu' => 'thu',
			'fri' => 'fri',
			'sat' => 'sat',
			'sun' => 'sun',
		);

		$key = $map[ $day ] ?? 'mon';
		$day_hours = $hours[ $key ] ?? array();
		if ( empty( $day_hours['enabled'] ) ) {
			return false;
		}

		$open = $day_hours['open'] ?? '09:00';
		$close = $day_hours['close'] ?? '18:00';

		$open_time = DateTimeImmutable::createFromFormat( 'H:i', $open, $timezone );
		$close_time = DateTimeImmutable::createFromFormat( 'H:i', $close, $timezone );
		if ( ! $open_time || ! $close_time ) {
			return true;
		}

		$today = $now->format( 'Y-m-d' );
		$open_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $today . ' ' . $open, $timezone );
		$close_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $today . ' ' . $close, $timezone );

		return $now >= $open_dt && $now <= $close_dt;
	}

	public static function greeting_for_time( $settings ) {
		$timezone = wp_timezone();
		$now = new DateTimeImmutable( 'now', $timezone );
		$hour = (int) $now->format( 'H' );

		if ( $hour < 12 ) {
			return $settings['greeting_morning'] ?? $settings['greeting'];
		}

		if ( $hour < 17 ) {
			return $settings['greeting_afternoon'] ?? $settings['greeting'];
		}

		return $settings['greeting_evening'] ?? $settings['greeting'];
	}
}
