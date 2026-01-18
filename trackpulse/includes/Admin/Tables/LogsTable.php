<?php
namespace TrackPulse\Admin\Tables;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class LogsTable extends \WP_List_Table {
	public function prepare_items() {
		global $wpdb;
		$table = $wpdb->prefix . 'trackpulse_logs';

		$per_page = 20;
		$paged = $this->get_pagenum();
		$offset = ( $paged - 1 ) * $per_page;

		$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
		$event = isset( $_GET['event'] ) ? sanitize_text_field( wp_unslash( $_GET['event'] ) ) : '';
		$range = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7';

		$where = 'WHERE 1=1';
		$params = array();
		if ( $status ) {
			$where .= ' AND status = %s';
			$params[] = $status;
		}
		if ( $event ) {
			$where .= ' AND event = %s';
			$params[] = $event;
		}
		if ( 'all' !== $range ) {
			$after = gmdate( 'Y-m-d H:i:s', time() - ( absint( $range ) * DAY_IN_SECONDS ) );
			$where .= ' AND created_at >= %s';
			$params[] = $after;
		}

		$sql = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$params[] = $per_page;
		$params[] = $offset;

		$this->items = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );

		$count_sql = "SELECT COUNT(*) FROM {$table} {$where}";
		$count_params = $params;
		if ( count( $count_params ) >= 2 ) {
			array_pop( $count_params );
			array_pop( $count_params );
		}
		if ( $count_params ) {
			$total_items = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $count_params ) );
		} else {
			$total_items = (int) $wpdb->get_var( $count_sql );
		}
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
		) );
	}

	public function get_columns() {
		return array(
			'created_at' => __( 'Date', 'trackpulse' ),
			'order_id'   => __( 'Order', 'trackpulse' ),
			'event'      => __( 'Event', 'trackpulse' ),
			'recipient'  => __( 'Recipient', 'trackpulse' ),
			'status'     => __( 'Status', 'trackpulse' ),
			'channel'    => __( 'Channel', 'trackpulse' ),
			'message'    => __( 'Message', 'trackpulse' ),
		);
	}

	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'order_id':
				if ( $item['order_id'] ) {
					$edit_link = admin_url( 'post.php?post=' . $item['order_id'] . '&action=edit' );
					return sprintf( '<a href="%s">#%s</a>', esc_url( $edit_link ), esc_html( $item['order_id'] ) );
				}
				return '—';
			case 'message':
				return esc_html( wp_trim_words( $item['message'], 10, '…' ) );
			default:
				return esc_html( $item[ $column_name ] ?? '' );
		}
	}
}
