<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Admin\Tables\ContactsTable;
use WAFlow\Database\ContactsRepository;
use WAFlow\Database\ActivitiesRepository;
use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LeadsPage {
	public function render() {
		if ( ! current_user_can( 'manage_waflow_crm' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		$this->handle_actions();

		$table = new ContactsTable();
		$table->prepare_items();

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'Leads & Contacts', 'waflow' ); ?></h1>
			<form method="post">
				<?php wp_nonce_field( 'waflow_contacts_action', 'waflow_contacts_nonce' ); ?>
				<?php $table->search_box( __( 'Search Contacts', 'waflow' ), 'waflow-search' ); ?>
				<?php $table->display(); ?>
				<p class="description"><?php esc_html_e( 'Use bulk actions to assign users, tag, or export CSV.', 'waflow' ); ?></p>
				<div class="waflow-bulk-extra">
					<label>
						<?php esc_html_e( 'Assign User', 'waflow' ); ?>
						<?php wp_dropdown_users( array( 'name' => 'bulk_assign_user', 'show_option_none' => __( 'Select user', 'waflow' ) ) ); ?>
					</label>
					<label>
						<?php esc_html_e( 'Tag', 'waflow' ); ?>
						<input type="text" name="bulk_tag" />
					</label>
				</div>
			</form>
		</div>
		<?php
	}

	private function handle_actions() {
		$action = sanitize_text_field( wp_unslash( $_POST['action'] ?? '' ) );
		$action2 = sanitize_text_field( wp_unslash( $_POST['action2'] ?? '' ) );
		$action = ( $action && '-1' !== $action ) ? $action : $action2;
		if ( empty( $action ) || '-1' === $action ) {
			return;
		}

		if ( empty( $_POST['waflow_contacts_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_contacts_nonce'] ) ), 'waflow_contacts_action' ) ) {
			return;
		}

		$action = sanitize_text_field( $action );
		$contact_ids = isset( $_POST['contact_ids'] ) ? array_map( 'absint', (array) $_POST['contact_ids'] ) : array();

		if ( empty( $contact_ids ) ) {
			return;
		}

		$repo = new ContactsRepository();
		$activities = new ActivitiesRepository();

		switch ( $action ) {
			case 'assign':
				$user_id = isset( $_POST['bulk_assign_user'] ) ? absint( $_POST['bulk_assign_user'] ) : 0;
				if ( $user_id ) {
					foreach ( $contact_ids as $contact_id ) {
						$repo->update( $contact_id, array( 'assigned_user_id' => $user_id, 'updated_at' => current_time( 'mysql' ) ) );
						$activities->add( $contact_id, 'assigned', array( 'user_id' => $user_id ) );
					}
				}
				break;
			case 'add_tag':
				$tag = sanitize_text_field( wp_unslash( $_POST['bulk_tag'] ?? '' ) );
				foreach ( $contact_ids as $contact_id ) {
					$contact = $repo->get( $contact_id );
					$tags = Helpers::sanitize_array_text( explode( ',', $contact->tags ) );
					if ( $tag && ! in_array( $tag, $tags, true ) ) {
						$tags[] = $tag;
						$repo->update( $contact_id, array( 'tags' => implode( ',', $tags ), 'updated_at' => current_time( 'mysql' ) ) );
						$activities->add( $contact_id, 'tag_added', array( 'tag' => $tag ) );
					}
				}
				break;
			case 'remove_tag':
				$tag = sanitize_text_field( wp_unslash( $_POST['bulk_tag'] ?? '' ) );
				foreach ( $contact_ids as $contact_id ) {
					$contact = $repo->get( $contact_id );
					$tags = Helpers::sanitize_array_text( explode( ',', $contact->tags ) );
					if ( $tag && in_array( $tag, $tags, true ) ) {
						$tags = array_diff( $tags, array( $tag ) );
						$repo->update( $contact_id, array( 'tags' => implode( ',', $tags ), 'updated_at' => current_time( 'mysql' ) ) );
						$activities->add( $contact_id, 'tag_removed', array( 'tag' => $tag ) );
					}
				}
				break;
			case 'export':
				$this->export_csv( $contact_ids );
				break;
		}
	}

	private function export_csv( $contact_ids ) {
		if ( headers_sent() ) {
			return;
		}

		$repo = new ContactsRepository();
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="waflow-contacts.csv"' );

		$fh = fopen( 'php://output', 'w' );
		fputcsv( $fh, array( 'ID', 'Name', 'Phone', 'Email', 'Source', 'Tags', 'Last Seen' ) );
		foreach ( $contact_ids as $contact_id ) {
			$contact = $repo->get( $contact_id );
			if ( $contact ) {
				fputcsv( $fh, array( $contact->id, $contact->name, $contact->phone, $contact->email, $contact->source, $contact->tags, $contact->last_seen ) );
			}
		}
		fclose( $fh );
		exit;
	}
}
