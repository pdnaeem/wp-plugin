<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Assets {
    public function register(): void {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
    }

    public function enqueue_public(): void {
        wp_enqueue_style(
            'ecolepedia-marketplace-public',
            ECOLEPEDIA_MARKETPLACE_URL . 'assets/css/public.css',
            [],
            ECOLEPEDIA_MARKETPLACE_VERSION
        );
        $settings = Settings::get();
        $inline = sprintf(
            ':root{--ecolepedia-primary:%1$s;--ecolepedia-secondary:%2$s;--ecolepedia-bg:%3$s;--ecolepedia-font:%4$s;--ecolepedia-radius:%5$spx;--ecolepedia-shadow:%6$s;}',
            esc_html($settings['accent_color']),
            esc_html($settings['secondary_color']),
            esc_html($settings['background_color']),
            esc_html($settings['font_family']),
            esc_html((string) $settings['radius']),
            esc_html($settings['shadow'])
        );
        wp_add_inline_style('ecolepedia-marketplace-public', $inline);
        wp_enqueue_script(
            'ecolepedia-marketplace-public',
            ECOLEPEDIA_MARKETPLACE_URL . 'assets/js/public.js',
            ['jquery'],
            ECOLEPEDIA_MARKETPLACE_VERSION,
            true
        );
        wp_localize_script('ecolepedia-marketplace-public', 'EcolepediaMarketplace', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ecolepedia_marketplace_nonce'),
        ]);
    }

    public function enqueue_admin(string $hook): void {
        if (strpos($hook, 'ecolepedia-marketplace') === false) {
            return;
        }
        wp_enqueue_style(
            'ecolepedia-marketplace-admin',
            ECOLEPEDIA_MARKETPLACE_URL . 'assets/css/admin.css',
            [],
            ECOLEPEDIA_MARKETPLACE_VERSION
        );
        wp_enqueue_script(
            'ecolepedia-marketplace-admin',
            ECOLEPEDIA_MARKETPLACE_URL . 'assets/js/admin.js',
            ['jquery'],
            ECOLEPEDIA_MARKETPLACE_VERSION,
            true
        );
    }
}
