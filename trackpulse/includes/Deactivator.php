<?php
namespace TrackPulse;

use TrackPulse\Scheduler\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Deactivator {
	public static function deactivate() {
		Cron::unschedule();
	}
}
