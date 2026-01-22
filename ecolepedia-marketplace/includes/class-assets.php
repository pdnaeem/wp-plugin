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
