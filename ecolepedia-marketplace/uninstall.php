<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$delete = get_option('ecolepedia_marketplace_delete_data', false);
if (!$delete) {
    return;
}

global $wpdb;

$tables = [
    $wpdb->prefix . 'ecolepedia_messages',
    $wpdb->prefix . 'ecolepedia_transactions',
    $wpdb->prefix . 'ecolepedia_revisions',
    $wpdb->prefix . 'ecolepedia_author_profiles',
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$table}");
}

delete_option('ecolepedia_marketplace_settings');
