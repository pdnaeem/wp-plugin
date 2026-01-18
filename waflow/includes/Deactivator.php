<?php

namespace WAFlow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Deactivator {
	public static function deactivate() {
		$timestamp = wp_next_scheduled( Automations\Queue::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, Automations\Queue::CRON_HOOK );
		}
	}
}
