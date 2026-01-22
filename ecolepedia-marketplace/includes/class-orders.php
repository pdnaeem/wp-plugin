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

    public static function seed_taxonomies(): void {
        $subjects = [
            'Accounting', 'Architecture', 'Art History', 'Biology', 'Business', 'Chemistry', 'Communications',
            'Computer Science', 'Creative Writing', 'Criminal Justice', 'Economics', 'Education', 'Engineering',
            'Environmental Science', 'Ethics', 'Finance', 'Geography', 'Healthcare', 'History', 'Human Resources',
            'International Relations', 'Journalism', 'Law', 'Literature', 'Management', 'Marketing', 'Mathematics',
            'Medicine', 'Music', 'Nursing', 'Philosophy', 'Physics', 'Political Science', 'Psychology', 'Public Health',
            'Sociology', 'Statistics', 'Technology', 'Theology', 'Tourism'
        ];

        $document_types = [
            'Essay', 'Research Paper', 'Case Study', 'Term Paper', 'Report', 'Thesis', 'Dissertation',
            'Annotated Bibliography', 'Literature Review', 'Lab Report', 'Presentation', 'PowerPoint',
            'Admission Essay', 'Personal Statement', 'Book Review', 'Movie Review', 'Speech', 'Article',
            'Summary', 'Coursework'
        ];

        foreach ($subjects as $subject) {
            if (!term_exists($subject, 'ecolepedia_subject')) {
                wp_insert_term($subject, 'ecolepedia_subject');
            }
        }

        foreach ($document_types as $type) {
            if (!term_exists($type, 'ecolepedia_document_type')) {
                wp_insert_term($type, 'ecolepedia_document_type');
            }
        }
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
