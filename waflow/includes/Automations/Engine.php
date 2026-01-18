<?php

namespace WAFlow\Automations;

use WAFlow\Database\AutomationsRepository;
use WAFlow\Database\ContactsRepository;
use WAFlow\Database\LogsRepository;
use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Engine {
	public function register() {
		// Triggers are handled by integrations and widget.
	}

	public static function handle_trigger( $trigger, $context ) {
		$repo = new AutomationsRepository();
		$automations = $repo->all_enabled_by_trigger( $trigger );
		if ( empty( $automations ) ) {
			return;
		}

		$contacts = new ContactsRepository();
		$logs = new LogsRepository();

		$contact = null;
		if ( ! empty( $context['contact_id'] ) ) {
			$contact = $contacts->get( (int) $context['contact_id'] );
		}

		foreach ( $automations as $automation ) {
			$definition = $automation->definition;
			if ( ! Triggers::matches( $definition['trigger'] ?? array(), $context ) ) {
				$logs->add(
					array(
						'automation_id' => $automation->id,
						'contact_id' => $context['contact_id'] ?? null,
						'status' => 'skipped',
						'message' => 'Trigger options did not match.',
						'payload' => $context,
					)
				);
				continue;
			}

			if ( ! Conditions::passes( $definition['conditions'] ?? array(), $contact, $context ) ) {
				$logs->add(
					array(
						'automation_id' => $automation->id,
						'contact_id' => $context['contact_id'] ?? null,
						'status' => 'skipped',
						'message' => 'Conditions not met.',
						'payload' => $context,
					)
				);
				continue;
			}

			$delay = $definition['delay'] ?? array( 'unit' => 'none', 'value' => 0 );
			if ( 'none' !== ( $delay['unit'] ?? 'none' ) && ! empty( $delay['value'] ) ) {
				Queue::enqueue_job( $automation->id, $context, $definition['actions'] ?? array(), $delay );
				$logs->add(
					array(
						'automation_id' => $automation->id,
						'contact_id' => $context['contact_id'] ?? null,
						'status' => 'queued',
						'message' => 'Actions queued with delay.',
						'payload' => $context,
					)
				);
				continue;
			}

			$results = Actions::execute( $automation->id, $contact, $context, $definition['actions'] ?? array() );
			$logs->add(
				array(
					'automation_id' => $automation->id,
					'contact_id' => $context['contact_id'] ?? null,
					'status' => $results['status'],
					'message' => $results['message'],
					'payload' => $results['payload'],
				)
			);
		}
	}
}
