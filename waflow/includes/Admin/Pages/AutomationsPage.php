<?php

namespace WAFlow\Admin\Pages;

use WAFlow\Database\AutomationsRepository;
use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AutomationsPage {
	public function render() {
		if ( ! current_user_can( 'manage_waflow_crm' ) ) {
			wp_die( esc_html__( 'Access denied.', 'waflow' ) );
		}

		$repo = new AutomationsRepository();
		$this->handle_actions( $repo );

		$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			$this->render_form( $repo );
			return;
		}

		$automations = $repo->all_enabled_by_trigger( '' );
		global $wpdb;
		$automations = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}waflow_automations ORDER BY created_at DESC" );

		?>
		<div class="wrap waflow-admin">
			<h1><?php esc_html_e( 'Automations', 'waflow' ); ?></h1>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-automations&action=add' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add New', 'waflow' ); ?></a>
			<form method="post" style="margin-top:16px;">
				<?php wp_nonce_field( 'waflow_import_examples', 'waflow_import_nonce' ); ?>
				<?php submit_button( __( 'Import Example Automations', 'waflow' ), 'secondary', 'waflow_import_examples', false ); ?>
			</form>
			<table class="widefat striped" style="margin-top:16px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Name', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Status', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Trigger', 'waflow' ); ?></th>
						<th><?php esc_html_e( 'Updated', 'waflow' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $automations ) ) : ?>
						<tr><td colspan="4"><?php esc_html_e( 'No automations yet.', 'waflow' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $automations as $automation ) : ?>
							<?php $definition = json_decode( $automation->definition, true ); ?>
							<tr>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=waflow-automations&action=edit&automation_id=' . absint( $automation->id ) ) ); ?>">
										<?php echo esc_html( $automation->name ); ?>
									</a>
								</td>
								<td><?php echo $automation->status ? esc_html__( 'Enabled', 'waflow' ) : esc_html__( 'Disabled', 'waflow' ); ?></td>
								<td><?php echo esc_html( $definition['trigger']['type'] ?? '' ); ?></td>
								<td><?php echo esc_html( $automation->updated_at ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function render_form( AutomationsRepository $repo ) {
		$automation_id = isset( $_GET['automation_id'] ) ? absint( $_GET['automation_id'] ) : 0;
		$automation = $automation_id ? $repo->get( $automation_id ) : null;
		$definition = $automation ? json_decode( $automation->definition, true ) : array();

		$trigger = $definition['trigger']['type'] ?? 'widget_click';
		$conditions = $definition['conditions'] ?? array();
		$actions = $definition['actions'] ?? array();

		?>
		<div class="wrap waflow-admin">
			<h1><?php echo $automation ? esc_html__( 'Edit Automation', 'waflow' ) : esc_html__( 'Add Automation', 'waflow' ); ?></h1>
			<form method="post">
				<?php wp_nonce_field( 'waflow_save_automation', 'waflow_automation_nonce' ); ?>
				<input type="hidden" name="automation_id" value="<?php echo esc_attr( $automation_id ); ?>" />
				<table class="form-table">
					<tr>
						<th><?php esc_html_e( 'Name', 'waflow' ); ?></th>
						<td><input type="text" name="automation_name" value="<?php echo esc_attr( $automation->name ?? '' ); ?>" class="regular-text" required /></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Enabled', 'waflow' ); ?></th>
						<td><input type="checkbox" name="automation_status" <?php checked( empty( $automation ) || $automation->status ); ?> /></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Trigger', 'waflow' ); ?></th>
						<td>
							<select name="trigger_type">
								<option value="widget_click" <?php selected( $trigger, 'widget_click' ); ?>><?php esc_html_e( 'Widget Click', 'waflow' ); ?></option>
								<option value="prechat_submit" <?php selected( $trigger, 'prechat_submit' ); ?>><?php esc_html_e( 'Pre-chat Submit', 'waflow' ); ?></option>
								<option value="wpforms_submit" <?php selected( $trigger, 'wpforms_submit' ); ?>><?php esc_html_e( 'WPForms Submit', 'waflow' ); ?></option>
								<option value="fluentforms_submit" <?php selected( $trigger, 'fluentforms_submit' ); ?>><?php esc_html_e( 'Fluent Forms Submit', 'waflow' ); ?></option>
								<option value="woocommerce_order_created" <?php selected( $trigger, 'woocommerce_order_created' ); ?>><?php esc_html_e( 'WooCommerce Order Created', 'waflow' ); ?></option>
								<option value="woocommerce_order_status_changed" <?php selected( $trigger, 'woocommerce_order_status_changed' ); ?>><?php esc_html_e( 'WooCommerce Status Changed', 'waflow' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Configure trigger options below if applicable.', 'waflow' ); ?></p>
							<label><?php esc_html_e( 'Form ID (optional)', 'waflow' ); ?> <input type="text" name="trigger_form_id" value="<?php echo esc_attr( $definition['trigger']['form_id'] ?? '' ); ?>" /></label>
							<label><?php esc_html_e( 'From Status', 'waflow' ); ?> <input type="text" name="trigger_from_status" value="<?php echo esc_attr( $definition['trigger']['from_status'] ?? '' ); ?>" /></label>
							<label><?php esc_html_e( 'To Status', 'waflow' ); ?> <input type="text" name="trigger_to_status" value="<?php echo esc_attr( $definition['trigger']['to_status'] ?? '' ); ?>" /></label>
						</td>
					</tr>
				</table>

				<h2><?php esc_html_e( 'Conditions', 'waflow' ); ?></h2>
				<div id="waflow-conditions">
					<?php if ( empty( $conditions ) ) : ?>
						<?php $conditions = array( array( 'field' => '', 'operator' => '', 'value' => '' ) ); ?>
					<?php endif; ?>
					<?php foreach ( $conditions as $condition ) : ?>
						<div class="waflow-row">
							<input type="text" name="conditions[field][]" value="<?php echo esc_attr( $condition['field'] ?? '' ); ?>" placeholder="field" />
							<select name="conditions[operator][]">
								<option value="equals" <?php selected( $condition['operator'] ?? '', 'equals' ); ?>>equals</option>
								<option value="contains" <?php selected( $condition['operator'] ?? '', 'contains' ); ?>>contains</option>
								<option value="greater_than" <?php selected( $condition['operator'] ?? '', 'greater_than' ); ?>>greater_than</option>
								<option value="has_tag" <?php selected( $condition['operator'] ?? '', 'has_tag' ); ?>>has_tag</option>
								<option value="not_has_tag" <?php selected( $condition['operator'] ?? '', 'not_has_tag' ); ?>>not_has_tag</option>
							</select>
							<input type="text" name="conditions[value][]" value="<?php echo esc_attr( $condition['value'] ?? '' ); ?>" placeholder="value" />
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" id="waflow-add-condition"><?php esc_html_e( 'Add Condition', 'waflow' ); ?></button>

				<h2><?php esc_html_e( 'Actions', 'waflow' ); ?></h2>
				<div id="waflow-actions">
					<?php if ( empty( $actions ) ) : ?>
						<?php $actions = array( array( 'type' => 'add_tag', 'value' => '' ) ); ?>
					<?php endif; ?>
					<?php foreach ( $actions as $action ) : ?>
						<div class="waflow-row">
							<select name="actions[type][]">
								<option value="add_tag" <?php selected( $action['type'] ?? '', 'add_tag' ); ?>>add_tag</option>
								<option value="assign_user" <?php selected( $action['type'] ?? '', 'assign_user' ); ?>>assign_user</option>
								<option value="add_note" <?php selected( $action['type'] ?? '', 'add_note' ); ?>>add_note</option>
								<option value="create_deal" <?php selected( $action['type'] ?? '', 'create_deal' ); ?>>create_deal</option>
								<option value="move_deal_stage" <?php selected( $action['type'] ?? '', 'move_deal_stage' ); ?>>move_deal_stage</option>
								<option value="send_message" <?php selected( $action['type'] ?? '', 'send_message' ); ?>>send_message</option>
							</select>
							<input type="text" name="actions[value][]" value="<?php echo esc_attr( $action['value'] ?? '' ); ?>" placeholder="value or template" />
							<input type="text" name="actions[extra][]" value="<?php echo esc_attr( $action['extra'] ?? '' ); ?>" placeholder="extra (mode, stage)" />
						</div>
					<?php endforeach; ?>
				</div>
				<button type="button" class="button" id="waflow-add-action"><?php esc_html_e( 'Add Action', 'waflow' ); ?></button>

				<h2><?php esc_html_e( 'Delay', 'waflow' ); ?></h2>
				<p>
					<select name="delay_unit">
						<option value="none" <?php selected( $definition['delay']['unit'] ?? 'none', 'none' ); ?>><?php esc_html_e( 'None', 'waflow' ); ?></option>
						<option value="minutes" <?php selected( $definition['delay']['unit'] ?? '', 'minutes' ); ?>><?php esc_html_e( 'Minutes', 'waflow' ); ?></option>
						<option value="hours" <?php selected( $definition['delay']['unit'] ?? '', 'hours' ); ?>><?php esc_html_e( 'Hours', 'waflow' ); ?></option>
						<option value="days" <?php selected( $definition['delay']['unit'] ?? '', 'days' ); ?>><?php esc_html_e( 'Days', 'waflow' ); ?></option>
					</select>
					<input type="number" name="delay_value" value="<?php echo esc_attr( $definition['delay']['value'] ?? '' ); ?>" min="0" />
					<label><input type="checkbox" name="delay_business_hours" <?php checked( ! empty( $definition['delay']['business_hours'] ) ); ?> /> <?php esc_html_e( 'Business hours only', 'waflow' ); ?></label>
				</p>

				<?php submit_button( __( 'Save Automation', 'waflow' ) ); ?>
			</form>
		</div>
		<script>
		(function(){
			function addRow(containerId){
				var container = document.getElementById(containerId);
				if (!container) {return;}
				var row = container.querySelector('.waflow-row');
				if (!row) {return;}
				var clone = row.cloneNode(true);
				clone.querySelectorAll('input').forEach(function(input){ input.value = ''; });
				container.appendChild(clone);
			}
			document.getElementById('waflow-add-condition').addEventListener('click', function(){ addRow('waflow-conditions'); });
			document.getElementById('waflow-add-action').addEventListener('click', function(){ addRow('waflow-actions'); });
		})();
		</script>
		<?php
	}

	private function handle_actions( AutomationsRepository $repo ) {
		if ( isset( $_POST['waflow_import_examples'] ) ) {
			if ( empty( $_POST['waflow_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_import_nonce'] ) ), 'waflow_import_examples' ) ) {
				return;
			}

			$examples = array(
				array(
					'name' => __( 'New lead → tag + assign + message', 'waflow' ),
					'definition' => array(
						'trigger' => array( 'type' => 'prechat_submit' ),
						'conditions' => array(),
						'actions' => array(
							array( 'type' => 'add_tag', 'value' => 'new-lead' ),
							array( 'type' => 'assign_user', 'value' => 'round_robin' ),
							array( 'type' => 'send_message', 'value' => 'Hi {name}, thanks for reaching out!', 'extra' => 'click_to_chat' ),
						),
						'delay' => array( 'unit' => 'none', 'value' => 0, 'business_hours' => false ),
					),
				),
				array(
					'name' => __( 'Order completed → thank you', 'waflow' ),
					'definition' => array(
						'trigger' => array( 'type' => 'woocommerce_order_status_changed', 'to_status' => 'completed' ),
						'conditions' => array(),
						'actions' => array(
							array( 'type' => 'send_message', 'value' => 'Thanks {name}! Order #{order_id} is completed.', 'extra' => 'click_to_chat' ),
						),
						'delay' => array( 'unit' => 'none', 'value' => 0, 'business_hours' => false ),
					),
				),
			);

			foreach ( $examples as $example ) {
				$repo->create(
					array(
						'name' => $example['name'],
						'status' => 1,
						'definition' => $example['definition'],
					)
				);
			}
		}

		if ( empty( $_POST['waflow_automation_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['waflow_automation_nonce'] ) ), 'waflow_save_automation' ) ) {
			return;
		}

		$name = sanitize_text_field( wp_unslash( $_POST['automation_name'] ?? '' ) );
		$status = isset( $_POST['automation_status'] ) ? 1 : 0;
		$automation_id = isset( $_POST['automation_id'] ) ? absint( $_POST['automation_id'] ) : 0;

		$definition = array(
			'trigger' => array(
				'type' => sanitize_text_field( wp_unslash( $_POST['trigger_type'] ?? '' ) ),
				'form_id' => sanitize_text_field( wp_unslash( $_POST['trigger_form_id'] ?? '' ) ),
				'from_status' => sanitize_text_field( wp_unslash( $_POST['trigger_from_status'] ?? '' ) ),
				'to_status' => sanitize_text_field( wp_unslash( $_POST['trigger_to_status'] ?? '' ) ),
			),
			'conditions' => $this->sanitize_conditions( $_POST['conditions'] ?? array() ),
			'actions' => $this->sanitize_actions( $_POST['actions'] ?? array() ),
			'delay' => array(
				'unit' => sanitize_text_field( wp_unslash( $_POST['delay_unit'] ?? 'none' ) ),
				'value' => absint( $_POST['delay_value'] ?? 0 ),
				'business_hours' => isset( $_POST['delay_business_hours'] ),
			),
		);

		if ( $automation_id ) {
			$repo->update(
				$automation_id,
				array(
					'name' => $name,
					'status' => $status,
					'definition' => $definition,
				)
			);
		} else {
			$repo->create(
				array(
					'name' => $name,
					'status' => $status,
					'definition' => $definition,
				)
			);
		}
	}

	private function sanitize_conditions( $raw ) {
		$fields = array_map( 'sanitize_text_field', wp_unslash( $raw['field'] ?? array() ) );
		$operators = array_map( 'sanitize_text_field', wp_unslash( $raw['operator'] ?? array() ) );
		$values = array_map( 'sanitize_text_field', wp_unslash( $raw['value'] ?? array() ) );

		$conditions = array();
		foreach ( $fields as $index => $field ) {
			if ( '' === $field ) {
				continue;
			}
			$conditions[] = array(
				'field' => $field,
				'operator' => $operators[ $index ] ?? 'equals',
				'value' => $values[ $index ] ?? '',
			);
		}

		return $conditions;
	}

	private function sanitize_actions( $raw ) {
		$types = array_map( 'sanitize_text_field', wp_unslash( $raw['type'] ?? array() ) );
		$values = array_map( 'sanitize_text_field', wp_unslash( $raw['value'] ?? array() ) );
		$extras = array_map( 'sanitize_text_field', wp_unslash( $raw['extra'] ?? array() ) );

		$actions = array();
		foreach ( $types as $index => $type ) {
			if ( '' === $type ) {
				continue;
			}
			$actions[] = array(
				'type' => $type,
				'value' => $values[ $index ] ?? '',
				'extra' => $extras[ $index ] ?? '',
			);
		}

		return $actions;
	}
}
