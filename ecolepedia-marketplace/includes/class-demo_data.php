<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Demo_Data {
    public function register(): void {
        // Hook placeholder for future features.
    }

    public static function generate(): void {
        Orders::seed_taxonomies();

        $author_id = username_exists('demo_author');
        if (!$author_id) {
            $author_id = wp_create_user('demo_author', wp_generate_password(), 'author@ecolepedia.test');
            if (!is_wp_error($author_id)) {
                $user = get_user_by('id', $author_id);
                if ($user) {
                    $user->set_role('ecolepedia_author');
                }
            }
        }

        $customer_id = username_exists('demo_customer');
        if (!$customer_id) {
            $customer_id = wp_create_user('demo_customer', wp_generate_password(), 'customer@ecolepedia.test');
            if (!is_wp_error($customer_id)) {
                $user = get_user_by('id', $customer_id);
                if ($user) {
                    $user->set_role('ecolepedia_customer');
                }
            }
        }

        $order_id = wp_insert_post([
            'post_type' => 'ecolepedia_order',
            'post_status' => 'publish',
            'post_title' => 'Sample Order: Market Analysis',
            'post_content' => 'Analyze the trends in the educational services market.',
            'post_author' => $customer_id,
        ]);

        if ($order_id && !is_wp_error($order_id)) {
            wp_set_object_terms($order_id, 'Business', 'ecolepedia_subject');
            wp_set_object_terms($order_id, 'Research Paper', 'ecolepedia_document_type');
            update_post_meta($order_id, '_ecolepedia_status', 'in_progress');
            update_post_meta($order_id, '_ecolepedia_assigned_author', $author_id);
        }
    }
}
