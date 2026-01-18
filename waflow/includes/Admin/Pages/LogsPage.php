<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Admin\Tables\LogsTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogsPage {
	public function render() {
		if ( ! current_user_can( 'manage_waflow_crm' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		$table = new LogsTable();
		$table->prepare_items();

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'Automation Logs', 'waflow' ); ?></h1>
			<?php $table->display(); ?>
		</div>
		<?php
	}
}
