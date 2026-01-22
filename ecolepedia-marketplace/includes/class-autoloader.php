<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Autoloader {
    public static function register(): void {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload(string $class): void {
        if (strpos($class, __NAMESPACE__ . '\\') !== 0) {
            return;
        }

        $relative = strtolower(str_replace(__NAMESPACE__ . '\\', '', $class));
        $relative = str_replace('\\', '-', $relative);
        $file = ECOLEPEDIA_MARKETPLACE_PATH . 'includes/class-' . $relative . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
