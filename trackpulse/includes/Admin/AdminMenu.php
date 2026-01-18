<?php
namespace TrackPulse\Admin;

use TrackPulse\Capabilities;
use TrackPulse\Admin\Pages\DashboardPage;
use TrackPulse\Admin\Pages\CouriersPage;
use TrackPulse\Admin\Pages\LogsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminMenu {
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	public function register_menu() {
		if ( ! Capabilities::can_manage() ) {
			return;
		}

		$capability = 'manage_woocommerce';
		$slug = 'trackpulse-dashboard';

		add_menu_page(
			__( 'TrackPulse', 'trackpulse' ),
			__( 'TrackPulse', 'trackpulse' ),
			$capability,
			$slug,
			array( new DashboardPage(), 'render' ),
			'dashicons-location-alt',
			56
		);

		add_submenu_page( $slug, __( 'Dashboard', 'trackpulse' ), __( 'Dashboard', 'trackpulse' ), $capability, $slug, array( new DashboardPage(), 'render' ) );
		add_submenu_page( $slug, __( 'Couriers', 'trackpulse' ), __( 'Couriers', 'trackpulse' ), $capability, 'trackpulse-couriers', array( new CouriersPage(), 'render' ) );
		add_submenu_page( $slug, __( 'Logs', 'trackpulse' ), __( 'Logs', 'trackpulse' ), $capability, 'trackpulse-logs', array( new LogsPage(), 'render' ) );
		add_submenu_page( $slug, __( 'Settings', 'trackpulse' ), __( 'Settings', 'trackpulse' ), $capability, 'trackpulse-settings', array( Settings::class, 'render_page' ) );
	}
}
