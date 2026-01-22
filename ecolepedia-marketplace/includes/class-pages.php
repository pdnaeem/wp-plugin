<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Pages {
    public static function create_required_pages(): void {
        self::create_pages();
    }

    public static function maybe_create_required_pages(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        $options = Settings::get();
        $pages = $options['pages'] ?? [];
        $missing = false;
        foreach ($pages as $page_id) {
            if (empty($page_id) || get_post_status($page_id) === false) {
                $missing = true;
                break;
            }
        }
        if ($missing) {
            self::create_pages();
        }
    }

    private static function create_pages(): void {
        $pages = [
            'ecolepedia_customer_register' => [
                'title' => __('Customer Registration', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_customer_register]',
            ],
            'ecolepedia_author_register' => [
                'title' => __('Author Registration', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_author_register]',
            ],
            'ecolepedia_login' => [
                'title' => __('Login', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_login]',
            ],
            'ecolepedia_place_order' => [
                'title' => __('Place Order', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_place_order]',
            ],
            'ecolepedia_checkout' => [
                'title' => __('Checkout', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_checkout]',
            ],
            'ecolepedia_customer_dashboard' => [
                'title' => __('Customer Dashboard', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_customer_dashboard]',
            ],
            'ecolepedia_author_dashboard' => [
                'title' => __('Author Dashboard', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_author_dashboard]',
            ],
            'ecolepedia_how_it_works' => [
                'title' => __('How It Works', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_how_it_works]',
            ],
            'ecolepedia_pricing' => [
                'title' => __('Pricing', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'shortcode' => '[ecolepedia_pricing_table]',
            ],
        ];

        $options = Settings::get();
        foreach ($pages as $key => $page) {
            if (!empty($options['pages'][$key])) {
                continue;
            }
            $existing = get_page_by_title($page['title']);
            if ($existing) {
                $options['pages'][$key] = $existing->ID;
                continue;
            }
            $page_id = wp_insert_post([
                'post_title' => $page['title'],
                'post_content' => $page['shortcode'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 0,
            ]);
            if ($page_id && !is_wp_error($page_id)) {
                $options['pages'][$key] = $page_id;
            }
        }

        update_option(Settings::OPTION_KEY, $options);
    }
}
