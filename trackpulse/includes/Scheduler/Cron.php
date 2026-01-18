<?php
namespace TrackPulse\Scheduler;

use TrackPulse\WhatsApp\Sender;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cron {
	const HOOK = 'trackpulse_process_queue';

	public function init() {
		add_action( self::HOOK, array( $this, 'process_queue' ) );
		add_filter( 'cron_schedules', array( $this, 'add_interval' ) );
		self::schedule();
	}

	public static function add_interval( $schedules ) {
		$schedules['trackpulse_five_minutes'] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 5 Minutes', 'trackpulse' ),
		);
		return $schedules;
	}

	public static function schedule() {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time() + 60, 'trackpulse_five_minutes', self::HOOK );
		}
	}

	public static function unschedule() {
		$timestamp = wp_next_scheduled( self::HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::HOOK );
		}
	}

	public function process_queue() {
		$lock = get_transient( 'trackpulse_queue_lock' );
		if ( $lock ) {
			return;
		}
		set_transient( 'trackpulse_queue_lock', 1, 60 );

		$sender = new Sender();
		$sender->process_queue();

		delete_transient( 'trackpulse_queue_lock' );
	}
}
