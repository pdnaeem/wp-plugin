<?php

namespace WAFlow\Admin\Tables;

use WAFlow\Database\LogsRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class LogsTable extends \WP_List_Table {
	private $repo;

	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'log',
				'plural' => 'logs',
				'ajax' => false,
			)
		);

		$this->repo = new LogsRepository();
	}

	public function get_columns() {
		return array(
			'automation_id' => __( 'Automation', 'waflow' ),
			'contact_id' => __( 'Contact', 'waflow' ),
			'status' => __( 'Status', 'waflow' ),
			'message' => __( 'Message', 'waflow' ),
			'created_at' => __( 'Date', 'waflow' ),
		);
	}

	public function column_default( $item, $column_name ) {
		if ( isset( $item->$column_name ) ) {
			return esc_html( $item->$column_name );
		}

		return '';
	}

	public function prepare_items() {
		$per_page = 20;
		$current_page = $this->get_pagenum();

		$total_items = $this->repo->count();
		$this->items = $this->repo->query(
			array(
				'per_page' => $per_page,
				'offset' => ( $current_page - 1 ) * $per_page,
			)
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page' => $per_page,
			)
		);
	}
}
