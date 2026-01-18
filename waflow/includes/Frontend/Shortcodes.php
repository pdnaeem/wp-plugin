<?php

namespace WAFlow\Frontend;

use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {
	public function register() {
		add_shortcode( 'waflow_whatsapp_button', array( $this, 'render_button' ) );
	}

	public function render_button( $atts ) {
		$atts = shortcode_atts(
			array(
				'phone' => '',
				'text' => __( 'Chat on WhatsApp', 'waflow' ),
				'prefill' => '',
				'agent' => '',
			),
			$atts,
			'waflow_whatsapp_button'
		);

		$settings = Helpers::get_settings();
		$widget = $settings['widget'];

		$phone = $atts['phone'] ? $atts['phone'] : $widget['phone'];
		if ( $atts['agent'] ) {
			$agent_phone = $this->resolve_agent_phone( $atts['agent'], $widget['agents'] );
			if ( $agent_phone ) {
				$phone = $agent_phone;
			}
		}

		if ( empty( $phone ) ) {
			return '';
		}

		$prefill = $atts['prefill'] ? $atts['prefill'] : $widget['prefill'];
		$link = Helpers::build_whatsapp_link( $phone, Helpers::interpolate_message( $prefill, array() ) );

		wp_enqueue_style( 'waflow-widget' );
		wp_enqueue_script( 'waflow-widget' );
		wp_localize_script(
			'waflow-widget',
			'waflowWidget',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'waflow_widget_click' ),
			)
		);

		return sprintf(
			'<a class="waflow-button" href="%1$s" target="_blank" rel="noopener noreferrer" data-waflow-direct>%2$s</a>',
			esc_url( $link ),
			esc_html( $atts['text'] )
		);
	}

	private function resolve_agent_phone( $agent_id, $agents ) {
		if ( empty( $agents ) ) {
			return '';
		}

		foreach ( $agents as $agent ) {
			if ( (string) $agent_id === (string) ( $agent['user_id'] ?? '' ) ) {
				return $agent['phone'];
			}
		}

		return '';
	}
}
