<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_Widget {
	private $crm;
	private $should_enqueue = false;

	public function __construct( WAFlow_CRM $crm ) {
		$this->crm = $crm;
	}

	public function register_assets() {
		wp_register_style( 'waflow-widget', WAFLOW_PLUGIN_URL . 'assets/css/widget.css', array(), WAFLOW_VERSION );
		wp_register_script( 'waflow-widget', WAFLOW_PLUGIN_URL . 'assets/js/widget.js', array(), WAFLOW_VERSION, true );
	}

	public function register_shortcodes() {
		add_shortcode( 'waflow_whatsapp_button', array( $this, 'render_button_shortcode' ) );
	}

	public function render_widget() {
		$settings = WAFlow_Settings::get_settings();

		if ( empty( $settings['enabled'] ) ) {
			return;
		}

		if ( ! $this->passes_display_rules( $settings ) ) {
			return;
		}

		$this->should_enqueue = true;
		$this->enqueue_assets();

		$phone = $settings['phone'] ?? '';
		if ( empty( $phone ) ) {
			return;
		}

		$position = ( $settings['position'] ?? 'right' ) === 'left' ? 'left' : 'right';
		$greeting = WAFlow_Settings::greeting_for_time( $settings );
		$prefill = $settings['prefill'] ?? '';
		$color = $settings['color'] ?? '#25d366';
		$show_pre_chat = ! empty( $settings['show_pre_chat'] );
		$is_open = WAFlow_Settings::is_business_hours( $settings );
		$widget_mode = $settings['widget_mode'] ?? 'single';
		$agents = $settings['agents'] ?? array();

		?>
		<div class="waflow-widget waflow-position-<?php echo esc_attr( $position ); ?>" data-waflow>
			<button class="waflow-trigger" style="background: <?php echo esc_attr( $color ); ?>">
				<span class="waflow-trigger-icon">💬</span>
				<span class="waflow-trigger-text"><?php esc_html_e( 'Chat on WhatsApp', 'waflow' ); ?></span>
			</button>
			<div class="waflow-panel" hidden>
				<?php if ( $greeting ) : ?>
					<p class="waflow-greeting"><?php echo esc_html( $greeting ); ?></p>
				<?php endif; ?>
				<?php if ( ! $is_open ) : ?>
					<p class="waflow-offline"><?php echo esc_html( $settings['offline_message'] ?? '' ); ?></p>
				<?php endif; ?>
				<?php if ( 'multi' === $widget_mode && ! empty( $agents ) ) : ?>
					<ul class="waflow-agent-list">
						<?php foreach ( $agents as $agent ) : ?>
							<li class="waflow-agent">
								<?php if ( ! empty( $agent['avatar'] ) ) : ?>
									<img src="<?php echo esc_url( $agent['avatar'] ); ?>" alt="<?php echo esc_attr( $agent['name'] ); ?>" />
								<?php endif; ?>
								<div class="waflow-agent-meta">
									<strong><?php echo esc_html( $agent['name'] ); ?></strong>
									<?php if ( ! empty( $agent['title'] ) ) : ?>
										<span><?php echo esc_html( $agent['title'] ); ?></span>
									<?php endif; ?>
								</div>
								<a class="waflow-agent-link" href="<?php echo esc_url( $this->build_whatsapp_link( $agent['phone'], $agent['prefill'] ?? $prefill ) ); ?>" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Chat', 'waflow' ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( $show_pre_chat ) : ?>
					<form class="waflow-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="waflow_capture_lead" />
						<?php wp_nonce_field( 'waflow_capture_lead', 'waflow_nonce' ); ?>
						<input type="hidden" name="phone" value="<?php echo esc_attr( $phone ); ?>" />
						<input type="hidden" name="prefill" value="<?php echo esc_attr( $prefill ); ?>" />
						<label>
							<span><?php esc_html_e( 'Name', 'waflow' ); ?></span>
							<input type="text" name="name" required />
						</label>
						<label>
							<span><?php esc_html_e( 'Email', 'waflow' ); ?></span>
							<input type="email" name="email" />
						</label>
						<label>
							<span><?php esc_html_e( 'Message', 'waflow' ); ?></span>
							<textarea name="message" rows="3"></textarea>
						</label>
						<label class="waflow-consent">
							<input type="checkbox" name="consent" required />
							<span><?php echo esc_html( $settings['consent_label'] ?? __( 'I agree to be contacted via WhatsApp.', 'waflow' ) ); ?></span>
						</label>
						<button type="submit" class="waflow-submit" style="background: <?php echo esc_attr( $color ); ?>">
							<?php esc_html_e( 'Start Chat', 'waflow' ); ?>
						</button>
					</form>
				<?php else : ?>
						<a class="waflow-direct" href="<?php echo esc_url( $this->build_whatsapp_link( $phone, $prefill ) ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Open WhatsApp', 'waflow' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function render_button_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'agent' => '',
				'text' => __( 'Chat Now', 'waflow' ),
				'prefill' => '',
				'phone' => '',
			),
			$atts,
			'waflow_whatsapp_button'
		);

		$settings = WAFlow_Settings::get_settings();
		$phone = $atts['phone'] ? $atts['phone'] : ( $settings['phone'] ?? '' );
		$prefill = $atts['prefill'] ? $atts['prefill'] : ( $settings['prefill'] ?? '' );

		if ( empty( $phone ) ) {
			return '';
		}

		$this->should_enqueue = true;
		$this->enqueue_assets();

		$link = $this->build_whatsapp_link( $phone, $prefill );

		return sprintf(
			'<a class="waflow-button" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $link ),
			esc_html( $atts['text'] )
		);
	}

	public function handle_lead_capture() {
		if ( empty( $_POST['waflow_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_nonce'] ) ), 'waflow_capture_lead' ) ) {
			wp_die( esc_html__( 'Invalid request.', 'waflow' ) );
		}

		if ( empty( $_POST['consent'] ) ) {
			wp_die( esc_html__( 'Consent is required.', 'waflow' ) );
		}

		$name = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
		$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
		$prefill = sanitize_textarea_field( wp_unslash( $_POST['prefill'] ?? '' ) );

		if ( empty( $phone ) || empty( $name ) ) {
			wp_die( esc_html__( 'Missing required fields.', 'waflow' ) );
		}

		$contact_id = $this->crm->find_contact_by_phone_or_email( $phone, $email );
		$data = array(
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'source' => 'widget',
			'notes' => $message,
			'last_activity' => current_time( 'mysql' ),
		);

		if ( $contact_id ) {
			$this->crm->update_contact( $contact_id, $data );
		} else {
			$contact_id = $this->crm->create_contact( $data );
		}

		$this->crm->add_activity(
			$contact_id,
			'pre_chat_form',
			$message
		);

		$prefill_message = trim( $prefill . ' ' . $message );
		$link = $this->build_whatsapp_link(
			$phone,
			$prefill_message,
			array(
				'name' => $name,
				'email' => $email,
				'message' => $message,
			)
		);

		wp_safe_redirect( $link );
		exit;
	}

	private function build_whatsapp_link( $phone, $message, $context = array() ) {
		$clean_phone = preg_replace( '/[^\d]/', '', $phone );
		$parsed = $this->parse_template_message( $message, $context );
		$encoded_message = rawurlencode( $parsed );

		return sprintf( 'https://wa.me/%1$s?text=%2$s', $clean_phone, $encoded_message );
	}

	private function enqueue_assets() {
		wp_enqueue_style( 'waflow-widget' );
		wp_enqueue_script( 'waflow-widget' );
	}

	private function parse_template_message( $message, $context ) {
		$variables = array_merge(
			array(
				'site_name' => get_bloginfo( 'name' ),
			),
			$context
		);

		$variables = apply_filters( 'waflow_message_variables', $variables, $context );

		foreach ( $variables as $key => $value ) {
			$message = str_replace( '{' . $key . '}', $value, $message );
		}

		return $message;
	}

	private function passes_display_rules( $settings ) {
		if ( is_admin() ) {
			return false;
		}

		$include = $settings['display_include'] ?? array();
		$exclude = $settings['display_exclude'] ?? array();

		if ( is_singular() ) {
			$post_id = get_the_ID();
			if ( ! empty( $exclude ) && in_array( $post_id, $exclude, true ) ) {
				return false;
			}
			if ( ! empty( $include ) && ! in_array( $post_id, $include, true ) ) {
				return false;
			}
		}

		return true;
	}
}
