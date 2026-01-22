<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Admin {
    public function register(): void {
        add_action('admin_menu', [$this, 'register_menu']);
    }

    public function register_menu(): void {
        add_menu_page(
            __('Ecolepedia Marketplace', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            __('Ecolepedia', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS,
            'ecolepedia-marketplace',
            [$this, 'render_settings_page'],
            'dashicons-welcome-write-blog'
        );

        add_submenu_page('ecolepedia-marketplace', __('Orders', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), __('Orders', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), ECOLEPEDIA_MARKETPLACE_CAP_ORDERS, 'edit.php?post_type=ecolepedia_order');
        add_submenu_page('ecolepedia-marketplace', __('Settings', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), __('Settings', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS, 'ecolepedia-marketplace', [$this, 'render_settings_page']);
        add_submenu_page('ecolepedia-marketplace', __('System Status', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), __('System Status', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS, 'ecolepedia-marketplace-status', [$this, 'render_status_page']);
        add_submenu_page('ecolepedia-marketplace', __('Demo Mode', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), __('Demo Mode', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS, 'ecolepedia-marketplace-demo', [$this, 'render_demo_page']);
    }

    public function render_settings_page(): void {
        if (!current_user_can(ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS)) {
            return;
        }
        echo '<div class="wrap ecolepedia-admin">';
        echo '<h1>' . esc_html__('Ecolepedia Marketplace Settings', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('ecolepedia_marketplace_settings');
        do_settings_sections('ecolepedia-marketplace');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function render_status_page(): void {
        if (!current_user_can(ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS)) {
            return;
        }
        $status = System_Status::get();
        echo '<div class="wrap ecolepedia-admin">';
        echo '<h1>' . esc_html__('System Status', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) . '</h1>';
        echo '<table class="widefat striped">';
        foreach ($status as $label => $value) {
            echo '<tr><th>' . esc_html($label) . '</th><td>' . esc_html($value) . '</td></tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    public function render_demo_page(): void {
        if (!current_user_can(ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS)) {
            return;
        }
        if (isset($_POST['ecolepedia_demo_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ecolepedia_demo_nonce'])), 'ecolepedia_demo_generate')) {
            Demo_Data::generate();
            echo '<div class="notice notice-success"><p>' . esc_html__('Demo data generated.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) . '</p></div>';
        }

        echo '<div class="wrap ecolepedia-admin">';
        echo '<h1>' . esc_html__('Demo Mode', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) . '</h1>';
        echo '<p>' . esc_html__('Generate sample subjects, document types, authors, and orders for testing.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) . '</p>';
        echo '<form method="post">';
        wp_nonce_field('ecolepedia_demo_generate', 'ecolepedia_demo_nonce');
        submit_button(__('Generate Demo Data', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN));
        echo '</form>';
        echo '</div>';
    }
}
