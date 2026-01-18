<?php
namespace TrackPulse\Frontend;

use TrackPulse\Helpers;
use TrackPulse\WhatsApp\TemplateEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {
	public function init() {
		add_shortcode( 'trackpulse_track_order', array( $this, 'render_tracking_form' ) );
		add_shortcode( 'trackpulse_tracking_info', array( $this, 'render_tracking_info' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets() {
		global $post;
		if ( ! $post ) {
			return;
		}
		if ( has_shortcode( $post->post_content, 'trackpulse_track_order' ) || has_shortcode( $post->post_content, 'trackpulse_tracking_info' ) ) {
			wp_enqueue_style( 'trackpulse-frontend', TRACKPULSE_PLUGIN_URL . 'assets/css/frontend.css', array(), TRACKPULSE_VERSION );
			wp_enqueue_script( 'trackpulse-frontend', TRACKPULSE_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), TRACKPULSE_VERSION, true );
		}
	}

	public function render_tracking_form() {
		$order = null;
		$message = '';
		if ( isset( $_POST['trackpulse_lookup_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['trackpulse_lookup_nonce'] ), 'trackpulse_lookup' ) ) {
			$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
			$email = isset( $_POST['billing_email'] ) ? sanitize_email( wp_unslash( $_POST['billing_email'] ) ) : '';
			$order = wc_get_order( $order_id );
			if ( ! $order || strtolower( $order->get_billing_email() ) !== strtolower( $email ) ) {
				$message = __( 'Order not found. Please check your details.', 'trackpulse' );
				$order = null;
			}
		}

		ob_start();
		?>
		<div class="trackpulse-tracking">
			<form method="post" class="trackpulse-form">
				<?php wp_nonce_field( 'trackpulse_lookup', 'trackpulse_lookup_nonce' ); ?>
				<p>
					<label><?php esc_html_e( 'Order ID', 'trackpulse' ); ?></label>
					<input type="text" name="order_id" required>
				</p>
				<p>
					<label><?php esc_html_e( 'Billing Email', 'trackpulse' ); ?></label>
					<input type="email" name="billing_email" required>
				</p>
				<button type="submit" class="trackpulse-button"><?php esc_html_e( 'Track Order', 'trackpulse' ); ?></button>
			</form>
			<?php if ( $message ) : ?>
				<p class="trackpulse-notice"><?php echo esc_html( $message ); ?></p>
			<?php endif; ?>
			<?php if ( $order ) : ?>
				<?php echo $this->render_tracking_block( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_tracking_info( $atts ) {
		$atts = shortcode_atts( array( 'order_id' => 0 ), $atts, 'trackpulse_tracking_info' );
		$order_id = absint( $atts['order_id'] );
		$order = $order_id ? wc_get_order( $order_id ) : null;
		if ( ! $order ) {
			return '';
		}

		return $this->render_tracking_block( $order );
	}

	private function render_tracking_block( $order ) {
		$settings = Helpers::get_settings();
		$meta = Helpers::get_order_tracking_meta( $order );
		$support_phone = $meta['support_phone'] ? $meta['support_phone'] : $settings['whatsapp']['support_phone'];
		$tracking_url = $meta['tracking_url'];
		if ( $tracking_url ) {
			$tracking_link = sprintf( '<a class="trackpulse-button" href="%s" target="_blank" rel="noopener">%s</a>', esc_url( $tracking_url ), esc_html__( 'Track Shipment', 'trackpulse' ) );
		} else {
			$tracking_link = '';
		}

		$wa_link = '';
		if ( $settings['tracking']['show_support_button'] && $support_phone ) {
			$wa_link = sprintf( '<a class="trackpulse-button" href="%s" target="_blank" rel="noopener">%s</a>', esc_url( 'https://wa.me/' . rawurlencode( preg_replace( '/[^0-9+]/', '', $support_phone ) ) ), esc_html__( 'Contact Support', 'trackpulse' ) );
		}

		ob_start();
		?>
		<div class="trackpulse-result">
			<h3><?php esc_html_e( 'Order Status', 'trackpulse' ); ?>: <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></h3>
			<div class="trackpulse-shipment">
				<h4><?php esc_html_e( 'Shipment Tracking', 'trackpulse' ); ?></h4>
				<ul>
					<li><strong><?php esc_html_e( 'Courier:', 'trackpulse' ); ?></strong> <?php echo esc_html( $meta['courier_name'] ); ?></li>
					<li><strong><?php esc_html_e( 'Tracking number:', 'trackpulse' ); ?></strong> <?php echo esc_html( $meta['tracking_number'] ); ?></li>
					<li><strong><?php esc_html_e( 'Shipped date:', 'trackpulse' ); ?></strong> <?php echo esc_html( $meta['shipped_date'] ); ?></li>
					<li><strong><?php esc_html_e( 'Delivery status:', 'trackpulse' ); ?></strong> <?php echo esc_html( ucwords( str_replace( '_', ' ', $meta['delivery_status'] ) ) ); ?></li>
				</ul>
				<?php echo $tracking_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php if ( $settings['tracking']['show_items'] ) : ?>
				<h4><?php esc_html_e( 'Items', 'trackpulse' ); ?></h4>
				<ul>
					<?php foreach ( $order->get_items() as $item ) : ?>
						<li><?php echo esc_html( $item->get_name() ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( $wa_link ) : ?>
				<div class="trackpulse-support">
					<?php echo $wa_link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
