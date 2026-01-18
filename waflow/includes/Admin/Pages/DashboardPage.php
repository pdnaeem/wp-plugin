<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardPage {
	public function render() {
		if ( ! current_user_can( 'manage_waflow_crm' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		global $wpdb;

		$total_contacts = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_contacts" );
		$last_7 = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_contacts WHERE created_at >= %s", gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) ) ) );
		$last_30 = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_contacts WHERE created_at >= %s", gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) ) ) );
		$clicks_7 = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_activities WHERE type = %s AND created_at >= %s", 'click', gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) ) ) );
		$clicks_30 = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}waflow_activities WHERE type = %s AND created_at >= %s", 'click', gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) ) ) );

		$sources = $wpdb->get_results( "SELECT source, COUNT(*) as total FROM {$wpdb->prefix}waflow_contacts GROUP BY source ORDER BY total DESC" );

		$pages = $wpdb->get_results( $wpdb->prepare( "SELECT payload, COUNT(*) as total FROM {$wpdb->prefix}waflow_activities WHERE type = %s GROUP BY payload ORDER BY total DESC LIMIT 5", 'click' ) );

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'WAFlow Dashboard', 'waflow' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Track WhatsApp engagement and CRM performance.', 'waflow' ); ?></p>

			<div class="waflow-metrics">
				<div class="waflow-card">
					<h3><?php esc_html_e( 'Total Contacts', 'waflow' ); ?></h3>
					<p class="waflow-metric"><?php echo esc_html( $total_contacts ); ?></p>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'New Contacts (7 days)', 'waflow' ); ?></h3>
					<p class="waflow-metric"><?php echo esc_html( $last_7 ); ?></p>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'New Contacts (30 days)', 'waflow' ); ?></h3>
					<p class="waflow-metric"><?php echo esc_html( $last_30 ); ?></p>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'WhatsApp Clicks (7 days)', 'waflow' ); ?></h3>
					<p class="waflow-metric"><?php echo esc_html( $clicks_7 ); ?></p>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'WhatsApp Clicks (30 days)', 'waflow' ); ?></h3>
					<p class="waflow-metric"><?php echo esc_html( $clicks_30 ); ?></p>
				</div>
			</div>

			<div class="waflow-grid">
				<div class="waflow-card">
					<h3><?php esc_html_e( 'Leads by Source', 'waflow' ); ?></h3>
					<table class="widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Source', 'waflow' ); ?></th>
								<th><?php esc_html_e( 'Total', 'waflow' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty( $sources ) ) : ?>
								<tr><td colspan="2"><?php esc_html_e( 'No data yet.', 'waflow' ); ?></td></tr>
							<?php else : ?>
								<?php foreach ( $sources as $source ) : ?>
									<tr>
										<td><?php echo esc_html( $source->source ?: __( 'Unknown', 'waflow' ) ); ?></td>
										<td><?php echo esc_html( $source->total ); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
				<div class="waflow-card">
					<h3><?php esc_html_e( 'Top Pages by Clicks', 'waflow' ); ?></h3>
					<table class="widefat striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Page URL', 'waflow' ); ?></th>
								<th><?php esc_html_e( 'Clicks', 'waflow' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty( $pages ) ) : ?>
								<tr><td colspan="2"><?php esc_html_e( 'No data yet.', 'waflow' ); ?></td></tr>
							<?php else : ?>
								<?php foreach ( $pages as $page ) : ?>
									<?php $payload = json_decode( $page->payload, true ); ?>
									<tr>
										<td><?php echo esc_html( $payload['page_url'] ?? '' ); ?></td>
										<td><?php echo esc_html( $page->total ); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}
}
