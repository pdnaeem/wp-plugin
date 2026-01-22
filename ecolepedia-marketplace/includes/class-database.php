<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Database {
    public static function install(): void {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $messages = $wpdb->prefix . ECOLEPEDIA_MARKETPLACE_TABLE_MESSAGES;
        $transactions = $wpdb->prefix . ECOLEPEDIA_MARKETPLACE_TABLE_TRANSACTIONS;
        $revisions = $wpdb->prefix . ECOLEPEDIA_MARKETPLACE_TABLE_REVISIONS;
        $profiles = $wpdb->prefix . ECOLEPEDIA_MARKETPLACE_TABLE_AUTHOR_PROFILES;

        $sql = "CREATE TABLE {$messages} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            thread_id VARCHAR(64) NOT NULL,
            sender_id BIGINT UNSIGNED NOT NULL,
            receiver_id BIGINT UNSIGNED NOT NULL,
            message TEXT NOT NULL,
            attachment_url TEXT NULL,
            created_at DATETIME NOT NULL,
            read_at DATETIME NULL,
            PRIMARY KEY  (id),
            KEY order_id (order_id),
            KEY thread_id (thread_id)
        ) {$charset};

        CREATE TABLE {$transactions} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            customer_id BIGINT UNSIGNED NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            currency VARCHAR(8) NOT NULL,
            gateway VARCHAR(64) NOT NULL,
            status VARCHAR(32) NOT NULL,
            fees DECIMAL(12,2) NOT NULL DEFAULT 0,
            commission DECIMAL(12,2) NOT NULL DEFAULT 0,
            author_earning DECIMAL(12,2) NOT NULL DEFAULT 0,
            platform_earning DECIMAL(12,2) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY  (id),
            KEY order_id (order_id),
            KEY customer_id (customer_id)
        ) {$charset};

        CREATE TABLE {$revisions} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            requested_by BIGINT UNSIGNED NOT NULL,
            request_text TEXT NOT NULL,
            status VARCHAR(32) NOT NULL,
            author_response TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY  (id),
            KEY order_id (order_id)
        ) {$charset};

        CREATE TABLE {$profiles} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            bio TEXT NULL,
            avatar_url TEXT NULL,
            education TEXT NULL,
            expertise TEXT NULL,
            languages TEXT NULL,
            writer_level VARCHAR(64) NULL,
            verification_status VARCHAR(32) NOT NULL DEFAULT 'pending',
            rating DECIMAL(3,2) NOT NULL DEFAULT 0,
            completed_orders INT NOT NULL DEFAULT 0,
            on_time_rate DECIMAL(5,2) NOT NULL DEFAULT 0,
            plagiarism_flags INT NOT NULL DEFAULT 0,
            availability_status VARCHAR(32) NOT NULL DEFAULT 'available',
            payout_method VARCHAR(64) NULL,
            payout_details TEXT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) {$charset};";

        dbDelta($sql);
    }
}
