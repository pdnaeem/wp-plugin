<?php
namespace Ecolepedia\Marketplace;

if (!defined('ABSPATH')) {
    exit;
}

class Roles {
    public static function add_roles(): void {
        add_role('ecolepedia_manager', __('Ecolepedia Manager', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [
            'read' => true,
            ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS => true,
            ECOLEPEDIA_MARKETPLACE_CAP_ORDERS => true,
            ECOLEPEDIA_MARKETPLACE_CAP_AUTHORS => true,
            ECOLEPEDIA_MARKETPLACE_CAP_FINANCE => true,
        ]);

        add_role('ecolepedia_author', __('Ecolepedia Author', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [
            'read' => true,
            ECOLEPEDIA_MARKETPLACE_CAP_AUTHOR_VIEW => true,
            ECOLEPEDIA_MARKETPLACE_CAP_AUTHOR_SUBMIT => true,
            ECOLEPEDIA_MARKETPLACE_CAP_AUTHOR_CHAT => true,
        ]);

        add_role('ecolepedia_customer', __('Ecolepedia Customer', ECOLEPEDIA_MARKETPLACE_TEXTDOMAIN), [
            'read' => true,
            ECOLEPEDIA_MARKETPLACE_CAP_CUSTOMER_PLACE => true,
            ECOLEPEDIA_MARKETPLACE_CAP_CUSTOMER_CHAT => true,
            ECOLEPEDIA_MARKETPLACE_CAP_CUSTOMER_DOWNLOAD => true,
        ]);

        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap(ECOLEPEDIA_MARKETPLACE_CAP_SETTINGS);
            $admin->add_cap(ECOLEPEDIA_MARKETPLACE_CAP_ORDERS);
            $admin->add_cap(ECOLEPEDIA_MARKETPLACE_CAP_AUTHORS);
            $admin->add_cap(ECOLEPEDIA_MARKETPLACE_CAP_FINANCE);
        }
    }
}
