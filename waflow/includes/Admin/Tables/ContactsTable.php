<?php

namespace WAFlow\Admin\Tables;

use WAFlow\Database\ContactsRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class ContactsTable extends \WP_List_Table {
	private $repo;

	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'contact',
				'plural' => 'contacts',
				'ajax' => false,
			)
		);

		$this->repo = new ContactsRepository();
	}

	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />',
			'name' => __( 'Name', 'waflow' ),
			'phone' => __( 'Phone', 'waflow' ),
			'email' => __( 'Email', 'waflow' ),
			'source' => __( 'Source', 'waflow' ),
			'last_seen' => __( 'Last Seen', 'waflow' ),
		);
	}

	protected function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="contact_ids[]" value="%d" />', absint( $item->id ) );
	}

	public function column_name( $item ) {
		$url = admin_url( 'admin.php?page=waflow-contact&contact_id=' . absint( $item->id ) );
		return sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $item->name ) );
	}

	public function column_default( $item, $column_name ) {
		if ( isset( $item->$column_name ) ) {
			return esc_html( $item->$column_name );
		}

		return '';
	}

	protected function get_bulk_actions() {
		return array(
			'assign' => __( 'Assign User', 'waflow' ),
			'add_tag' => __( 'Add Tag', 'waflow' ),
			'remove_tag' => __( 'Remove Tag', 'waflow' ),
			'export' => __( 'Export CSV', 'waflow' ),
		);
	}

	public function prepare_items() {
		$per_page = 20;
		$current_page = $this->get_pagenum();

		$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
		$source = isset( $_REQUEST['source'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['source'] ) ) : '';

		$total_items = $this->repo->count(
			array(
				'search' => $search,
				'source' => $source,
			)
		);

		$this->items = $this->repo->query(
			array(
				'search' => $search,
				'source' => $source,
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

	public function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		$source = isset( $_REQUEST['source'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['source'] ) ) : '';
		?>
		<div class="alignleft actions">
			<select name="source">
				<option value=""><?php esc_html_e( 'All Sources', 'waflow' ); ?></option>
				<option value="widget" <?php selected( $source, 'widget' ); ?>><?php esc_html_e( 'Widget', 'waflow' ); ?></option>
				<option value="prechat" <?php selected( $source, 'prechat' ); ?>><?php esc_html_e( 'Pre-chat', 'waflow' ); ?></option>
				<option value="wpforms" <?php selected( $source, 'wpforms' ); ?>><?php esc_html_e( 'WPForms', 'waflow' ); ?></option>
				<option value="fluentforms" <?php selected( $source, 'fluentforms' ); ?>><?php esc_html_e( 'Fluent Forms', 'waflow' ); ?></option>
				<option value="woocommerce" <?php selected( $source, 'woocommerce' ); ?>><?php esc_html_e( 'WooCommerce', 'waflow' ); ?></option>
			</select>
			<?php submit_button( __( 'Filter', 'waflow' ), '', 'filter_action', false ); ?>
		</div>
		<?php
	}
}
