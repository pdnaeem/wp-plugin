<?php

namespace WAFlow;

use WAFlow\Admin\AdminMenu;
use WAFlow\Automations\Engine;
use WAFlow\Automations\Queue;
use WAFlow\Frontend\Shortcodes;
use WAFlow\Frontend\Widget;
use WAFlow\Integrations\FluentForms;
use WAFlow\Integrations\WooCommerce;
use WAFlow\Integrations\WPForms;
use WAFlow\Api\Rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {
	public static function init() {
		load_plugin_textdomain( 'waflow', false, dirname( plugin_basename( WAFLOW_PLUGIN_FILE ) ) . '/languages' );

		add_filter( 'cron_schedules', array( Queue::class, 'register_schedule' ) );
		add_action( Queue::CRON_HOOK, array( Queue::class, 'process_queue' ) );

		$admin_menu = new AdminMenu();
		$admin_menu->register();

		$widget = new Widget();
		$widget->register();

		$shortcodes = new Shortcodes();
		$shortcodes->register();

		$engine = new Engine();
		$engine->register();

		if ( class_exists( 'WooCommerce' ) ) {
			$integration = new WooCommerce();
			$integration->register();
		}

		if ( class_exists( '\\WPForms' ) ) {
			$integration = new WPForms();
			$integration->register();
		}

		if ( defined( 'FLUENTFORM' ) ) {
			$integration = new FluentForms();
			$integration->register();
		}

		$rest = new Rest();
		$rest->register();
	}
}
