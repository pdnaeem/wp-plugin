<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Settings {
    public const OPTION_KEY = 'ecolepedia_marketplace_settings';

    public function register(): void {
        add_action('admin_init', [__CLASS__, 'register_settings']);
        add_action('update_option_' . self::OPTION_KEY, [__CLASS__, 'sync_taxonomies'], 10, 2);
    }

    public static function defaults(): array {
        return [
            'brand_name' => 'Ecolepedia',
            'accent_color' => '#3558F4',
            'secondary_color' => '#10162F',
            'background_color' => '#F5F7FB',
            'font_family' => 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
            'radius' => 14,
            'shadow' => '0 12px 30px rgba(19, 33, 68, 0.08)',
            'logo_id' => 0,
            'button_style' => 'pill',
            'file_max_mb' => 20,
            'file_types' => ['pdf', 'doc', 'docx', 'txt', 'xlsx', 'ppt', 'pptx', 'jpg', 'png'],
            'rate_limits' => [
                'forms_per_hour' => 10,
            ],
            'auto_approve_days' => 3,
            'revision_limit' => 2,
            'order_cleanup_days' => 7,
            'tax_rate' => 0,
            'currency' => 'USD',
            'base_rate' => 12,
            'commission_percent' => 20,
            'stripe_mode' => 'test',
            'stripe_publishable_key' => '',
            'stripe_secret_key' => '',
            'paypal_client_id' => '',
            'paypal_secret_key' => '',
            'subjects_list' => '',
            'document_types_list' => '',
            'sync_taxonomies' => false,
            'email_templates' => [
                'order_created' => __('Hi {{customer_name}}, your order {{order_id}} has been created.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'author_assigned' => __('Your order {{order_id}} has been assigned to {{author_name}}.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
                'message_received' => __('You received a new message on order {{order_id}}.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            ],
            'pages' => [
                'ecolepedia_customer_register' => 0,
                'ecolepedia_author_register' => 0,
                'ecolepedia_login' => 0,
                'ecolepedia_place_order' => 0,
                'ecolepedia_checkout' => 0,
                'ecolepedia_customer_dashboard' => 0,
                'ecolepedia_author_dashboard' => 0,
                'ecolepedia_how_it_works' => 0,
                'ecolepedia_pricing' => 0,
            ],
        ];
    }

    public static function get(): array {
        return wp_parse_args(get_option(self::OPTION_KEY, []), self::defaults());
    }

    public static function register_settings(): void {
        register_setting('ecolepedia_marketplace_settings', self::OPTION_KEY, [
            'sanitize_callback' => [__CLASS__, 'sanitize'],
            'default' => self::defaults(),
        ]);

        add_settings_section('ecolepedia_marketplace_general', __('General', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-general');
        add_settings_field('brand_name', __('Brand Name', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_brand_name'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('logo_id', __('Logo Attachment ID', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_logo_id'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('accent_color', __('Accent Color', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_accent_color'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('secondary_color', __('Secondary Color', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_secondary_color'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('background_color', __('Background Color', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_background_color'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('font_family', __('Font Family', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_font_family'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('radius', __('Corner Radius (px)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_radius'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');
        add_settings_field('shadow', __('Card Shadow', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_shadow'], 'ecolepedia-marketplace-general', 'ecolepedia_marketplace_general');

        add_settings_section('ecolepedia_marketplace_pages', __('Pages', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-pages');
        add_settings_field('page_customer_register', __('Customer Register Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_customer_register'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_author_register', __('Author Register Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_author_register'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_login', __('Login Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_login'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_place_order', __('Place Order Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_place_order'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_checkout', __('Checkout Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_checkout'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_customer_dashboard', __('Customer Dashboard Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_customer_dashboard'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_author_dashboard', __('Author Dashboard Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_author_dashboard'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_how_it_works', __('How It Works Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_how_it_works'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');
        add_settings_field('page_pricing', __('Pricing Page', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_page_pricing'], 'ecolepedia-marketplace-pages', 'ecolepedia_marketplace_pages');

        add_settings_section('ecolepedia_marketplace_catalog', __('Catalog', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-catalog');
        add_settings_field('subjects_list', __('Subjects (one per line)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_subjects_list'], 'ecolepedia-marketplace-catalog', 'ecolepedia_marketplace_catalog');
        add_settings_field('document_types_list', __('Document Types (one per line)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_document_types_list'], 'ecolepedia-marketplace-catalog', 'ecolepedia_marketplace_catalog');
        add_settings_field('sync_taxonomies', __('Sync Taxonomies from List', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_sync_taxonomies'], 'ecolepedia-marketplace-catalog', 'ecolepedia_marketplace_catalog');

        add_settings_section('ecolepedia_marketplace_orders', __('Orders', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-orders');
        add_settings_field('revision_limit', __('Revision Limit', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_revision_limit'], 'ecolepedia-marketplace-orders', 'ecolepedia_marketplace_orders');
        add_settings_field('auto_approve_days', __('Auto-Approve Days', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_auto_approve_days'], 'ecolepedia-marketplace-orders', 'ecolepedia_marketplace_orders');
        add_settings_field('order_cleanup_days', __('Draft Cleanup Days', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_order_cleanup_days'], 'ecolepedia-marketplace-orders', 'ecolepedia_marketplace_orders');

        add_settings_section('ecolepedia_marketplace_pricing', __('Pricing', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-pricing');
        add_settings_field('base_rate', __('Base Rate (per page)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_base_rate'], 'ecolepedia-marketplace-pricing', 'ecolepedia_marketplace_pricing');
        add_settings_field('tax_rate', __('Tax Rate (%)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_tax_rate'], 'ecolepedia-marketplace-pricing', 'ecolepedia_marketplace_pricing');
        add_settings_field('currency', __('Currency', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_currency'], 'ecolepedia-marketplace-pricing', 'ecolepedia_marketplace_pricing');

        add_settings_section('ecolepedia_marketplace_payments', __('Payments', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-payments');
        add_settings_field('stripe_mode', __('Stripe Mode', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_stripe_mode'], 'ecolepedia-marketplace-payments', 'ecolepedia_marketplace_payments');
        add_settings_field('stripe_publishable_key', __('Stripe Publishable Key', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_stripe_publishable_key'], 'ecolepedia-marketplace-payments', 'ecolepedia_marketplace_payments');
        add_settings_field('stripe_secret_key', __('Stripe Secret Key', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_stripe_secret_key'], 'ecolepedia-marketplace-payments', 'ecolepedia_marketplace_payments');
        add_settings_field('paypal_client_id', __('PayPal Client ID', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_paypal_client_id'], 'ecolepedia-marketplace-payments', 'ecolepedia_marketplace_payments');
        add_settings_field('paypal_secret_key', __('PayPal Secret Key', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_paypal_secret_key'], 'ecolepedia-marketplace-payments', 'ecolepedia_marketplace_payments');

        add_settings_section('ecolepedia_marketplace_commissions', __('Commissions', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-commissions');
        add_settings_field('commission_percent', __('Platform Commission (%)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_commission_percent'], 'ecolepedia-marketplace-commissions', 'ecolepedia_marketplace_commissions');

        add_settings_section('ecolepedia_marketplace_security', __('Security', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-security');
        add_settings_field('file_max_mb', __('Max Upload Size (MB)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_file_max'], 'ecolepedia-marketplace-security', 'ecolepedia_marketplace_security');
        add_settings_field('file_types', __('Allowed File Types', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_file_types'], 'ecolepedia-marketplace-security', 'ecolepedia_marketplace_security');
        add_settings_field('rate_limits', __('Form Rate Limit (per hour)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_rate_limits'], 'ecolepedia-marketplace-security', 'ecolepedia_marketplace_security');

        add_settings_section('ecolepedia_marketplace_emails', __('Email Templates', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace-emails');
        add_settings_field('email_order_created', __('Order Created Email', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_email_order_created'], 'ecolepedia-marketplace-emails', 'ecolepedia_marketplace_emails');
        add_settings_field('email_author_assigned', __('Author Assigned Email', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_email_author_assigned'], 'ecolepedia-marketplace-emails', 'ecolepedia_marketplace_emails');
        add_settings_field('email_message_received', __('Message Received Email', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_email_message_received'], 'ecolepedia-marketplace-emails', 'ecolepedia_marketplace_emails');
    }

    public static function sanitize(array $input): array {
        $defaults = self::defaults();
        $output = [];

        $output['brand_name'] = sanitize_text_field($input['brand_name'] ?? $defaults['brand_name']);
        $output['accent_color'] = sanitize_hex_color($input['accent_color'] ?? $defaults['accent_color']) ?: $defaults['accent_color'];
        $output['secondary_color'] = sanitize_hex_color($input['secondary_color'] ?? $defaults['secondary_color']) ?: $defaults['secondary_color'];
        $output['background_color'] = sanitize_hex_color($input['background_color'] ?? $defaults['background_color']) ?: $defaults['background_color'];
        $output['font_family'] = sanitize_text_field($input['font_family'] ?? $defaults['font_family']);
        $output['radius'] = max(0, absint($input['radius'] ?? $defaults['radius']));
        $output['shadow'] = sanitize_text_field($input['shadow'] ?? $defaults['shadow']);
        $output['logo_id'] = absint($input['logo_id'] ?? 0);
        $output['button_style'] = sanitize_text_field($input['button_style'] ?? $defaults['button_style']);
        $output['file_max_mb'] = max(1, absint($input['file_max_mb'] ?? $defaults['file_max_mb']));
        $file_types = $input['file_types'] ?? $defaults['file_types'];
        $file_types = array_filter(array_map('sanitize_text_field', is_array($file_types) ? $file_types : explode(',', (string) $file_types)));
        $output['file_types'] = array_values(array_unique($file_types));
        $output['rate_limits'] = [
            'forms_per_hour' => max(1, absint($input['rate_limits']['forms_per_hour'] ?? $defaults['rate_limits']['forms_per_hour'])),
        ];
        $output['auto_approve_days'] = max(1, absint($input['auto_approve_days'] ?? $defaults['auto_approve_days']));
        $output['revision_limit'] = max(0, absint($input['revision_limit'] ?? $defaults['revision_limit']));
        $output['order_cleanup_days'] = max(1, absint($input['order_cleanup_days'] ?? $defaults['order_cleanup_days']));
        $output['tax_rate'] = max(0, floatval($input['tax_rate'] ?? $defaults['tax_rate']));
        $output['currency'] = sanitize_text_field($input['currency'] ?? $defaults['currency']);
        $output['base_rate'] = max(0, floatval($input['base_rate'] ?? $defaults['base_rate']));
        $output['commission_percent'] = max(0, floatval($input['commission_percent'] ?? $defaults['commission_percent']));
        $output['stripe_mode'] = in_array($input['stripe_mode'] ?? $defaults['stripe_mode'], ['test', 'live'], true) ? $input['stripe_mode'] : $defaults['stripe_mode'];
        $output['stripe_publishable_key'] = sanitize_text_field($input['stripe_publishable_key'] ?? $defaults['stripe_publishable_key']);
        $output['stripe_secret_key'] = sanitize_text_field($input['stripe_secret_key'] ?? $defaults['stripe_secret_key']);
        $output['paypal_client_id'] = sanitize_text_field($input['paypal_client_id'] ?? $defaults['paypal_client_id']);
        $output['paypal_secret_key'] = sanitize_text_field($input['paypal_secret_key'] ?? $defaults['paypal_secret_key']);
        $output['subjects_list'] = wp_kses_post($input['subjects_list'] ?? $defaults['subjects_list']);
        $output['document_types_list'] = wp_kses_post($input['document_types_list'] ?? $defaults['document_types_list']);
        $output['sync_taxonomies'] = !empty($input['sync_taxonomies']);
        $output['email_templates'] = [
            'order_created' => wp_kses_post($input['email_templates']['order_created'] ?? $defaults['email_templates']['order_created']),
            'author_assigned' => wp_kses_post($input['email_templates']['author_assigned'] ?? $defaults['email_templates']['author_assigned']),
            'message_received' => wp_kses_post($input['email_templates']['message_received'] ?? $defaults['email_templates']['message_received']),
        ];
        $pages = $input['pages'] ?? $defaults['pages'];
        $output['pages'] = array_map('absint', $pages);

        return $output;
    }

    public static function sync_taxonomies(array $old_value, array $value): void {
        if (empty($value['sync_taxonomies'])) {
            return;
        }
        Orders::sync_taxonomies($value);
    }

    public static function field_brand_name(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[brand_name]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['brand_name'])
        );
    }

    public static function field_logo_id(): void {
        $options = self::get();
        printf(
            '<input type="number" min="0" name="%s[logo_id]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['logo_id']
        );
    }

    public static function field_accent_color(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[accent_color]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['accent_color'])
        );
    }

    public static function field_secondary_color(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[secondary_color]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['secondary_color'])
        );
    }

    public static function field_background_color(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[background_color]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['background_color'])
        );
    }

    public static function field_font_family(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[font_family]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['font_family'])
        );
    }

    public static function field_radius(): void {
        $options = self::get();
        printf(
            '<input type="number" min="0" name="%s[radius]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['radius']
        );
    }

    public static function field_shadow(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[shadow]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['shadow'])
        );
    }

    private static function render_page_dropdown(string $name, int $selected): void {
        wp_dropdown_pages([
            'name' => sprintf('%s[pages][%s]', self::OPTION_KEY, $name),
            'selected' => $selected,
            'show_option_none' => __('— Select —', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
        ]);
    }

    public static function field_page_customer_register(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_customer_register', (int) $options['pages']['ecolepedia_customer_register']);
    }

    public static function field_page_author_register(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_author_register', (int) $options['pages']['ecolepedia_author_register']);
    }

    public static function field_page_login(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_login', (int) $options['pages']['ecolepedia_login']);
    }

    public static function field_page_place_order(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_place_order', (int) $options['pages']['ecolepedia_place_order']);
    }

    public static function field_page_checkout(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_checkout', (int) $options['pages']['ecolepedia_checkout']);
    }

    public static function field_page_customer_dashboard(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_customer_dashboard', (int) $options['pages']['ecolepedia_customer_dashboard']);
    }

    public static function field_page_author_dashboard(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_author_dashboard', (int) $options['pages']['ecolepedia_author_dashboard']);
    }

    public static function field_page_how_it_works(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_how_it_works', (int) $options['pages']['ecolepedia_how_it_works']);
    }

    public static function field_page_pricing(): void {
        $options = self::get();
        self::render_page_dropdown('ecolepedia_pricing', (int) $options['pages']['ecolepedia_pricing']);
    }

    public static function field_subjects_list(): void {
        $options = self::get();
        printf(
            '<textarea name="%s[subjects_list]" class="large-text" rows="6">%s</textarea>',
            esc_attr(self::OPTION_KEY),
            esc_textarea($options['subjects_list'])
        );
    }

    public static function field_document_types_list(): void {
        $options = self::get();
        printf(
            '<textarea name="%s[document_types_list]" class="large-text" rows="6">%s</textarea>',
            esc_attr(self::OPTION_KEY),
            esc_textarea($options['document_types_list'])
        );
    }

    public static function field_sync_taxonomies(): void {
        $options = self::get();
        printf(
            '<label><input type="checkbox" name="%s[sync_taxonomies]" value="1" %s /> %s</label><p class="description">%s</p>',
            esc_attr(self::OPTION_KEY),
            checked(!empty($options['sync_taxonomies']), true, false),
            esc_html__('Update taxonomies from the lists above when saving settings.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            esc_html__('Existing terms remain; new ones are added.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN)
        );
    }

    public static function field_file_max(): void {
        $options = self::get();
        printf(
            '<input type="number" min="1" name="%s[file_max_mb]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['file_max_mb']
        );
    }

    public static function field_file_types(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[file_types]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr(implode(',', $options['file_types']))
        );
    }

    public static function field_rate_limits(): void {
        $options = self::get();
        printf(
            '<input type="number" min="1" name="%s[rate_limits][forms_per_hour]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['rate_limits']['forms_per_hour']
        );
    }

    public static function field_revision_limit(): void {
        $options = self::get();
        printf(
            '<input type="number" min="0" name="%s[revision_limit]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['revision_limit']
        );
    }

    public static function field_auto_approve_days(): void {
        $options = self::get();
        printf(
            '<input type="number" min="1" name="%s[auto_approve_days]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['auto_approve_days']
        );
    }

    public static function field_order_cleanup_days(): void {
        $options = self::get();
        printf(
            '<input type="number" min="1" name="%s[order_cleanup_days]" value="%d" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            (int) $options['order_cleanup_days']
        );
    }

    public static function field_tax_rate(): void {
        $options = self::get();
        printf(
            '<input type="number" step="0.1" min="0" name="%s[tax_rate]" value="%s" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr((string) $options['tax_rate'])
        );
    }

    public static function field_currency(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[currency]" value="%s" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['currency'])
        );
    }

    public static function field_base_rate(): void {
        $options = self::get();
        printf(
            '<input type="number" step="0.01" min="0" name="%s[base_rate]" value="%s" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr((string) $options['base_rate'])
        );
    }

    public static function field_commission_percent(): void {
        $options = self::get();
        printf(
            '<input type="number" step="0.1" min="0" name="%s[commission_percent]" value="%s" class="small-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr((string) $options['commission_percent'])
        );
    }

    public static function field_stripe_mode(): void {
        $options = self::get();
        $value = $options['stripe_mode'];
        printf(
            '<select name="%s[stripe_mode]"><option value="test" %s>%s</option><option value="live" %s>%s</option></select>',
            esc_attr(self::OPTION_KEY),
            selected($value, 'test', false),
            esc_html__('Test', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN),
            selected($value, 'live', false),
            esc_html__('Live', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN)
        );
    }

    public static function field_stripe_publishable_key(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[stripe_publishable_key]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['stripe_publishable_key'])
        );
    }

    public static function field_stripe_secret_key(): void {
        $options = self::get();
        printf(
            '<input type="password" name="%s[stripe_secret_key]" value="%s" class="regular-text" autocomplete="off" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['stripe_secret_key'])
        );
    }

    public static function field_paypal_client_id(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[paypal_client_id]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['paypal_client_id'])
        );
    }

    public static function field_paypal_secret_key(): void {
        $options = self::get();
        printf(
            '<input type="password" name="%s[paypal_secret_key]" value="%s" class="regular-text" autocomplete="off" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['paypal_secret_key'])
        );
    }

    public static function field_email_order_created(): void {
        $options = self::get();
        printf(
            '<textarea name="%s[email_templates][order_created]" class="large-text" rows="4">%s</textarea>',
            esc_attr(self::OPTION_KEY),
            esc_textarea($options['email_templates']['order_created'])
        );
    }

    public static function field_email_author_assigned(): void {
        $options = self::get();
        printf(
            '<textarea name="%s[email_templates][author_assigned]" class="large-text" rows="4">%s</textarea>',
            esc_attr(self::OPTION_KEY),
            esc_textarea($options['email_templates']['author_assigned'])
        );
    }

    public static function field_email_message_received(): void {
        $options = self::get();
        printf(
            '<textarea name="%s[email_templates][message_received]" class="large-text" rows="4">%s</textarea>',
            esc_attr(self::OPTION_KEY),
            esc_textarea($options['email_templates']['message_received'])
        );
    }
}
