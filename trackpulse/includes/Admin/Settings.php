<?php
namespace TrackPulse\Admin;

use TrackPulse\Capabilities;
use TrackPulse\Helpers;
use TrackPulse\WhatsApp\TemplateEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {
	public function init() {
		add_action( 'admin_post_trackpulse_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_post_trackpulse_reset_templates', array( $this, 'reset_templates' ) );
		add_action( 'admin_post_trackpulse_test_send', array( $this, 'test_send' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets( $hook ) {
		$is_trackpulse = false !== strpos( $hook, 'trackpulse' );
		$is_order_screen = in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && 'shop_order' === get_post_type();
		if ( ! $is_trackpulse && ! $is_order_screen ) {
			return;
		}

		wp_enqueue_style( 'trackpulse-admin', TRACKPULSE_PLUGIN_URL . 'assets/css/admin.css', array(), TRACKPULSE_VERSION );
		wp_enqueue_script( 'trackpulse-admin', TRACKPULSE_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), TRACKPULSE_VERSION, true );
	}

	public static function render_page() {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		$settings = Helpers::get_settings();
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
		$tabs = array(
			'general'  => __( 'General', 'trackpulse' ),
			'tracking' => __( 'Tracking', 'trackpulse' ),
			'whatsapp' => __( 'WhatsApp', 'trackpulse' ),
			'cloud'    => __( 'Cloud API', 'trackpulse' ),
			'advanced' => __( 'Advanced', 'trackpulse' ),
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'TrackPulse Settings', 'trackpulse' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $key => $label ) : ?>
					<a class="nav-tab <?php echo $tab === $key ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=trackpulse-settings&tab=' . $key ) ); ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				<?php endforeach; ?>
			</h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'trackpulse_save_settings', 'trackpulse_nonce' ); ?>
				<input type="hidden" name="action" value="trackpulse_save_settings">
				<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>">
				<?php self::render_tab( $tab, $settings ); ?>
				<?php submit_button(); ?>
			</form>
			<?php if ( 'whatsapp' === $tab ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="trackpulse-inline-form">
				<?php wp_nonce_field( 'trackpulse_reset_templates', 'trackpulse_reset_nonce' ); ?>
				<input type="hidden" name="action" value="trackpulse_reset_templates">
				<?php submit_button( __( 'Reset to default templates', 'trackpulse' ), 'secondary' ); ?>
			</form>
			<?php endif; ?>
			<?php if ( 'cloud' === $tab ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="trackpulse-inline-form">
				<?php wp_nonce_field( 'trackpulse_test_send', 'trackpulse_test_nonce' ); ?>
				<input type="hidden" name="action" value="trackpulse_test_send">
				<h2><?php esc_html_e( 'Send Test WhatsApp', 'trackpulse' ); ?></h2>
				<p>
					<label for="trackpulse_test_phone"><?php esc_html_e( 'Phone Number', 'trackpulse' ); ?></label>
					<input type="text" id="trackpulse_test_phone" name="test_phone" class="regular-text">
				</p>
				<p>
					<label for="trackpulse_test_event"><?php esc_html_e( 'Event Template', 'trackpulse' ); ?></label>
					<select id="trackpulse_test_event" name="test_event">
						<?php foreach ( array_keys( Helpers::default_templates() ) as $event ) : ?>
							<option value="<?php echo esc_attr( $event ); ?>"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $event ) ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<?php submit_button( __( 'Send Test', 'trackpulse' ), 'secondary' ); ?>
			</form>
			<?php endif; ?>
		</div>
		<?php
	}

	private static function render_tab( $tab, $settings ) {
		switch ( $tab ) {
			case 'tracking':
				self::render_tracking_tab( $settings );
				break;
			case 'whatsapp':
				self::render_whatsapp_tab( $settings );
				break;
			case 'cloud':
				self::render_cloud_tab( $settings );
				break;
			case 'advanced':
				self::render_advanced_tab( $settings );
				break;
			default:
				self::render_general_tab( $settings );
		}
	}

	private static function render_general_tab( $settings ) {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable plugin', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[general][enabled]" value="1" <?php checked( $settings['general']['enabled'] ); ?>></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Default country code', 'trackpulse' ); ?></th>
				<td><input type="text" name="settings[general][default_country_code]" value="<?php echo esc_attr( $settings['general']['default_country_code'] ); ?>" class="small-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delete data on uninstall', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[general][delete_on_uninstall]" value="1" <?php checked( $settings['general']['delete_on_uninstall'] ); ?>></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable custom statuses', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[statuses][enable_custom_statuses]" value="1" <?php checked( $settings['statuses']['enable_custom_statuses'] ); ?>></td>
			</tr>
		</table>
		<?php
	}

	private static function render_tracking_tab( $settings ) {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable shipment panel', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[tracking][enable_panel]" value="1" <?php checked( $settings['tracking']['enable_panel'] ); ?>></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Show items', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[tracking][show_items]" value="1" <?php checked( $settings['tracking']['show_items'] ); ?>></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Show totals', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[tracking][show_totals]" value="1" <?php checked( $settings['tracking']['show_totals'] ); ?>></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Show support button', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[tracking][show_support_button]" value="1" <?php checked( $settings['tracking']['show_support_button'] ); ?>></td>
			</tr>
		</table>
		<?php
	}

	private static function render_whatsapp_tab( $settings ) {
		$templates = $settings['whatsapp']['templates'];
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable WhatsApp notifications', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[whatsapp][enabled]" value="1" <?php checked( $settings['whatsapp']['enabled'] ); ?>></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Mode', 'trackpulse' ); ?></th>
				<td>
					<select name="settings[whatsapp][mode]">
						<option value="click_to_chat" <?php selected( $settings['whatsapp']['mode'], 'click_to_chat' ); ?>><?php esc_html_e( 'Click-to-Chat', 'trackpulse' ); ?></option>
						<option value="cloud_api" <?php selected( $settings['whatsapp']['mode'], 'cloud_api' ); ?>><?php esc_html_e( 'WhatsApp Cloud API', 'trackpulse' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Support WhatsApp number', 'trackpulse' ); ?></th>
				<td><input type="text" name="settings[whatsapp][support_phone]" value="<?php echo esc_attr( $settings['whatsapp']['support_phone'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Admin notification phone', 'trackpulse' ); ?></th>
				<td><input type="text" name="settings[whatsapp][admin_phone]" value="<?php echo esc_attr( $settings['whatsapp']['admin_phone'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Events', 'trackpulse' ); ?></th>
				<td>
					<?php foreach ( $settings['whatsapp']['events'] as $event => $enabled ) : ?>
						<label><input type="checkbox" name="settings[whatsapp][events][<?php echo esc_attr( $event ); ?>]" value="1" <?php checked( $enabled ); ?>> <?php echo esc_html( ucfirst( str_replace( '_', ' ', $event ) ) ); ?></label><br>
					<?php endforeach; ?>
				</td>
			</tr>
		</table>
		<h2><?php esc_html_e( 'Templates', 'trackpulse' ); ?></h2>
		<?php foreach ( $templates as $event => $template ) : ?>
			<p>
				<label for="trackpulse_template_<?php echo esc_attr( $event ); ?>"><strong><?php echo esc_html( ucfirst( str_replace( '_', ' ', $event ) ) ); ?></strong></label><br>
				<textarea id="trackpulse_template_<?php echo esc_attr( $event ); ?>" name="settings[whatsapp][templates][<?php echo esc_attr( $event ); ?>]" rows="3" class="large-text code"><?php echo esc_textarea( $template ); ?></textarea>
			</p>
		<?php endforeach; ?>
		<?php
	}

	private static function render_cloud_tab( $settings ) {
		$token = $settings['cloud_api']['access_token'];
		$masked = $token ? str_repeat( '•', 8 ) : '';
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Phone Number ID', 'trackpulse' ); ?></th>
				<td><input type="text" name="settings[cloud_api][phone_number_id]" value="<?php echo esc_attr( $settings['cloud_api']['phone_number_id'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Access Token', 'trackpulse' ); ?></th>
				<td>
					<?php if ( $masked ) : ?>
						<p><?php echo esc_html( $masked ); ?></p>
						<p><label><?php esc_html_e( 'Change token', 'trackpulse' ); ?> <input type="text" name="settings[cloud_api][access_token]" value=""></label></p>
					<?php else : ?>
						<input type="text" name="settings[cloud_api][access_token]" value="">
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'API Version', 'trackpulse' ); ?></th>
				<td><input type="text" name="settings[cloud_api][api_version]" value="<?php echo esc_attr( $settings['cloud_api']['api_version'] ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Message mode', 'trackpulse' ); ?></th>
				<td>
					<select name="settings[cloud_api][message_mode]">
						<option value="text" <?php selected( $settings['cloud_api']['message_mode'], 'text' ); ?>><?php esc_html_e( 'Freeform text', 'trackpulse' ); ?></option>
						<option value="template" <?php selected( $settings['cloud_api']['message_mode'], 'template' ); ?>><?php esc_html_e( 'Template message', 'trackpulse' ); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}

	private static function render_advanced_tab( $settings ) {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Rate limit per minute', 'trackpulse' ); ?></th>
				<td><input type="number" name="settings[advanced][rate_limit_per_minute]" value="<?php echo esc_attr( $settings['advanced']['rate_limit_per_minute'] ); ?>" class="small-text"></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Debug logging', 'trackpulse' ); ?></th>
				<td><input type="checkbox" name="settings[advanced][debug_logging]" value="1" <?php checked( $settings['advanced']['debug_logging'] ); ?>></td>
			</tr>
		</table>
		<?php
	}

	public function save_settings() {
		if ( ! Capabilities::can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'trackpulse' ) );
		}

		check_admin_referer( 'trackpulse_save_settings', 'trackpulse_nonce' );

		$tab = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : 'general';
		$settings = Helpers::get_settings();
		$input = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : array();

		switch ( $tab ) {
			case 'tracking':
				$settings['tracking']['enable_panel'] = ! empty( $input['tracking']['enable_panel'] );
				$settings['tracking']['show_items'] = ! empty( $input['tracking']['show_items'] );
				$settings['tracking']['show_totals'] = ! empty( $input['tracking']['show_totals'] );
				$settings['tracking']['show_support_button'] = ! empty( $input['tracking']['show_support_button'] );
				break;
			case 'whatsapp':
				$settings['whatsapp']['enabled'] = ! empty( $input['whatsapp']['enabled'] );
				$settings['whatsapp']['mode'] = isset( $input['whatsapp']['mode'] ) ? sanitize_text_field( $input['whatsapp']['mode'] ) : 'click_to_chat';
				$settings['whatsapp']['support_phone'] = isset( $input['whatsapp']['support_phone'] ) ? sanitize_text_field( $input['whatsapp']['support_phone'] ) : '';
				$settings['whatsapp']['admin_phone'] = isset( $input['whatsapp']['admin_phone'] ) ? sanitize_text_field( $input['whatsapp']['admin_phone'] ) : '';
				$settings['whatsapp']['events'] = array();
				foreach ( Helpers::default_templates() as $event => $template ) {
					$settings['whatsapp']['events'][ $event ] = ! empty( $input['whatsapp']['events'][ $event ] );
					$settings['whatsapp']['templates'][ $event ] = isset( $input['whatsapp']['templates'][ $event ] ) ? wp_kses_post( $input['whatsapp']['templates'][ $event ] ) : $template;
				}
				break;
			case 'cloud':
				$settings['cloud_api']['phone_number_id'] = isset( $input['cloud_api']['phone_number_id'] ) ? sanitize_text_field( $input['cloud_api']['phone_number_id'] ) : '';
				$settings['cloud_api']['api_version'] = isset( $input['cloud_api']['api_version'] ) ? sanitize_text_field( $input['cloud_api']['api_version'] ) : 'v19.0';
				$settings['cloud_api']['message_mode'] = isset( $input['cloud_api']['message_mode'] ) ? sanitize_text_field( $input['cloud_api']['message_mode'] ) : 'text';
				if ( ! empty( $input['cloud_api']['access_token'] ) ) {
					$settings['cloud_api']['access_token'] = sanitize_text_field( $input['cloud_api']['access_token'] );
				}
				break;
			case 'advanced':
				$settings['advanced']['rate_limit_per_minute'] = isset( $input['advanced']['rate_limit_per_minute'] ) ? absint( $input['advanced']['rate_limit_per_minute'] ) : 10;
				$settings['advanced']['debug_logging'] = ! empty( $input['advanced']['debug_logging'] );
				break;
			default:
				$settings['general']['enabled'] = ! empty( $input['general']['enabled'] );
				$settings['general']['default_country_code'] = isset( $input['general']['default_country_code'] ) ? sanitize_text_field( $input['general']['default_country_code'] ) : '';
				$settings['general']['delete_on_uninstall'] = ! empty( $input['general']['delete_on_uninstall'] );
				$settings['statuses']['enable_custom_statuses'] = ! empty( $input['statuses']['enable_custom_statuses'] );
		}

		Helpers::update_settings( $settings );
		wp_safe_redirect( admin_url( 'admin.php?page=trackpulse-settings&tab=' . $tab . '&updated=1' ) );
		exit;
	}

	public function reset_templates() {
		if ( ! Capabilities::can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'trackpulse' ) );
		}
		check_admin_referer( 'trackpulse_reset_templates', 'trackpulse_reset_nonce' );

		$settings = Helpers::get_settings();
		$settings['whatsapp']['templates'] = Helpers::default_templates();
		Helpers::update_settings( $settings );

		wp_safe_redirect( admin_url( 'admin.php?page=trackpulse-settings&tab=whatsapp&reset=1' ) );
		exit;
	}

	public function test_send() {
		if ( ! Capabilities::can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'trackpulse' ) );
		}
		check_admin_referer( 'trackpulse_test_send', 'trackpulse_test_nonce' );

		$phone = isset( $_POST['test_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['test_phone'] ) ) : '';
		$event = isset( $_POST['test_event'] ) ? sanitize_text_field( wp_unslash( $_POST['test_event'] ) ) : 'order_placed';

		$engine = new TemplateEngine();
		$message = $engine->render_message( null, $event );

		do_action( 'trackpulse_send_whatsapp_manual', $phone, $message, $event );

		wp_safe_redirect( admin_url( 'admin.php?page=trackpulse-settings&tab=cloud&test=1' ) );
		exit;
	}
}
