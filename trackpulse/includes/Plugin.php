<?php
namespace TrackPulse;

use TrackPulse\Admin\AdminMenu;
use TrackPulse\Admin\MetaBoxes;
use TrackPulse\Admin\OrderActions;
use TrackPulse\Admin\Settings;
use TrackPulse\Frontend\Shortcodes;
use TrackPulse\Scheduler\Cron;
use TrackPulse\Woo\EmailHooks;
use TrackPulse\Woo\OrderMeta;
use TrackPulse\Woo\Statuses;
use TrackPulse\Api\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	private function init() {
		load_plugin_textdomain( 'trackpulse', false, dirname( plugin_basename( TRACKPULSE_PLUGIN_FILE ) ) . '/languages' );

		$settings = new Settings();
		$settings->init();

		( new AdminMenu() )->init();
		( new MetaBoxes() )->init();
		( new OrderActions() )->init();
		( new OrderMeta() )->init();
		( new Shortcodes() )->init();
		( new Statuses() )->init();
		( new EmailHooks() )->init();
		( new Cron() )->init();
		( new Rest() )->init();
	}
}
