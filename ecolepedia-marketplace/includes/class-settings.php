<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Settings {
    public const OPTION_KEY = 'ecolepedia_marketplace_settings';

    public function register(): void {
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function defaults(): array {
        return [
            'brand_name' => 'Ecolepedia',
            'accent_color' => '#3558F4',
            'logo_id' => 0,
            'file_max_mb' => 20,
            'file_types' => ['pdf', 'doc', 'docx', 'txt', 'xlsx', 'ppt', 'pptx', 'jpg', 'png'],
            'rate_limits' => [
                'forms_per_hour' => 10,
            ],
            'auto_approve_days' => 3,
            'revision_limit' => 2,
            'tax_rate' => 0,
            'currency' => 'USD',
            'stripe_mode' => 'test',
            'stripe_publishable_key' => '',
            'stripe_secret_key' => '',
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

        add_settings_section('ecolepedia_marketplace_general', __('General', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), '__return_false', 'ecolepedia-marketplace');
        add_settings_field('brand_name', __('Brand Name', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_brand_name'], 'ecolepedia-marketplace', 'ecolepedia_marketplace_general');
        add_settings_field('accent_color', __('Accent Color', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_accent_color'], 'ecolepedia-marketplace', 'ecolepedia_marketplace_general');
        add_settings_field('file_max_mb', __('Max Upload Size (MB)', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_file_max'], 'ecolepedia-marketplace', 'ecolepedia_marketplace_general');
        add_settings_field('file_types', __('Allowed File Types', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [__CLASS__, 'field_file_types'], 'ecolepedia-marketplace', 'ecolepedia_marketplace_general');
    }

    public static function sanitize(array $input): array {
        $defaults = self::defaults();
        $output = [];

        $output['brand_name'] = sanitize_text_field($input['brand_name'] ?? $defaults['brand_name']);
        $output['accent_color'] = sanitize_hex_color($input['accent_color'] ?? $defaults['accent_color']) ?: $defaults['accent_color'];
        $output['logo_id'] = absint($input['logo_id'] ?? 0);
        $output['file_max_mb'] = max(1, absint($input['file_max_mb'] ?? $defaults['file_max_mb']));
        $file_types = $input['file_types'] ?? $defaults['file_types'];
        $file_types = array_filter(array_map('sanitize_text_field', is_array($file_types) ? $file_types : explode(',', (string) $file_types)));
        $output['file_types'] = array_values(array_unique($file_types));
        $output['rate_limits'] = [
            'forms_per_hour' => max(1, absint($input['rate_limits']['forms_per_hour'] ?? $defaults['rate_limits']['forms_per_hour'])),
        ];
        $output['auto_approve_days'] = max(1, absint($input['auto_approve_days'] ?? $defaults['auto_approve_days']));
        $output['revision_limit'] = max(0, absint($input['revision_limit'] ?? $defaults['revision_limit']));
        $output['tax_rate'] = max(0, floatval($input['tax_rate'] ?? $defaults['tax_rate']));
        $output['currency'] = sanitize_text_field($input['currency'] ?? $defaults['currency']);
        $output['stripe_mode'] = in_array($input['stripe_mode'] ?? $defaults['stripe_mode'], ['test', 'live'], true) ? $input['stripe_mode'] : $defaults['stripe_mode'];
        $output['stripe_publishable_key'] = sanitize_text_field($input['stripe_publishable_key'] ?? $defaults['stripe_publishable_key']);
        $output['stripe_secret_key'] = sanitize_text_field($input['stripe_secret_key'] ?? $defaults['stripe_secret_key']);

        return $output;
    }

    public static function field_brand_name(): void {
        $options = self::get();
        printf(
            '<input type="text" name="%s[brand_name]" value="%s" class="regular-text" />',
            esc_attr(self::OPTION_KEY),
            esc_attr($options['brand_name'])
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
        echo '<p class="description">' . esc_html__('Comma-separated list of extensions.', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN) . '</p>';
    }
}
