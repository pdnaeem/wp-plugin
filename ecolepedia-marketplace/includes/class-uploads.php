<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Uploads {
    public function register(): void {
        add_filter('upload_dir', [$this, 'filter_upload_dir']);
    }

    public function filter_upload_dir(array $dirs): array {
        if (!isset($_POST['ecolepedia_upload'])) {
            return $dirs;
        }

        $subdir = '/' . ECOLEPEDIA_MARKETPLACE_UPLOAD_DIR;
        $dirs['subdir'] = $subdir;
        $dirs['path'] = $dirs['basedir'] . $subdir;
        $dirs['url'] = $dirs['baseurl'] . $subdir;
        return $dirs;
    }

    public static function validate_file(array $file): bool {
        $settings = Settings::get();
        $max_mb = (int) ($settings['file_max_mb'] ?? 20);
        $allowed = $settings['file_types'] ?? [];

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $size_mb = isset($file['size']) ? ($file['size'] / (1024 * 1024)) : 0;

        if (!in_array($ext, $allowed, true)) {
            return false;
        }

        if ($size_mb > $max_mb) {
            return false;
        }

        return true;
    }
}
