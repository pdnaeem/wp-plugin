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
        $settings = Settings::get();
        self::sync_taxonomies($settings);
    }

    public static function sync_taxonomies(array $settings): void {
        $subjects = self::parse_list($settings['subjects_list'] ?? '');
        if (empty($subjects)) {
            $subjects = self::default_subjects();
        }
        $document_types = self::parse_list($settings['document_types_list'] ?? '');
        if (empty($document_types)) {
            $document_types = self::default_document_types();
        }

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

    private static function parse_list(string $raw): array {
        $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw)));
        return array_values(array_unique($lines));
    }

    private static function default_subjects(): array {
        return [
            'Accounting', 'Aerospace Engineering', 'Anthropology', 'Architecture', 'Art History', 'Biology',
            'Biomedical Engineering', 'Business', 'Chemistry', 'Civil Engineering', 'Communications',
            'Computer Science', 'Creative Writing', 'Criminal Justice', 'Cybersecurity', 'Data Science',
            'Dentistry', 'Economics', 'Education', 'Electrical Engineering', 'Engineering', 'Environmental Science',
            'Ethics', 'Finance', 'Geography', 'Healthcare', 'History', 'Hospitality', 'Human Resources',
            'International Relations', 'Journalism', 'Law', 'Literature', 'Management', 'Marketing',
            'Mathematics', 'Mechanical Engineering', 'Medicine', 'Music', 'Nursing', 'Philosophy', 'Physics',
            'Political Science', 'Psychology', 'Public Health', 'Public Policy', 'Sociology', 'Statistics',
            'Technology', 'Theology', 'Tourism', 'Visual Arts'
        ];
    }

    private static function default_document_types(): array {
        return [
            'Admission Essay', 'Annotated Bibliography', 'Article', 'Article Review', 'Book Review',
            'Business Plan', 'Capstone Project', 'Case Study', 'Coursework', 'Cover Letter',
            'Creative Writing', 'Dissertation', 'Essay', 'Lab Report', 'Literature Review',
            'Personal Statement', 'Presentation', 'Proposal', 'Research Paper', 'Report',
            'Resume', 'Speech', 'Term Paper', 'Thesis', 'White Paper'
        ];
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
