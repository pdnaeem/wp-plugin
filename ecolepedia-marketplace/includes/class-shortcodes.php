<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcodes {
    public function register(): void {
        add_action('init', [$this, 'handle_forms']);
        add_shortcode('ecolepedia_customer_register', [$this, 'customer_register']);
        add_shortcode('ecolepedia_author_register', [$this, 'author_register']);
        add_shortcode('ecolepedia_login', [$this, 'login_form']);
        add_shortcode('ecolepedia_place_order', [$this, 'place_order']);
        add_shortcode('ecolepedia_checkout', [$this, 'checkout']);
        add_shortcode('ecolepedia_customer_dashboard', [$this, 'customer_dashboard']);
        add_shortcode('ecolepedia_author_dashboard', [$this, 'author_dashboard']);
        add_shortcode('ecolepedia_order_details', [$this, 'order_details']);
        add_shortcode('ecolepedia_how_it_works', [$this, 'how_it_works']);
        add_shortcode('ecolepedia_pricing_table', [$this, 'pricing_table']);
    }

    public function handle_forms(): void {
        if (empty($_POST['ecolepedia_action'])) {
            return;
        }
        $action = sanitize_text_field(wp_unslash($_POST['ecolepedia_action']));
        $nonce = sanitize_text_field(wp_unslash($_POST['ecolepedia_nonce'] ?? ''));
        if (!wp_verify_nonce($nonce, 'ecolepedia_form_action')) {
            return;
        }

        if (!Rate_Limiter::allow('form_submit')) {
            wp_die(esc_html__('Too many requests. Please try again later.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN));
        }

        if ($action === 'customer_register') {
            $this->process_registration('ecolepedia_customer');
        }

        if ($action === 'author_register') {
            $this->process_registration('ecolepedia_author');
        }
    }

    private function process_registration(string $role): void {
        $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
        $password = wp_unslash($_POST['password'] ?? '');
        $name = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));

        if (empty($email) || empty($password)) {
            wp_die(esc_html__('Email and password are required.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN));
        }

        if (!is_email($email)) {
            wp_die(esc_html__('Please provide a valid email.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN));
        }

        if (email_exists($email)) {
            wp_die(esc_html__('This email is already registered.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN));
        }

        $user_id = wp_create_user($email, $password, $email);
        if (is_wp_error($user_id)) {
            wp_die(esc_html($user_id->get_error_message()));
        }

        wp_update_user([
            'ID' => $user_id,
            'display_name' => $name,
        ]);

        $user = get_user_by('id', $user_id);
        if ($user) {
            $user->set_role($role);
        }

        wp_safe_redirect(home_url());
        exit;
    }

    public function customer_register(): string {
        return Templates::render('customer-register.php');
    }

    public function author_register(): string {
        return Templates::render('author-register.php');
    }

    public function login_form(): string {
        return Templates::render('login.php');
    }

    public function place_order(): string {
        return Templates::render('place-order.php', [
            'statuses' => Orders::get_statuses(),
        ]);
    }

    public function checkout(): string {
        return Templates::render('checkout.php');
    }

    public function customer_dashboard(): string {
        return Templates::render('customer-dashboard.php');
    }

    public function author_dashboard(): string {
        return Templates::render('author-dashboard.php');
    }

    public function order_details(array $atts): string {
        $atts = shortcode_atts(['id' => 0], $atts, 'ecolepedia_order_details');
        return Templates::render('order-details.php', [
            'order_id' => absint($atts['id']),
        ]);
    }

    public function how_it_works(): string {
        return Templates::render('how-it-works.php');
    }

    public function pricing_table(): string {
        return Templates::render('pricing-table.php');
    }
}
