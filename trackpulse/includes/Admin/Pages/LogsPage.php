<?php
namespace TrackPulse\Admin\Pages;

use TrackPulse\Admin\Tables\LogsTable;
use TrackPulse\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogsPage {
	public function render() {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		if ( isset( $_GET['export'] ) ) {
			$this->export_csv();
			return;
		}

		$table = new LogsTable();
		$table->prepare_items();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'TrackPulse Logs', 'trackpulse' ); ?></h1>
			<form method="get">
				<input type="hidden" name="page" value="trackpulse-logs">
				<select name="status">
					<option value=""><?php esc_html_e( 'All statuses', 'trackpulse' ); ?></option>
					<?php foreach ( array( 'success', 'fail', 'skipped', 'queued' ) as $status ) : ?>
						<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $_GET['status'] ?? '', $status ); ?>><?php echo esc_html( ucfirst( $status ) ); ?></option>
					<?php endforeach; ?>
				</select>
				<select name="event">
					<option value=""><?php esc_html_e( 'All events', 'trackpulse' ); ?></option>
					<?php foreach ( array( 'order_placed', 'payment_complete', 'status_processing', 'shipped', 'delivered', 'cancelled', 'refunded', 'manual_send' ) as $event ) : ?>
						<option value="<?php echo esc_attr( $event ); ?>" <?php selected( $_GET['event'] ?? '', $event ); ?>><?php echo esc_html( ucfirst( str_replace( '_', ' ', $event ) ) ); ?></option>
					<?php endforeach; ?>
				</select>
				<select name="range">
					<option value="7" <?php selected( $_GET['range'] ?? '', '7' ); ?>><?php esc_html_e( 'Last 7 days', 'trackpulse' ); ?></option>
					<option value="30" <?php selected( $_GET['range'] ?? '', '30' ); ?>><?php esc_html_e( 'Last 30 days', 'trackpulse' ); ?></option>
					<option value="all" <?php selected( $_GET['range'] ?? '', 'all' ); ?>><?php esc_html_e( 'All time', 'trackpulse' ); ?></option>
				</select>
				<?php submit_button( __( 'Filter', 'trackpulse' ), 'secondary', '', false ); ?>
				<a class="button" href="<?php echo esc_url( add_query_arg( array( 'page' => 'trackpulse-logs', 'export' => 1 ) ) ); ?>"><?php esc_html_e( 'Export CSV', 'trackpulse' ); ?></a>
			</form>
			<form method="post">
				<?php $table->display(); ?>
			</form>
		</div>
		<?php
	}

	private function export_csv() {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_logs';
		$rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A );

		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="trackpulse-logs.csv"' );

		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, array( 'id', 'order_id', 'channel', 'event', 'recipient', 'status', 'message', 'response', 'created_at' ) );
		foreach ( $rows as $row ) {
			fputcsv( $output, $row );
		}
		fclose( $output );
		exit;
	}
}
