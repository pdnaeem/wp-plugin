<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Cron {
    public function register(): void {
        add_action('init', [$this, 'schedule']);
        add_action('ecolepedia_marketplace_cron_check', [$this, 'run']);
    }

    public function schedule(): void {
        if (!wp_next_scheduled('ecolepedia_marketplace_cron_check')) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'hourly', 'ecolepedia_marketplace_cron_check');
        }
    }

    public function run(): void {
        $cleanup_days = 7;
        $args = [
            'post_type' => 'ecolepedia_order',
            'post_status' => 'draft',
            'date_query' => [
                [
                    'before' => $cleanup_days . ' days ago',
                ],
            ],
            'fields' => 'ids',
        ];
        $orders = get_posts($args);
        foreach ($orders as $order_id) {
            wp_trash_post($order_id);
        }
    }
}
