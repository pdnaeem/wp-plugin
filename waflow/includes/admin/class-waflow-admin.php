<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WAFlow_Admin {
	private $crm;

	public function __construct( WAFlow_CRM $crm ) {
		$this->crm = $crm;
	}

	public function register_menu() {
		add_menu_page(
			__( 'WAFlow', 'waflow' ),
			__( 'WAFlow', 'waflow' ),
			'manage_options',
			'waflow',
			array( $this, 'render_inbox' ),
			'dashicons-whatsapp',
			26
		);

		add_submenu_page( 'waflow', __( 'Inbox / Leads', 'waflow' ), __( 'Inbox / Leads', 'waflow' ), 'manage_options', 'waflow', array( $this, 'render_inbox' ) );
		add_submenu_page( 'waflow', __( 'Contacts', 'waflow' ), __( 'Contacts', 'waflow' ), 'manage_options', 'waflow-contacts', array( $this, 'render_contacts' ) );
		add_submenu_page( 'waflow', __( 'Deals', 'waflow' ), __( 'Deals', 'waflow' ), 'manage_options', 'waflow-deals', array( $this, 'render_deals' ) );
		add_submenu_page( 'waflow', __( 'Automations', 'waflow' ), __( 'Automations', 'waflow' ), 'manage_options', 'waflow-automations', array( $this, 'render_automations' ) );
		add_submenu_page( 'waflow', __( 'Analytics', 'waflow' ), __( 'Analytics', 'waflow' ), 'manage_options', 'waflow-analytics', array( $this, 'render_analytics' ) );
		add_submenu_page( 'waflow', __( 'Settings', 'waflow' ), __( 'Settings', 'waflow' ), 'manage_options', 'waflow-settings', array( $this, 'render_settings' ) );
	}

	public function handle_settings() {
		if ( empty( $_POST['waflow_settings_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_settings_nonce'] ) ), 'waflow_save_settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = array(
			'enabled' => isset( $_POST['enabled'] ),
			'phone' => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
			'prefill' => sanitize_textarea_field( wp_unslash( $_POST['prefill'] ?? '' ) ),
			'position' => sanitize_text_field( wp_unslash( $_POST['position'] ?? 'right' ) ),
			'color' => sanitize_hex_color( wp_unslash( $_POST['color'] ?? '#25d366' ) ),
			'greeting' => sanitize_text_field( wp_unslash( $_POST['greeting'] ?? '' ) ),
			'show_pre_chat' => isset( $_POST['show_pre_chat'] ),
			'consent_label' => sanitize_text_field( wp_unslash( $_POST['consent_label'] ?? '' ) ),
			'delete_on_uninstall' => isset( $_POST['delete_on_uninstall'] ),
		);

		update_option( 'waflow_settings', $settings );
	}

	public function render_inbox() {
		$contacts = $this->crm->get_contacts( 20 );
		$this->render_header( __( 'Inbox / Leads', 'waflow' ) );
		?>
		<div class="waflow-card">
			<p><?php esc_html_e( 'Latest leads captured from your widget and forms.', 'waflow' ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Phone', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Email', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Source', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Last Activity', 'waflow' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $contacts ) ) : ?>
						<tr>
							<td colspan="5"><?php esc_html_e( 'No leads yet.', 'waflow' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $contacts as $contact ) : ?>
							<tr>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-contacts&contact_id=' . absint( $contact->id ) ) ); ?>">
										<?php echo esc_html( $contact->name ); ?>
									</a>
								</td>
								<td><?php echo esc_html( $contact->phone ); ?></td>
								<td><?php echo esc_html( $contact->email ); ?></td>
								<td><?php echo esc_html( $contact->source ); ?></td>
								<td><?php echo esc_html( $contact->last_activity ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
		$this->render_footer();
	}

	public function render_contacts() {
		$contact_id = isset( $_GET['contact_id'] ) ? absint( $_GET['contact_id'] ) : 0;

		if ( $contact_id ) {
			$contact = $this->crm->get_contact( $contact_id );
			$activities = $this->crm->get_activities( $contact_id );
			$this->render_header( __( 'Contact Profile', 'waflow' ) );
			if ( ! $contact ) {
				?><p><?php esc_html_e( 'Contact not found.', 'waflow' ); ?></p><?php
				$this->render_footer();
				return;
			}
			?>
			<div class="waflow-grid">
				<div class="waflow-card">
					<h2><?php echo esc_html( $contact->name ); ?></h2>
					<p><strong><?php esc_html_e( 'Phone:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->phone ); ?></p>
					<p><strong><?php esc_html_e( 'Email:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->email ); ?></p>
					<p><strong><?php esc_html_e( 'Source:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->source ); ?></p>
					<p><strong><?php esc_html_e( 'First seen:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->first_seen ); ?></p>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'Timeline', 'waflow' ); ?></h3>
					<ul class="waflow-timeline">
						<?php if ( empty( $activities ) ) : ?>
							<li><?php esc_html_e( 'No activity yet.', 'waflow' ); ?></li>
						<?php else : ?>
							<?php foreach ( $activities as $activity ) : ?>
								<li>
									<strong><?php echo esc_html( $activity->activity_type ); ?></strong>
									<span><?php echo esc_html( $activity->created_at ); ?></span>
									<p><?php echo esc_html( $activity->payload ); ?></p>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
			</div>
			<?php
			$this->render_footer();
			return;
		}

		$contacts = $this->crm->get_contacts( 50 );
		$this->render_header( __( 'Contacts', 'waflow' ) );
		?>
		<div class="waflow-card">
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Phone', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Email', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Source', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Last Activity', 'waflow' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $contacts ) ) : ?>
						<tr>
							<td colspan="5"><?php esc_html_e( 'No contacts yet.', 'waflow' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $contacts as $contact ) : ?>
							<tr>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-contacts&contact_id=' . absint( $contact->id ) ) ); ?>">
										<?php echo esc_html( $contact->name ); ?>
									</a>
								</td>
								<td><?php echo esc_html( $contact->phone ); ?></td>
								<td><?php echo esc_html( $contact->email ); ?></td>
								<td><?php echo esc_html( $contact->source ); ?></td>
								<td><?php echo esc_html( $contact->last_activity ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
		$this->render_footer();
	}

	public function render_deals() {
		$this->render_header( __( 'Deals', 'waflow' ) );
		?>
		<div class="waflow-card">
			<p><?php esc_html_e( 'Deals pipeline will appear here in a future update.', 'waflow' ); ?></p>
		</div>
		<?php
		$this->render_footer();
	}

	public function render_automations() {
		$this->render_header( __( 'Automations', 'waflow' ) );
		?>
		<div class="waflow-card">
			<p><?php esc_html_e( 'Automation builder is coming soon. Configure triggers and actions in the next release.', 'waflow' ); ?></p>
		</div>
		<?php
		$this->render_footer();
	}

	public function render_analytics() {
		$this->render_header( __( 'Analytics', 'waflow' ) );
		?>
		<div class="waflow-card">
			<p><?php esc_html_e( 'Analytics dashboard will display lead sources and WhatsApp clicks.', 'waflow' ); ?></p>
		</div>
		<?php
		$this->render_footer();
	}

	public function render_settings() {
		$settings = get_option( 'waflow_settings', array() );
		$this->render_header( __( 'Settings', 'waflow' ) );
		?>
		<form method="post" class="waflow-card">
			<?php wp_nonce_field( 'waflow_save_settings', 'waflow_settings_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Widget', 'waflow' ); ?></th>
					<td><label><input type="checkbox" name="enabled" <?php checked( ! empty( $settings['enabled'] ) ); ?> /> <?php esc_html_e( 'Show the floating WhatsApp widget.', 'waflow' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'WhatsApp Number', 'waflow' ); ?></th>
					<td><input type="text" name="phone" value="<?php echo esc_attr( $settings['phone'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Prefill Message', 'waflow' ); ?></th>
					<td><textarea name="prefill" rows="3" class="large-text"><?php echo esc_textarea( $settings['prefill'] ?? '' ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Greeting', 'waflow' ); ?></th>
					<td><input type="text" name="greeting" value="<?php echo esc_attr( $settings['greeting'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Position', 'waflow' ); ?></th>
					<td>
						<select name="position">
							<option value="right" <?php selected( $settings['position'] ?? 'right', 'right' ); ?>><?php esc_html_e( 'Right', 'waflow' ); ?></option>
							<option value="left" <?php selected( $settings['position'] ?? 'right', 'left' ); ?>><?php esc_html_e( 'Left', 'waflow' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Button Color', 'waflow' ); ?></th>
					<td><input type="text" name="color" value="<?php echo esc_attr( $settings['color'] ?? '#25d366' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Pre-chat Form', 'waflow' ); ?></th>
					<td><label><input type="checkbox" name="show_pre_chat" <?php checked( ! empty( $settings['show_pre_chat'] ) ); ?> /> <?php esc_html_e( 'Ask for name and email before chat.', 'waflow' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Consent Label', 'waflow' ); ?></th>
					<td><input type="text" name="consent_label" value="<?php echo esc_attr( $settings['consent_label'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Delete Data on Uninstall', 'waflow' ); ?></th>
					<td><label><input type="checkbox" name="delete_on_uninstall" <?php checked( ! empty( $settings['delete_on_uninstall'] ) ); ?> /> <?php esc_html_e( 'Remove plugin data when uninstalling.', 'waflow' ); ?></label></td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Settings', 'waflow' ) ); ?>
		</form>
		<?php
		$this->render_footer();
	}

	private function render_header( $title ) {
		wp_enqueue_style( 'waflow-admin', WAFLOW_PLUGIN_URL . 'assets/css/admin.css', array(), WAFLOW_VERSION );
		?>
		<div class="wrap waflow-admin">
			<h1><?php echo esc_html( $title ); ?></h1>
			<p class="description"><?php esc_html_e( 'WAFlow helps you capture WhatsApp leads and manage conversations.', 'waflow' ); ?></p>
		<?php
	}

	private function render_footer() {
		?>
		</div>
		<?php
	}
}
