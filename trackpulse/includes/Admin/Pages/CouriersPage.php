<?php
namespace TrackPulse\Admin\Pages;

use TrackPulse\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CouriersPage {
	public function render() {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		$this->handle_actions();
		$couriers = get_option( 'trackpulse_couriers', array() );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Couriers', 'trackpulse' ); ?></h1>
			<form method="post">
				<?php wp_nonce_field( 'trackpulse_save_courier', 'trackpulse_courier_nonce' ); ?>
				<input type="hidden" name="trackpulse_action" value="save_courier">
				<h2><?php esc_html_e( 'Add Courier', 'trackpulse' ); ?></h2>
				<table class="form-table">
					<tr>
						<th><?php esc_html_e( 'Name', 'trackpulse' ); ?></th>
						<td><input type="text" name="courier[name]" class="regular-text" required></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Tracking URL template', 'trackpulse' ); ?></th>
						<td><input type="text" name="courier[template]" class="regular-text"></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Country', 'trackpulse' ); ?></th>
						<td><input type="text" name="courier[country]" class="regular-text"></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Active', 'trackpulse' ); ?></th>
						<td><input type="checkbox" name="courier[active]" value="1" checked></td>
					</tr>
				</table>
				<?php submit_button( __( 'Add Courier', 'trackpulse' ) ); ?>
			</form>
			<h2><?php esc_html_e( 'Existing Couriers', 'trackpulse' ); ?></h2>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'trackpulse' ); ?></th>
						<th><?php esc_html_e( 'Template', 'trackpulse' ); ?></th>
						<th><?php esc_html_e( 'Country', 'trackpulse' ); ?></th>
						<th><?php esc_html_e( 'Active', 'trackpulse' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'trackpulse' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( $couriers ) : ?>
						<?php foreach ( $couriers as $courier ) : ?>
							<tr>
								<td><?php echo esc_html( $courier['name'] ); ?></td>
								<td><?php echo esc_html( $courier['template'] ); ?></td>
								<td><?php echo esc_html( $courier['country'] ); ?></td>
								<td><?php echo ! empty( $courier['active'] ) ? esc_html__( 'Yes', 'trackpulse' ) : esc_html__( 'No', 'trackpulse' ); ?></td>
								<td>
									<form method="post" class="trackpulse-inline-form">
										<?php wp_nonce_field( 'trackpulse_delete_courier', 'trackpulse_delete_nonce' ); ?>
										<input type="hidden" name="trackpulse_action" value="delete_courier">
										<input type="hidden" name="courier_id" value="<?php echo esc_attr( $courier['id'] ); ?>">
										<?php submit_button( __( 'Delete', 'trackpulse' ), 'link-delete', '', false ); ?>
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr><td colspan="5"><?php esc_html_e( 'No couriers yet.', 'trackpulse' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function handle_actions() {
		if ( empty( $_POST['trackpulse_action'] ) ) {
			return;
		}

		if ( ! Capabilities::can_manage() ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_POST['trackpulse_action'] ) );
		$couriers = get_option( 'trackpulse_couriers', array() );

		if ( 'save_courier' === $action ) {
			check_admin_referer( 'trackpulse_save_courier', 'trackpulse_courier_nonce' );
			$data = isset( $_POST['courier'] ) ? wp_unslash( $_POST['courier'] ) : array();
			$couriers[] = array(
				'id'       => uniqid( 'tp_', true ),
				'name'     => sanitize_text_field( $data['name'] ?? '' ),
				'template' => sanitize_text_field( $data['template'] ?? '' ),
				'country'  => sanitize_text_field( $data['country'] ?? '' ),
				'active'   => ! empty( $data['active'] ),
			);
			update_option( 'trackpulse_couriers', $couriers, false );
		}

		if ( 'delete_courier' === $action ) {
			check_admin_referer( 'trackpulse_delete_courier', 'trackpulse_delete_nonce' );
			$courier_id = isset( $_POST['courier_id'] ) ? sanitize_text_field( wp_unslash( $_POST['courier_id'] ) ) : '';
			$couriers = array_values( array_filter( $couriers, function( $courier ) use ( $courier_id ) {
				return $courier['id'] !== $courier_id;
			} ) );
			update_option( 'trackpulse_couriers', $couriers, false );
		}
	}
}
