<?php

namespace WAFlow;

use DateTimeImmutable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Helpers {
	public static function default_settings() {
		return array(
			'widget' => array(
				'enabled' => true,
				'mode' => 'single',
				'phone' => '',
				'button_text' => __( 'Chat on WhatsApp', 'waflow' ),
				'position' => 'right',
				'color' => '#25d366',
				'greeting' => __( 'Hi! How can we help?', 'waflow' ),
				'greeting_morning' => __( 'Good morning! How can we help?', 'waflow' ),
				'greeting_afternoon' => __( 'Good afternoon! How can we help?', 'waflow' ),
				'greeting_evening' => __( 'Good evening! How can we help?', 'waflow' ),
				'offline_message' => __( 'We are offline. Leave a message and we will reply soon.', 'waflow' ),
				'prefill' => __( 'Hi! I visited {site_name} and need help with {page_title}.', 'waflow' ),
				'prechat_enabled' => true,
				'consent_label' => __( 'I agree to be contacted via WhatsApp.', 'waflow' ),
				'business_hours' => array(
					'timezone' => wp_timezone_string(),
					'mon' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
					'tue' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
					'wed' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
					'thu' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
					'fri' => array( 'enabled' => true, 'open' => '09:00', 'close' => '18:00' ),
					'sat' => array( 'enabled' => false, 'open' => '09:00', 'close' => '13:00' ),
					'sun' => array( 'enabled' => false, 'open' => '09:00', 'close' => '13:00' ),
				),
				'display' => array(
					'include_ids' => array(),
					'exclude_ids' => array(),
					'post_types' => array(),
					'categories' => array(),
					'mobile' => true,
					'desktop' => true,
				),
				'agents' => array(),
			),
			'crm' => array(
				'deals_enabled' => true,
				'pipeline_stages' => array( 'Lead', 'Qualified', 'Proposal', 'Won', 'Lost' ),
				'data_retention_days' => 0,
			),
			'integrations' => array(
				'woocommerce' => true,
				'wpforms' => true,
				'fluentforms' => true,
				'wpforms_map' => array(),
				'fluentforms_map' => array(),
			),
			'cloud_api' => array(
				'phone_number_id' => '',
				'access_token' => '',
				'business_account_id' => '',
			),
			'advanced' => array(
				'delete_on_uninstall' => false,
			),
		);
	}

	public static function get_settings() {
		$settings = get_option( 'waflow_settings', array() );

		return wp_parse_args( $settings, self::default_settings() );
	}

	public static function update_settings( $settings ) {
		$merged = wp_parse_args( $settings, self::default_settings() );
		update_option( 'waflow_settings', $merged );
	}

	public static function is_business_open( $settings ) {
		$hours = $settings['widget']['business_hours'] ?? array();
		if ( empty( $hours ) ) {
			return true;
		}

		$timezone = ! empty( $hours['timezone'] ) ? $hours['timezone'] : wp_timezone_string();
		$zone = new \DateTimeZone( $timezone );
		$now = new DateTimeImmutable( 'now', $zone );
		$day = strtolower( $now->format( 'D' ) );

		$day_hours = $hours[ $day ] ?? array();
		if ( empty( $day_hours['enabled'] ) ) {
			return false;
		}

		$open = $day_hours['open'] ?? '09:00';
		$close = $day_hours['close'] ?? '18:00';
		$open_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $now->format( 'Y-m-d' ) . ' ' . $open, $zone );
		$close_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $now->format( 'Y-m-d' ) . ' ' . $close, $zone );

		if ( ! $open_dt || ! $close_dt ) {
			return true;
		}

		return $now >= $open_dt && $now <= $close_dt;
	}

	public static function greeting_for_time( $settings ) {
		$timezone = wp_timezone();
		$now = new DateTimeImmutable( 'now', $timezone );
		$hour = (int) $now->format( 'H' );

		if ( $hour < 12 ) {
			return $settings['widget']['greeting_morning'] ?? $settings['widget']['greeting'];
		}

		if ( $hour < 17 ) {
			return $settings['widget']['greeting_afternoon'] ?? $settings['widget']['greeting'];
		}

		return $settings['widget']['greeting_evening'] ?? $settings['widget']['greeting'];
	}

	public static function sanitize_hex( $color ) {
		$color = sanitize_hex_color( $color );
		return $color ? $color : '#25d366';
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
				'avatar' => esc_url_raw( $agent['avatar'] ?? '' ),
				'user_id' => isset( $agent['user_id'] ) ? absint( $agent['user_id'] ) : 0,
				'prefill' => sanitize_text_field( $agent['prefill'] ?? '' ),
			);
		}

		return $agents;
	}

	public static function build_whatsapp_link( $phone, $message ) {
		$clean_phone = preg_replace( '/[^\d]/', '', $phone );
		return sprintf( 'https://wa.me/%1$s?text=%2$s', $clean_phone, rawurlencode( $message ) );
	}

	public static function interpolate_message( $message, $context ) {
		$variables = array_merge(
			array(
				'site_name' => get_bloginfo( 'name' ),
				'page_title' => is_singular() ? get_the_title() : '',
				'page_url' => is_singular() ? get_permalink() : home_url(),
			),
			$context
		);

		$variables = apply_filters( 'waflow_message_variables', $variables, $context );

		foreach ( $variables as $key => $value ) {
			$message = str_replace( '{' . $key . '}', $value, $message );
		}

		return $message;
	}

	public static function sanitize_array_text( $values ) {
		$clean = array();
		foreach ( (array) $values as $value ) {
			$value = sanitize_text_field( wp_unslash( $value ) );
			if ( '' !== $value ) {
				$clean[] = $value;
			}
		}
		return array_values( array_unique( $clean ) );
	}
}
