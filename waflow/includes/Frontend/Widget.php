<?php

namespace WAFlow\Frontend;

use WAFlow\Database\ContactsRepository;
use WAFlow\Database\ActivitiesRepository;
use WAFlow\Helpers;
use WAFlow\Automations\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Widget {
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'wp_footer', array( $this, 'render' ) );
		add_action( 'admin_post_nopriv_waflow_prechat_submit', array( $this, 'handle_prechat' ) );
		add_action( 'admin_post_waflow_prechat_submit', array( $this, 'handle_prechat' ) );
		add_action( 'wp_ajax_nopriv_waflow_widget_click', array( $this, 'log_click' ) );
		add_action( 'wp_ajax_waflow_widget_click', array( $this, 'log_click' ) );
	}

	public function register_assets() {
		wp_register_style( 'waflow-widget', WAFLOW_PLUGIN_URL . 'assets/css/widget.css', array(), WAFLOW_VERSION );
		wp_register_script( 'waflow-widget', WAFLOW_PLUGIN_URL . 'assets/js/widget.js', array(), WAFLOW_VERSION, true );
	}

	public function enqueue_assets() {
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
	}

	public function render() {
		$settings = Helpers::get_settings();
		$widget = $settings['widget'];

		if ( ! $widget['enabled'] ) {
			return;
		}

		if ( ! $this->passes_display_rules( $widget ) ) {
			return;
		}

		if ( empty( $widget['phone'] ) ) {
			return;
		}

		$this->enqueue_assets();

		$greeting = Helpers::greeting_for_time( $settings );
		$prefill = Helpers::interpolate_message( $widget['prefill'], array() );
		$is_open = Helpers::is_business_open( $settings );
		$position = 'left' === $widget['position'] ? 'left' : 'right';

		?>
		<div class="waflow-widget waflow-position-<?php echo esc_attr( $position ); ?>" data-waflow-widget>
			<button class="waflow-trigger" style="background: <?php echo esc_attr( $widget['color'] ); ?>" type="button">
				<span class="waflow-trigger-icon">💬</span>
				<span class="waflow-trigger-text"><?php echo esc_html( $widget['button_text'] ); ?></span>
			</button>
			<div class="waflow-panel" hidden>
				<?php if ( $greeting ) : ?>
					<p class="waflow-greeting"><?php echo esc_html( $greeting ); ?></p>
				<?php endif; ?>
				<?php if ( ! $is_open ) : ?>
					<p class="waflow-offline"><?php echo esc_html( $widget['offline_message'] ); ?></p>
				<?php endif; ?>
				<?php if ( 'multi' === $widget['mode'] && ! empty( $widget['agents'] ) ) : ?>
					<ul class="waflow-agent-list">
						<?php foreach ( $widget['agents'] as $agent ) : ?>
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
								<a class="waflow-agent-link" href="<?php echo esc_url( Helpers::build_whatsapp_link( $agent['phone'], $prefill ) ); ?>" target="_blank" rel="noopener noreferrer" data-waflow-direct>
									<?php esc_html_e( 'Chat', 'waflow' ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php elseif ( $widget['prechat_enabled'] ) : ?>
					<form class="waflow-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="waflow_prechat_submit" />
						<?php wp_nonce_field( 'waflow_prechat_submit', 'waflow_prechat_nonce' ); ?>
						<input type="hidden" name="prefill" value="<?php echo esc_attr( $widget['prefill'] ); ?>" />
						<label>
							<span><?php esc_html_e( 'Name', 'waflow' ); ?></span>
							<input type="text" name="name" required />
						</label>
						<label>
							<span><?php esc_html_e( 'Email', 'waflow' ); ?></span>
							<input type="email" name="email" />
						</label>
						<label>
							<span><?php esc_html_e( 'Phone', 'waflow' ); ?></span>
							<input type="text" name="phone" required />
						</label>
						<label>
							<span><?php esc_html_e( 'Message', 'waflow' ); ?></span>
							<textarea name="message" rows="3"></textarea>
						</label>
						<label class="waflow-consent">
							<input type="checkbox" name="consent" required />
							<span><?php echo esc_html( $widget['consent_label'] ); ?></span>
						</label>
						<button type="submit" class="waflow-submit" style="background: <?php echo esc_attr( $widget['color'] ); ?>">
							<?php esc_html_e( 'Start Chat', 'waflow' ); ?>
						</button>
					</form>
				<?php else : ?>
					<a class="waflow-direct" href="<?php echo esc_url( Helpers::build_whatsapp_link( $widget['phone'], $prefill ) ); ?>" target="_blank" rel="noopener noreferrer" data-waflow-direct>
						<?php esc_html_e( 'Open WhatsApp', 'waflow' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function handle_prechat() {
		if ( empty( $_POST['waflow_prechat_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_prechat_nonce'] ) ), 'waflow_prechat_submit' ) ) {
			wp_die( esc_html__( 'Invalid request.', 'waflow' ) );
		}

		if ( empty( $_POST['consent'] ) ) {
			wp_die( esc_html__( 'Consent is required.', 'waflow' ) );
		}

		$name = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
		$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
		$prefill_template = sanitize_textarea_field( wp_unslash( $_POST['prefill'] ?? '' ) );

		if ( empty( $name ) || empty( $phone ) ) {
			wp_die( esc_html__( 'Missing required fields.', 'waflow' ) );
		}

		$contacts = new ContactsRepository();
		$activities = new ActivitiesRepository();

		$contact_id = $contacts->find_by_phone_or_email( $phone, $email );
		$data = array(
			'name' => $name,
			'phone' => $phone,
			'email' => $email,
			'source' => 'prechat',
			'last_seen' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		if ( $contact_id ) {
			$contacts->update( $contact_id, $data );
		} else {
			$data['first_seen'] = current_time( 'mysql' );
			$data['created_at'] = current_time( 'mysql' );
			$contact_id = $contacts->create( $data );
		}

		$activities->add( $contact_id, 'form_submit', array( 'message' => $message ) );

		Engine::handle_trigger( 'prechat_submit', array( 'contact_id' => $contact_id, 'payload' => array( 'message' => $message ) ) );

		$settings = Helpers::get_settings();
		$prefill = Helpers::interpolate_message(
			$prefill_template,
			array(
				'name' => $name,
				'email' => $email,
				'phone' => $phone,
				'message' => $message,
			)
		);

		$link = Helpers::build_whatsapp_link( $settings['widget']['phone'], $prefill . ' ' . $message );
		wp_safe_redirect( $link );
		exit;
	}

	public function log_click() {
		check_ajax_referer( 'waflow_widget_click', 'nonce' );

		$payload = array(
			'page_title' => sanitize_text_field( wp_unslash( $_POST['page_title'] ?? '' ) ),
			'page_url' => esc_url_raw( wp_unslash( $_POST['page_url'] ?? '' ) ),
		);

		$activities = new ActivitiesRepository();
		$activities->add( 0, 'click', $payload );

		Engine::handle_trigger( 'widget_click', array( 'payload' => $payload ) );

		wp_send_json_success();
	}

	private function passes_display_rules( $widget ) {
		if ( wp_is_mobile() && empty( $widget['display']['mobile'] ) ) {
			return false;
		}

		if ( ! wp_is_mobile() && empty( $widget['display']['desktop'] ) ) {
			return false;
		}

		if ( is_singular() ) {
			$post_id = get_the_ID();
			if ( ! empty( $widget['display']['exclude_ids'] ) && in_array( $post_id, $widget['display']['exclude_ids'], true ) ) {
				return false;
			}
			if ( ! empty( $widget['display']['include_ids'] ) && ! in_array( $post_id, $widget['display']['include_ids'], true ) ) {
				return false;
			}

			if ( ! empty( $widget['display']['post_types'] ) && ! in_array( get_post_type( $post_id ), $widget['display']['post_types'], true ) ) {
				return false;
			}

			if ( ! empty( $widget['display']['categories'] ) ) {
				$terms = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'ids' ) );
				if ( empty( array_intersect( $widget['display']['categories'], $terms ) ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
