<?php

namespace WAFlow\Admin;

use WAFlow\Capabilities;
use WAFlow\Admin\Pages\DashboardPage;
use WAFlow\Admin\Pages\LeadsPage;
use WAFlow\Admin\Pages\ContactViewPage;
use WAFlow\Admin\Pages\AutomationsPage;
use WAFlow\Admin\Pages\LogsPage;
use WAFlow\Admin\Pages\SettingsPage;
use WAFlow\Admin\Pages\WizardPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminMenu {
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_to_wizard' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function add_menu() {
		$cap = Capabilities::MANAGE_CRM;

		add_menu_page(
			__( 'WAFlow', 'waflow' ),
			__( 'WAFlow', 'waflow' ),
			$cap,
			'waflow',
			array( new DashboardPage(), 'render' ),
			'dashicons-whatsapp',
			26
		);

		add_submenu_page( 'waflow', __( 'Dashboard', 'waflow' ), __( 'Dashboard', 'waflow' ), $cap, 'waflow', array( new DashboardPage(), 'render' ) );
		add_submenu_page( 'waflow', __( 'Leads & Contacts', 'waflow' ), __( 'Leads & Contacts', 'waflow' ), $cap, 'waflow-leads', array( new LeadsPage(), 'render' ) );
		add_submenu_page( 'waflow', __( 'Contact View', 'waflow' ), __( 'Contact View', 'waflow' ), $cap, 'waflow-contact', array( new ContactViewPage(), 'render' ) );
		add_submenu_page( 'waflow', __( 'Automations', 'waflow' ), __( 'Automations', 'waflow' ), $cap, 'waflow-automations', array( new AutomationsPage(), 'render' ) );
		add_submenu_page( 'waflow', __( 'Logs', 'waflow' ), __( 'Logs', 'waflow' ), $cap, 'waflow-logs', array( new LogsPage(), 'render' ) );
		add_submenu_page( 'waflow', __( 'Settings', 'waflow' ), __( 'Settings', 'waflow' ), 'manage_options', 'waflow-settings', array( new SettingsPage(), 'render' ) );
		add_submenu_page( null, __( 'Setup Wizard', 'waflow' ), __( 'Setup Wizard', 'waflow' ), 'manage_options', 'waflow-setup', array( new WizardPage(), 'render' ) );
	}

	public function enqueue_assets( $hook ) {
		if ( false === strpos( $hook, 'waflow' ) ) {
			return;
		}

		wp_enqueue_style( 'waflow-admin', WAFLOW_PLUGIN_URL . 'assets/css/admin.css', array(), WAFLOW_VERSION );
	}

	public function maybe_redirect_to_wizard() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( get_option( 'waflow_setup_complete' ) ) {
			return;
		}

		if ( isset( $_GET['page'] ) && 'waflow-setup' === $_GET['page'] ) {
			return;
		}

		if ( ! is_admin() ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=waflow-setup' ) );
		exit;
	}
}
