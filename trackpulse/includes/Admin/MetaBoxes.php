<?php
namespace TrackPulse\Admin;

use TrackPulse\Capabilities;
use TrackPulse\Helpers;
use TrackPulse\WhatsApp\Sender;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MetaBoxes {
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
		add_action( 'save_post_shop_order', array( $this, 'save_meta' ), 10, 2 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_meta_hpos' ), 10, 2 );
	}

	public function register_meta_box() {
		$settings = Helpers::get_settings();
		if ( empty( $settings['tracking']['enable_panel'] ) ) {
			return;
		}

		add_meta_box(
			'trackpulse_shipment_tracking',
			__( 'Shipment Tracking', 'trackpulse' ),
			array( $this, 'render_meta_box' ),
			'shop_order',
			'side',
			'high'
		);
	}

	public function render_meta_box( $post ) {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		$order = wc_get_order( $post->ID );
		if ( ! $order ) {
			return;
		}

		$meta = Helpers::get_order_tracking_meta( $order );
		$couriers = get_option( 'trackpulse_couriers', array() );

		wp_nonce_field( 'trackpulse_save_meta', 'trackpulse_meta_nonce' );
		?>
		<p>
			<label for="trackpulse_courier"><?php esc_html_e( 'Courier', 'trackpulse' ); ?></label><br>
			<select id="trackpulse_courier" name="trackpulse_meta[courier_id]">
				<option value=""><?php esc_html_e( 'Select courier', 'trackpulse' ); ?></option>
				<?php foreach ( $couriers as $courier ) : ?>
					<?php if ( empty( $courier['active'] ) ) : continue; endif; ?>
					<option value="<?php echo esc_attr( $courier['id'] ); ?>" <?php selected( $meta['courier_id'], $courier['id'] ); ?>><?php echo esc_html( $courier['name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="trackpulse_tracking_number"><?php esc_html_e( 'Tracking number', 'trackpulse' ); ?></label><br>
			<input type="text" id="trackpulse_tracking_number" name="trackpulse_meta[tracking_number]" value="<?php echo esc_attr( $meta['tracking_number'] ); ?>" class="widefat">
		</p>
		<p>
			<label for="trackpulse_tracking_url"><?php esc_html_e( 'Tracking URL', 'trackpulse' ); ?></label><br>
			<input type="url" id="trackpulse_tracking_url" name="trackpulse_meta[tracking_url]" value="<?php echo esc_url( $meta['tracking_url'] ); ?>" class="widefat">
		</p>
		<p>
			<label for="trackpulse_shipped_date"><?php esc_html_e( 'Shipped date', 'trackpulse' ); ?></label><br>
			<input type="date" id="trackpulse_shipped_date" name="trackpulse_meta[shipped_date]" value="<?php echo esc_attr( $meta['shipped_date'] ); ?>" class="widefat">
		</p>
		<p>
			<label for="trackpulse_delivery_status"><?php esc_html_e( 'Delivery status', 'trackpulse' ); ?></label><br>
			<select id="trackpulse_delivery_status" name="trackpulse_meta[delivery_status]" class="widefat">
				<?php foreach ( array( 'pending', 'shipped', 'in_transit', 'delivered' ) as $status ) : ?>
					<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $meta['delivery_status'], $status ); ?>><?php echo esc_html( ucwords( str_replace( '_', ' ', $status ) ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="trackpulse_support_phone"><?php esc_html_e( 'Support WhatsApp number', 'trackpulse' ); ?></label><br>
			<input type="text" id="trackpulse_support_phone" name="trackpulse_meta[support_phone]" value="<?php echo esc_attr( $meta['support_phone'] ); ?>" class="widefat">
		</p>
		<?php
	}

	public function save_meta( $post_id, $post ) {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		if ( ! isset( $_POST['trackpulse_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['trackpulse_meta_nonce'] ), 'trackpulse_save_meta' ) ) {
			return;
		}

		if ( 'shop_order' !== $post->post_type ) {
			return;
		}

		$order = wc_get_order( $post_id );
		if ( ! $order ) {
			return;
		}

		$this->save_order_meta_fields( $order );
	}

	public function save_meta_hpos( $order_id, $order ) {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		if ( ! isset( $_POST['trackpulse_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['trackpulse_meta_nonce'] ), 'trackpulse_save_meta' ) ) {
			return;
		}

		if ( ! $order ) {
			$order = wc_get_order( $order_id );
		}

		if ( ! $order ) {
			return;
		}

		$this->save_order_meta_fields( $order );
	}

	private function save_order_meta_fields( $order ) {
		$meta = isset( $_POST['trackpulse_meta'] ) ? wp_unslash( $_POST['trackpulse_meta'] ) : array();
		$courier_id = isset( $meta['courier_id'] ) ? sanitize_text_field( $meta['courier_id'] ) : '';
		$tracking_number = isset( $meta['tracking_number'] ) ? sanitize_text_field( $meta['tracking_number'] ) : '';
		$tracking_url = isset( $meta['tracking_url'] ) ? esc_url_raw( $meta['tracking_url'] ) : '';
		$shipped_date = isset( $meta['shipped_date'] ) ? sanitize_text_field( $meta['shipped_date'] ) : '';
		$delivery_status = isset( $meta['delivery_status'] ) ? sanitize_text_field( $meta['delivery_status'] ) : 'pending';
		$support_phone = isset( $meta['support_phone'] ) ? sanitize_text_field( $meta['support_phone'] ) : '';

		$couriers = get_option( 'trackpulse_couriers', array() );
		$courier_name = '';
		foreach ( $couriers as $courier ) {
			if ( $courier_id === $courier['id'] ) {
				$courier_name = $courier['name'];
				if ( ! $tracking_url && ! empty( $courier['template'] ) ) {
					$tracking_url = Helpers::get_tracking_url_from_courier( $courier_id, $tracking_number );
				}
				break;
			}
		}

		$order->update_meta_data( '_trackpulse_courier_id', $courier_id );
		$order->update_meta_data( '_trackpulse_courier_name', $courier_name );
		$order->update_meta_data( '_trackpulse_tracking_number', $tracking_number );
		$order->update_meta_data( '_trackpulse_tracking_url', $tracking_url );
		$order->update_meta_data( '_trackpulse_shipped_date', $shipped_date );
		$order->update_meta_data( '_trackpulse_delivery_status', $delivery_status );
		$order->update_meta_data( '_trackpulse_support_phone', $support_phone );
		$order->save();

		if ( $tracking_number ) {
			$order->add_order_note( __( 'Shipment tracking updated via TrackPulse.', 'trackpulse' ) );
			do_action( 'trackpulse_tracking_updated', $order->get_id() );
		}
	}
}
