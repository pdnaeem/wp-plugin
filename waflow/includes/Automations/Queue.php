<?php

namespace WAFlow\Automations;

use WAFlow\Database\QueueRepository;
use WAFlow\Database\ContactsRepository;
use WAFlow\Database\LogsRepository;
use WAFlow\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Queue {
	const CRON_HOOK = 'waflow_process_queue';
	const CRON_INTERVAL = 'waflow_every_five_minutes';

	public static function register_schedule( $schedules ) {
		$schedules[ self::CRON_INTERVAL ] = array(
			'interval' => 300,
			'display' => __( 'Every Five Minutes', 'waflow' ),
		);
		return $schedules;
	}

	public static function enqueue_job( $automation_id, $context, $actions, $delay ) {
		$repo = new QueueRepository();
		$run_at = self::calculate_run_at( $delay );
		$repo->enqueue(
			$run_at,
			array(
				'automation_id' => $automation_id,
				'context' => $context,
				'actions' => $actions,
			)
		);
	}

	public static function process_queue() {
		$repo = new QueueRepository();
		$logs = new LogsRepository();
		$contacts = new ContactsRepository();

		$jobs = $repo->fetch_due( 10 );
		foreach ( $jobs as $job ) {
			$repo->mark_processing( $job->id );

			$payload = json_decode( $job->payload, true );
			$context = $payload['context'] ?? array();
			$actions = $payload['actions'] ?? array();

			$contact = null;
			if ( ! empty( $context['contact_id'] ) ) {
				$contact = $contacts->get( (int) $context['contact_id'] );
			}

			$results = Actions::execute( $payload['automation_id'] ?? 0, $contact, $context, $actions );
			if ( 'success' === $results['status'] ) {
				$repo->mark_complete( $job->id );
			} else {
				$repo->mark_failed( $job->id, $results['message'], (int) $job->attempts + 1 );
			}

			$logs->add(
				array(
					'automation_id' => $payload['automation_id'] ?? 0,
					'contact_id' => $context['contact_id'] ?? null,
					'status' => $results['status'],
					'message' => $results['message'],
					'payload' => $results['payload'],
				)
			);
		}
	}

	private static function calculate_run_at( $delay ) {
		$unit = $delay['unit'] ?? 'none';
		$value = (int) ( $delay['value'] ?? 0 );
		if ( 'none' === $unit || $value <= 0 ) {
			return current_time( 'mysql' );
		}

		$seconds = $value * MINUTE_IN_SECONDS;
		if ( 'hours' === $unit ) {
			$seconds = $value * HOUR_IN_SECONDS;
		} elseif ( 'days' === $unit ) {
			$seconds = $value * DAY_IN_SECONDS;
		}

		$run_at = time() + $seconds;

		if ( ! empty( $delay['business_hours'] ) ) {
			$settings = Helpers::get_settings();
			if ( ! Helpers::is_business_open( $settings ) ) {
				$run_at = strtotime( 'tomorrow 09:00', $run_at );
			}
		}

		return gmdate( 'Y-m-d H:i:s', $run_at );
	}
}
