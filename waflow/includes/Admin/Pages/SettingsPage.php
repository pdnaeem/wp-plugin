<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsPage {
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		$this->handle_save();

		$settings = Helpers::get_settings();
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'widget';

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'WAFlow Settings', 'waflow' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-settings&tab=widget' ) ); ?>" class="nav-tab <?php echo 'widget' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Widget', 'waflow' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-settings&tab=crm' ) ); ?>" class="nav-tab <?php echo 'crm' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'CRM', 'waflow' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-settings&tab=integrations' ) ); ?>" class="nav-tab <?php echo 'integrations' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Integrations', 'waflow' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-settings&tab=cloud' ) ); ?>" class="nav-tab <?php echo 'cloud' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Cloud API', 'waflow' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-settings&tab=advanced' ) ); ?>" class="nav-tab <?php echo 'advanced' === $tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Advanced', 'waflow' ); ?></a>
			</h2>

			<form method="post">
				<?php wp_nonce_field( 'waflow_save_settings', 'waflow_settings_nonce' ); ?>
				<input type="hidden" name="waflow_settings_tab" value="<?php echo esc_attr( $tab ); ?>" />
				<?php $this->render_tab( $tab, $settings ); ?>
				<?php submit_button( __( 'Save Settings', 'waflow' ) ); ?>
			</form>
		</div>
		<?php
	}

	private function render_tab( $tab, $settings ) {
		switch ( $tab ) {
			case 'crm':
				$this->render_crm_tab( $settings );
				break;
			case 'integrations':
				$this->render_integrations_tab( $settings );
				break;
			case 'cloud':
				$this->render_cloud_tab( $settings );
				break;
			case 'advanced':
				$this->render_advanced_tab( $settings );
				break;
			case 'widget':
			default:
				$this->render_widget_tab( $settings );
				break;
		}
	}

	private function render_widget_tab( $settings ) {
		$widget = $settings['widget'];
		?>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Enable Widget', 'waflow' ); ?></th>
				<td><label><input type="checkbox" name="widget_enabled" <?php checked( $widget['enabled'] ); ?> /> <?php esc_html_e( 'Enable the floating widget.', 'waflow' ); ?></label></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Mode', 'waflow' ); ?></th>
				<td>
					<select name="widget_mode">
						<option value="single" <?php selected( $widget['mode'], 'single' ); ?>><?php esc_html_e( 'Single Agent', 'waflow' ); ?></option>
						<option value="multi" <?php selected( $widget['mode'], 'multi' ); ?>><?php esc_html_e( 'Multi-Agent', 'waflow' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Phone Number', 'waflow' ); ?></th>
				<td><input type="text" name="widget_phone" value="<?php echo esc_attr( $widget['phone'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button Text', 'waflow' ); ?></th>
				<td><input type="text" name="widget_button_text" value="<?php echo esc_attr( $widget['button_text'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Position', 'waflow' ); ?></th>
				<td>
					<select name="widget_position">
						<option value="right" <?php selected( $widget['position'], 'right' ); ?>><?php esc_html_e( 'Right', 'waflow' ); ?></option>
						<option value="left" <?php selected( $widget['position'], 'left' ); ?>><?php esc_html_e( 'Left', 'waflow' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button Color', 'waflow' ); ?></th>
				<td><input type="text" name="widget_color" value="<?php echo esc_attr( $widget['color'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Greeting', 'waflow' ); ?></th>
				<td><input type="text" name="widget_greeting" value="<?php echo esc_attr( $widget['greeting'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Time-based Greetings', 'waflow' ); ?></th>
				<td>
					<input type="text" name="widget_greeting_morning" value="<?php echo esc_attr( $widget['greeting_morning'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Morning', 'waflow' ); ?>" />
					<input type="text" name="widget_greeting_afternoon" value="<?php echo esc_attr( $widget['greeting_afternoon'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Afternoon', 'waflow' ); ?>" />
					<input type="text" name="widget_greeting_evening" value="<?php echo esc_attr( $widget['greeting_evening'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Evening', 'waflow' ); ?>" />
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Offline Message', 'waflow' ); ?></th>
				<td><input type="text" name="widget_offline_message" value="<?php echo esc_attr( $widget['offline_message'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Prefill Template', 'waflow' ); ?></th>
				<td><textarea name="widget_prefill" class="large-text" rows="3"><?php echo esc_textarea( $widget['prefill'] ); ?></textarea></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Pre-chat Form', 'waflow' ); ?></th>
				<td><label><input type="checkbox" name="widget_prechat" <?php checked( $widget['prechat_enabled'] ); ?> /> <?php esc_html_e( 'Enable pre-chat lead capture form.', 'waflow' ); ?></label></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Consent Label', 'waflow' ); ?></th>
				<td><input type="text" name="widget_consent_label" value="<?php echo esc_attr( $widget['consent_label'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Business Hours', 'waflow' ); ?></th>
				<td>
					<label><?php esc_html_e( 'Timezone', 'waflow' ); ?> <input type="text" name="business_timezone" value="<?php echo esc_attr( $widget['business_hours']['timezone'] ); ?>" class="regular-text" /></label>
					<?php foreach ( array( 'mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun' ) as $day => $label ) : ?>
						<p>
							<label><input type="checkbox" name="business_<?php echo esc_attr( $day ); ?>_enabled" <?php checked( $widget['business_hours'][ $day ]['enabled'] ); ?> /> <?php echo esc_html( $label ); ?></label>
							<input type="text" name="business_<?php echo esc_attr( $day ); ?>_open" value="<?php echo esc_attr( $widget['business_hours'][ $day ]['open'] ); ?>" class="small-text" />
							<input type="text" name="business_<?php echo esc_attr( $day ); ?>_close" value="<?php echo esc_attr( $widget['business_hours'][ $day ]['close'] ); ?>" class="small-text" />
						</p>
					<?php endforeach; ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Display Rules', 'waflow' ); ?></th>
				<td>
					<p><label><?php esc_html_e( 'Include IDs', 'waflow' ); ?> <input type="text" name="display_include" value="<?php echo esc_attr( implode( ',', $widget['display']['include_ids'] ) ); ?>" class="regular-text" /></label></p>
					<p><label><?php esc_html_e( 'Exclude IDs', 'waflow' ); ?> <input type="text" name="display_exclude" value="<?php echo esc_attr( implode( ',', $widget['display']['exclude_ids'] ) ); ?>" class="regular-text" /></label></p>
					<p><label><?php esc_html_e( 'Post Types', 'waflow' ); ?> <input type="text" name="display_post_types" value="<?php echo esc_attr( implode( ',', $widget['display']['post_types'] ) ); ?>" class="regular-text" /></label></p>
					<p><label><?php esc_html_e( 'Categories', 'waflow' ); ?> <input type="text" name="display_categories" value="<?php echo esc_attr( implode( ',', $widget['display']['categories'] ) ); ?>" class="regular-text" /></label></p>
					<label><input type="checkbox" name="display_mobile" <?php checked( $widget['display']['mobile'] ); ?> /> <?php esc_html_e( 'Show on mobile', 'waflow' ); ?></label>
					<label><input type="checkbox" name="display_desktop" <?php checked( $widget['display']['desktop'] ); ?> /> <?php esc_html_e( 'Show on desktop', 'waflow' ); ?></label>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Agents (JSON)', 'waflow' ); ?></th>
				<td>
					<textarea name="widget_agents" rows="6" class="large-text code"><?php echo esc_textarea( wp_json_encode( $widget['agents'], JSON_PRETTY_PRINT ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Provide agents array with name, title, phone, avatar, user_id, prefill.', 'waflow' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	private function render_crm_tab( $settings ) {
		$crm = $settings['crm'];
		?>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Enable Deals', 'waflow' ); ?></th>
				<td><input type="checkbox" name="crm_deals_enabled" <?php checked( $crm['deals_enabled'] ); ?> /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Pipeline Stages', 'waflow' ); ?></th>
				<td><input type="text" name="crm_pipeline_stages" value="<?php echo esc_attr( implode( ',', $crm['pipeline_stages'] ) ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Data Retention (days)', 'waflow' ); ?></th>
				<td><input type="number" name="crm_retention" value="<?php echo esc_attr( $crm['data_retention_days'] ); ?>" min="0" /></td>
			</tr>
		</table>
		<?php
	}

	private function render_integrations_tab( $settings ) {
		$integrations = $settings['integrations'];
		?>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'WooCommerce', 'waflow' ); ?></th>
				<td><input type="checkbox" name="integration_woocommerce" <?php checked( $integrations['woocommerce'] ); ?> /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'WPForms', 'waflow' ); ?></th>
				<td><input type="checkbox" name="integration_wpforms" <?php checked( $integrations['wpforms'] ); ?> /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Fluent Forms', 'waflow' ); ?></th>
				<td><input type="checkbox" name="integration_fluentforms" <?php checked( $integrations['fluentforms'] ); ?> /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'WPForms Mapping', 'waflow' ); ?></th>
				<td>
					<textarea name="wpforms_map" rows="4" class="large-text code"><?php echo esc_textarea( wp_json_encode( $integrations['wpforms_map'], JSON_PRETTY_PRINT ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Map form_id to field keys (name, email, phone, message) and tags.', 'waflow' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Fluent Forms Mapping', 'waflow' ); ?></th>
				<td>
					<textarea name="fluentforms_map" rows="4" class="large-text code"><?php echo esc_textarea( wp_json_encode( $integrations['fluentforms_map'], JSON_PRETTY_PRINT ) ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Map form_id to field keys (name, email, phone, message) and tags.', 'waflow' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	private function render_cloud_tab( $settings ) {
		$cloud = $settings['cloud_api'];
		?>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Phone Number ID', 'waflow' ); ?></th>
				<td><input type="text" name="cloud_phone_number_id" value="<?php echo esc_attr( $cloud['phone_number_id'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Access Token', 'waflow' ); ?></th>
				<td><input type="text" name="cloud_access_token" value="<?php echo esc_attr( $cloud['access_token'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Business Account ID', 'waflow' ); ?></th>
				<td><input type="text" name="cloud_business_account_id" value="<?php echo esc_attr( $cloud['business_account_id'] ); ?>" class="regular-text" /></td>
			</tr>
		</table>
		<?php
	}

	private function render_advanced_tab( $settings ) {
		$advanced = $settings['advanced'];
		?>
		<table class="form-table">
			<tr>
				<th><?php esc_html_e( 'Delete Data on Uninstall', 'waflow' ); ?></th>
				<td><input type="checkbox" name="advanced_delete" <?php checked( $advanced['delete_on_uninstall'] ); ?> /></td>
			</tr>
		</table>
		<?php
	}

	private function handle_save() {
		if ( empty( $_POST['waflow_settings_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_settings_nonce'] ) ), 'waflow_save_settings' ) ) {
			return;
		}

		$settings = Helpers::get_settings();
		$tab = sanitize_text_field( wp_unslash( $_POST['waflow_settings_tab'] ?? 'widget' ) );

		if ( 'widget' === $tab ) {
			$settings['widget'] = array(
				'enabled' => isset( $_POST['widget_enabled'] ),
				'mode' => sanitize_text_field( wp_unslash( $_POST['widget_mode'] ?? 'single' ) ),
				'phone' => sanitize_text_field( wp_unslash( $_POST['widget_phone'] ?? '' ) ),
				'button_text' => sanitize_text_field( wp_unslash( $_POST['widget_button_text'] ?? '' ) ),
				'position' => sanitize_text_field( wp_unslash( $_POST['widget_position'] ?? 'right' ) ),
				'color' => Helpers::sanitize_hex( wp_unslash( $_POST['widget_color'] ?? '' ) ),
				'greeting' => sanitize_text_field( wp_unslash( $_POST['widget_greeting'] ?? '' ) ),
				'greeting_morning' => sanitize_text_field( wp_unslash( $_POST['widget_greeting_morning'] ?? '' ) ),
				'greeting_afternoon' => sanitize_text_field( wp_unslash( $_POST['widget_greeting_afternoon'] ?? '' ) ),
				'greeting_evening' => sanitize_text_field( wp_unslash( $_POST['widget_greeting_evening'] ?? '' ) ),
				'offline_message' => sanitize_text_field( wp_unslash( $_POST['widget_offline_message'] ?? '' ) ),
				'prefill' => sanitize_textarea_field( wp_unslash( $_POST['widget_prefill'] ?? '' ) ),
				'prechat_enabled' => isset( $_POST['widget_prechat'] ),
				'consent_label' => sanitize_text_field( wp_unslash( $_POST['widget_consent_label'] ?? '' ) ),
				'business_hours' => $this->sanitize_business_hours(),
				'display' => array(
					'include_ids' => array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['display_include'] ?? '' ) ) ) ) ),
					'exclude_ids' => array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['display_exclude'] ?? '' ) ) ) ) ),
					'post_types' => Helpers::sanitize_array_text( explode( ',', sanitize_text_field( wp_unslash( $_POST['display_post_types'] ?? '' ) ) ) ),
					'categories' => array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['display_categories'] ?? '' ) ) ) ) ),
					'mobile' => isset( $_POST['display_mobile'] ),
					'desktop' => isset( $_POST['display_desktop'] ),
				),
				'agents' => Helpers::sanitize_agents( $_POST['widget_agents'] ?? '' ),
			);
		}

		if ( 'crm' === $tab ) {
			$settings['crm'] = array(
				'deals_enabled' => isset( $_POST['crm_deals_enabled'] ),
				'pipeline_stages' => Helpers::sanitize_array_text( explode( ',', sanitize_text_field( wp_unslash( $_POST['crm_pipeline_stages'] ?? '' ) ) ) ),
				'data_retention_days' => absint( $_POST['crm_retention'] ?? 0 ),
			);
		}

		if ( 'integrations' === $tab ) {
			$settings['integrations'] = array(
				'woocommerce' => isset( $_POST['integration_woocommerce'] ),
				'wpforms' => isset( $_POST['integration_wpforms'] ),
				'fluentforms' => isset( $_POST['integration_fluentforms'] ),
				'wpforms_map' => $this->sanitize_mapping( $_POST['wpforms_map'] ?? '' ),
				'fluentforms_map' => $this->sanitize_mapping( $_POST['fluentforms_map'] ?? '' ),
			);
		}

		if ( 'cloud' === $tab ) {
			$settings['cloud_api'] = array(
				'phone_number_id' => sanitize_text_field( wp_unslash( $_POST['cloud_phone_number_id'] ?? '' ) ),
				'access_token' => sanitize_text_field( wp_unslash( $_POST['cloud_access_token'] ?? '' ) ),
				'business_account_id' => sanitize_text_field( wp_unslash( $_POST['cloud_business_account_id'] ?? '' ) ),
			);
		}

		if ( 'advanced' === $tab ) {
			$settings['advanced'] = array(
				'delete_on_uninstall' => isset( $_POST['advanced_delete'] ),
			);
		}

		Helpers::update_settings( $settings );
	}

	private function sanitize_business_hours() {
		$hours = array(
			'timezone' => sanitize_text_field( wp_unslash( $_POST['business_timezone'] ?? wp_timezone_string() ) ),
		);

		foreach ( array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' ) as $day ) {
			$hours[ $day ] = array(
				'enabled' => isset( $_POST[ 'business_' . $day . '_enabled' ] ),
				'open' => sanitize_text_field( wp_unslash( $_POST[ 'business_' . $day . '_open' ] ?? '09:00' ) ),
				'close' => sanitize_text_field( wp_unslash( $_POST[ 'business_' . $day . '_close' ] ?? '18:00' ) ),
			);
		}

		return $hours;
	}

	private function sanitize_mapping( $raw ) {
		$decoded = json_decode( wp_unslash( $raw ), true );
		return is_array( $decoded ) ? $decoded : array();
	}
}
