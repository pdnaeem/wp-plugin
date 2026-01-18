<?php
namespace TrackPulse\Woo;

use TrackPulse\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EmailHooks {
	public function init() {
		add_action( 'woocommerce_email_after_order_table', array( $this, 'add_tracking_to_email' ), 10, 4 );
	}

	public function add_tracking_to_email( $order, $sent_to_admin, $plain_text, $email ) {
		if ( $sent_to_admin ) {
			return;
		}

		$meta = Helpers::get_order_tracking_meta( $order );
		if ( empty( $meta['tracking_number'] ) ) {
			return;
		}

		$tracking_url = $meta['tracking_url'];
		if ( $plain_text ) {
			echo "\n" . esc_html__( 'Tracking Number:', 'trackpulse' ) . ' ' . esc_html( $meta['tracking_number'] );
			if ( $tracking_url ) {
				echo "\n" . esc_html__( 'Tracking URL:', 'trackpulse' ) . ' ' . esc_url( $tracking_url );
			}
			return;
		}
		?>
		<h3><?php esc_html_e( 'Shipment Tracking', 'trackpulse' ); ?></h3>
		<p><?php esc_html_e( 'Tracking Number:', 'trackpulse' ); ?> <?php echo esc_html( $meta['tracking_number'] ); ?></p>
		<?php if ( $tracking_url ) : ?>
			<p><a href="<?php echo esc_url( $tracking_url ); ?>"><?php esc_html_e( 'Track your shipment', 'trackpulse' ); ?></a></p>
		<?php endif; ?>
		<?php
	}
}
