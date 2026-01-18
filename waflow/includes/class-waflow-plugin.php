<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WAFLOW_PLUGIN_DIR . 'includes/class-waflow-settings.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/data/class-waflow-crm.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/admin/class-waflow-admin.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/frontend/class-waflow-widget.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/integrations/class-waflow-woocommerce.php';
require_once WAFLOW_PLUGIN_DIR . 'includes/integrations/class-waflow-forms.php';

class WAFlow_Plugin {
	private $crm;
	private $admin;
	private $widget;
	private $woocommerce;
	private $forms;

	public function init() {
		$this->crm    = new WAFlow_CRM();
		$this->admin  = new WAFlow_Admin( $this->crm );
		$this->widget = new WAFlow_Widget( $this->crm );
		$this->woocommerce = new WAFlow_WooCommerce_Integration( $this->crm );
		$this->forms = new WAFlow_Forms_Integration( $this->crm );

		add_action( 'admin_init', array( $this->admin, 'handle_settings' ) );
		add_action( 'admin_menu', array( $this->admin, 'register_menu' ) );

		add_action( 'init', array( $this->widget, 'register_shortcodes' ) );
		add_action( 'wp_enqueue_scripts', array( $this->widget, 'register_assets' ) );
		add_action( 'wp_footer', array( $this->widget, 'render_widget' ) );

		add_action( 'admin_post_nopriv_waflow_capture_lead', array( $this->widget, 'handle_lead_capture' ) );
		add_action( 'admin_post_waflow_capture_lead', array( $this->widget, 'handle_lead_capture' ) );

		$this->woocommerce->init();
		$this->forms->init();
	}
}
