<?php

namespace WAFlow;

use WAFlow\Database\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activator {
	public static function activate() {
		Schema::create_tables();

		Capabilities::add_caps();

		$defaults = Helpers::default_settings();
		if ( false === get_option( 'waflow_settings' ) ) {
			add_option( 'waflow_settings', $defaults );
		}

		if ( ! wp_next_scheduled( Automations\Queue::CRON_HOOK ) ) {
			wp_schedule_event( time() + 300, Automations\Queue::CRON_INTERVAL, Automations\Queue::CRON_HOOK );
		}

		if ( false === get_option( 'waflow_setup_complete' ) ) {
			add_option( 'waflow_setup_complete', 0 );
		}
	}
}
