<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Database\ContactsRepository;
use WAFlow\Database\ActivitiesRepository;
use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactViewPage {
	public function render() {
		if ( ! current_user_can( 'manage_waflow_crm' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		$contact_id = isset( $_GET['contact_id'] ) ? absint( $_GET['contact_id'] ) : 0;
		if ( ! $contact_id ) {
			wp_die( esc_html__( 'Missing contact.', 'waflow' ) );
		}

		$repo = new ContactsRepository();
		$activities_repo = new ActivitiesRepository();

		$contact = $repo->get( $contact_id );
		if ( ! $contact ) {
			wp_die( esc_html__( 'Contact not found.', 'waflow' ) );
		}

		$this->handle_actions( $contact_id, $repo, $activities_repo );

		$contact = $repo->get( $contact_id );
		$activities = $activities_repo->list_for_contact( $contact_id );

		$tags = Helpers::sanitize_array_text( explode( ',', $contact->tags ) );

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'Contact Profile', 'waflow' ); ?></h1>
			<div class="waflow-grid">
				<div class="waflow-card">
					<h2><?php echo esc_html( $contact->name ); ?></h2>
					<p><strong><?php esc_html_e( 'Phone:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->phone ); ?></p>
					<p><strong><?php esc_html_e( 'Email:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->email ); ?></p>
					<p><strong><?php esc_html_e( 'Source:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->source ); ?></p>
					<p><strong><?php esc_html_e( 'First Seen:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->first_seen ); ?></p>
					<p><strong><?php esc_html_e( 'Last Seen:', 'waflow' ); ?></strong> <?php echo esc_html( $contact->last_seen ); ?></p>
					<form method="post">
						<?php wp_nonce_field( 'waflow_contact_update', 'waflow_contact_nonce' ); ?>
						<p>
							<label><?php esc_html_e( 'Assign User', 'waflow' ); ?></label>
							<?php wp_dropdown_users( array( 'name' => 'assigned_user_id', 'selected' => (int) $contact->assigned_user_id, 'show_option_none' => __( 'Unassigned', 'waflow' ) ) ); ?>
						</p>
						<p>
							<label><?php esc_html_e( 'Tags (comma separated)', 'waflow' ); ?></label>
							<input type="text" name="tags" value="<?php echo esc_attr( implode( ',', $tags ) ); ?>" class="regular-text" />
						</p>
						<?php submit_button( __( 'Update Contact', 'waflow' ), 'secondary', 'waflow_update_contact' ); ?>
					</form>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'Add Note', 'waflow' ); ?></h3>
					<form method="post">
						<?php wp_nonce_field( 'waflow_contact_note', 'waflow_note_nonce' ); ?>
						<textarea name="note" rows="4" class="large-text"></textarea>
						<?php submit_button( __( 'Add Note', 'waflow' ), 'primary', 'waflow_add_note' ); ?>
					</form>
				</div>
			</div>

			<div class="waflow-card">
				<h3><?php esc_html_e( 'Activity Timeline', 'waflow' ); ?></h3>
				<ul class="waflow-timeline">
					<?php if ( empty( $activities ) ) : ?>
						<li><?php esc_html_e( 'No activity yet.', 'waflow' ); ?></li>
					<?php else : ?>
						<?php foreach ( $activities as $activity ) : ?>
							<li>
								<strong><?php echo esc_html( $activity->type ); ?></strong>
								<span><?php echo esc_html( $activity->created_at ); ?></span>
								<p><?php echo esc_html( $activity->payload ); ?></p>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
		<?php
	}

	private function handle_actions( $contact_id, ContactsRepository $repo, ActivitiesRepository $activities ) {
		if ( isset( $_POST['waflow_update_contact'] ) ) {
			if ( empty( $_POST['waflow_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_contact_nonce'] ) ), 'waflow_contact_update' ) ) {
				return;
			}

			$assigned_user = isset( $_POST['assigned_user_id'] ) ? absint( $_POST['assigned_user_id'] ) : 0;
			$tags = sanitize_text_field( wp_unslash( $_POST['tags'] ?? '' ) );

			$repo->update(
				$contact_id,
				array(
					'assigned_user_id' => $assigned_user,
					'tags' => $tags,
					'updated_at' => current_time( 'mysql' ),
				)
			);

			$activities->add( $contact_id, 'assigned', array( 'user_id' => $assigned_user ) );
		}

		if ( isset( $_POST['waflow_add_note'] ) ) {
			if ( empty( $_POST['waflow_note_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_note_nonce'] ) ), 'waflow_contact_note' ) ) {
				return;
			}

			$note = sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) );
			if ( $note ) {
				$activities->add( $contact_id, 'note', array( 'note' => $note ) );
			}
		}
	}
}
