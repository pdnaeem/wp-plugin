<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WizardPage {
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		$this->handle_save();

		$settings = Helpers::get_settings();
		$widget = $settings['widget'];

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'WAFlow Setup Wizard', 'waflow' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Let’s get your WhatsApp widget ready.', 'waflow' ); ?></p>
			<form method="post">
				<?php wp_nonce_field( 'waflow_setup_wizard', 'waflow_setup_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th><?php esc_html_e( 'WhatsApp Number', 'waflow' ); ?></th>
						<td><input type="text" name="wizard_phone" value="<?php echo esc_attr( $widget['phone'] ); ?>" class="regular-text" required /></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Widget Mode', 'waflow' ); ?></th>
						<td>
							<select name="wizard_mode">
								<option value="single" <?php selected( $widget['mode'], 'single' ); ?>><?php esc_html_e( 'Single Agent', 'waflow' ); ?></option>
								<option value="multi" <?php selected( $widget['mode'], 'multi' ); ?>><?php esc_html_e( 'Multi-Agent', 'waflow' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Enable Integrations', 'waflow' ); ?></th>
						<td>
							<label><input type="checkbox" name="wizard_woo" <?php checked( $settings['integrations']['woocommerce'] ); ?> /> <?php esc_html_e( 'WooCommerce', 'waflow' ); ?></label><br />
							<label><input type="checkbox" name="wizard_wpforms" <?php checked( $settings['integrations']['wpforms'] ); ?> /> <?php esc_html_e( 'WPForms', 'waflow' ); ?></label><br />
							<label><input type="checkbox" name="wizard_fluent" <?php checked( $settings['integrations']['fluentforms'] ); ?> /> <?php esc_html_e( 'Fluent Forms', 'waflow' ); ?></label>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Finish Setup', 'waflow' ) ); ?>
			</form>
		</div>
		<?php
	}

	private function handle_save() {
		if ( empty( $_POST['waflow_setup_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_setup_nonce'] ) ), 'waflow_setup_wizard' ) ) {
			return;
		}

		$settings = Helpers::get_settings();
		$settings['widget']['phone'] = sanitize_text_field( wp_unslash( $_POST['wizard_phone'] ?? '' ) );
		$settings['widget']['mode'] = sanitize_text_field( wp_unslash( $_POST['wizard_mode'] ?? 'single' ) );
		$settings['integrations']['woocommerce'] = isset( $_POST['wizard_woo'] );
		$settings['integrations']['wpforms'] = isset( $_POST['wizard_wpforms'] );
		$settings['integrations']['fluentforms'] = isset( $_POST['wizard_fluent'] );

		Helpers::update_settings( $settings );
		update_option( 'waflow_setup_complete', 1 );
		wp_safe_redirect( admin_url( 'admin.php?page=waflow' ) );
		exit;
	}
}
