<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Rate_Limiter {
    public static function allow(string $action): bool {
        $settings = Settings::get();
        $limit = (int) ($settings['rate_limits']['forms_per_hour'] ?? 10);
        $ip = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $key = 'ecolepedia_rate_' . md5($action . $ip);
        $count = (int) get_transient($key);

        if ($count >= $limit) {
            return false;
        }

        set_transient($key, $count + 1, HOUR_IN_SECONDS);
        return true;
    }
}
