<?php
namespace TrackPulse\Admin\Pages;

use TrackPulse\Database\LogsRepository;
use TrackPulse\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardPage {
	public function render() {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		$sent = LogsRepository::get_counts_last_days( 7, 'success' );
		$failed = LogsRepository::get_counts_last_days( 7, 'fail' );
		$shipped = LogsRepository::get_counts_last_days( 7, 'success' );
		$top_couriers = LogsRepository::get_top_couriers();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'TrackPulse Dashboard', 'trackpulse' ); ?></h1>
			<div class="trackpulse-dashboard">
				<div class="trackpulse-card">
					<h3><?php esc_html_e( 'Notifications sent (7 days)', 'trackpulse' ); ?></h3>
					<p class="trackpulse-stat"><?php echo esc_html( $sent ); ?></p>
				</div>
				<div class="trackpulse-card">
					<h3><?php esc_html_e( 'Failed notifications (7 days)', 'trackpulse' ); ?></h3>
					<p class="trackpulse-stat"><?php echo esc_html( $failed ); ?></p>
				</div>
				<div class="trackpulse-card">
					<h3><?php esc_html_e( 'Shipped notifications (7 days)', 'trackpulse' ); ?></h3>
					<p class="trackpulse-stat"><?php echo esc_html( $shipped ); ?></p>
				</div>
			</div>
			<h2><?php esc_html_e( 'Top couriers used', 'trackpulse' ); ?></h2>
			<table class="widefat">
				<thead><tr><th><?php esc_html_e( 'Courier', 'trackpulse' ); ?></th><th><?php esc_html_e( 'Count', 'trackpulse' ); ?></th></tr></thead>
				<tbody>
				<?php if ( $top_couriers ) : ?>
					<?php foreach ( $top_couriers as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row['message'] ); ?></td>
							<td><?php echo esc_html( $row['total'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="2"><?php esc_html_e( 'No data yet.', 'trackpulse' ); ?></td></tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
