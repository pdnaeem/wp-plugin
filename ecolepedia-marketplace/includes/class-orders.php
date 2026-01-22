<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Orders {
    public function register(): void {
        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('init', [__CLASS__, 'register_taxonomies']);
    }

    public static function register_post_type(): void {
        $labels = [
            'name' => __('Orders', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'singular_name' => __('Order', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'add_new' => __('Add New', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'add_new_item' => __('Add New Order', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'edit_item' => __('Edit Order', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'new_item' => __('New Order', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'view_item' => __('View Order', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'search_items' => __('Search Orders', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'not_found' => __('No orders found.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'menu_name' => __('Ecolepedia Orders', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
        ];

        register_post_type('ecolepedia_order', [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-portfolio',
            'supports' => ['title', 'editor', 'author', 'custom-fields'],
            'capability_type' => ['ecolepedia_order', 'ecolepedia_orders'],
            'map_meta_cap' => true,
            'show_in_menu' => false,
        ]);
    }

    public static function register_taxonomies(): void {
        register_taxonomy('ecolepedia_subject', 'ecolepedia_order', [
            'labels' => [
                'name' => __('Subjects', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'singular_name' => __('Subject', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'hierarchical' => true,
        ]);

        register_taxonomy('ecolepedia_document_type', 'ecolepedia_order', [
            'labels' => [
                'name' => __('Document Types', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'singular_name' => __('Document Type', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'hierarchical' => true,
        ]);
    }

    public static function get_statuses(): array {
        return [
            'draft' => __('Draft', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'awaiting_payment' => __('Awaiting Payment', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'paid' => __('Paid', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'in_progress' => __('In Progress', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'draft_submitted' => __('Draft Submitted', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'revision_requested' => __('Revision Requested', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'final_submitted' => __('Final Submitted', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'approved' => __('Approved', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'completed' => __('Completed', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'cancelled' => __('Cancelled', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'refunded' => __('Refunded', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            'disputed' => __('Disputed', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
        ];
    }
}
