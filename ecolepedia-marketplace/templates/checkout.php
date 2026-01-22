<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-container">
        <div class="ecolepedia-header">
            <div>
                <h2><?php echo esc_html__('Checkout', 'ecolepedia-marketplace'); ?></h2>
                <p class="ecolepedia-muted"><?php echo esc_html__('Secure payments and clear cost breakdown before you confirm.', 'ecolepedia-marketplace'); ?></p>
            </div>
        </div>
        <div class="ecolepedia-grid">
            <div class="ecolepedia-card">
                <h3><?php echo esc_html__('Order Summary', 'ecolepedia-marketplace'); ?></h3>
                <ul class="ecolepedia-list">
                    <li><?php echo esc_html__('Base price: $120', 'ecolepedia-marketplace'); ?></li>
                    <li><?php echo esc_html__('Add-ons: $20', 'ecolepedia-marketplace'); ?></li>
                    <li><?php echo esc_html__('Taxes: $0', 'ecolepedia-marketplace'); ?></li>
                    <li><strong><?php echo esc_html__('Total: $140', 'ecolepedia-marketplace'); ?></strong></li>
                </ul>
            </div>
            <div class="ecolepedia-card accent">
                <h3><?php echo esc_html__('Payment Method', 'ecolepedia-marketplace'); ?></h3>
                <p class="ecolepedia-muted"><?php echo esc_html__('Configure gateways in Settings > Payments to enable Stripe or PayPal.', 'ecolepedia-marketplace'); ?></p>
                <button class="ecolepedia-button" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                    <?php echo esc_html__('Pay Now', 'ecolepedia-marketplace'); ?>
                </button>
                <p class="ecolepedia-muted"><?php echo esc_html__('Your payment is held securely until delivery is approved.', 'ecolepedia-marketplace'); ?></p>
            </div>
        </div>
    </div>
</div>
