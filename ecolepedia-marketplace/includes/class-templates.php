<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Templates {
    public static function locate(string $template): string {
        $theme_path = locate_template('ecolepedia-marketplace/' . $template);
        if ($theme_path) {
            return $theme_path;
        }
        return ECOLEPEDIA_MARKETPLACE_PATH . 'templates/' . $template;
    }

    public static function render(string $template, array $data = []): string {
        $path = self::locate($template);
        if (!file_exists($path)) {
            return '';
        }
        ob_start();
        $data = $data;
        include $path;
        return ob_get_clean();
    }
}
