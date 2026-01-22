<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-card">
        <h2><?php echo esc_html__('Checkout', 'ecolepedia-marketplace'); ?></h2>
        <p><?php echo esc_html__('Secure payment powered by Stripe or PayPal (gateway setup required).', 'ecolepedia-marketplace'); ?></p>
        <div class="ecolepedia-grid">
            <div class="ecolepedia-card">
                <h3><?php echo esc_html__('Order Summary', 'ecolepedia-marketplace'); ?></h3>
                <ul>
                    <li><?php echo esc_html__('Base price: $120', 'ecolepedia-marketplace'); ?></li>
                    <li><?php echo esc_html__('Add-ons: $20', 'ecolepedia-marketplace'); ?></li>
                    <li><?php echo esc_html__('Total: $140', 'ecolepedia-marketplace'); ?></li>
                </ul>
            </div>
            <div class="ecolepedia-card">
                <h3><?php echo esc_html__('Payment Method', 'ecolepedia-marketplace'); ?></h3>
                <p><?php echo esc_html__('Configure keys in Settings > Payments.', 'ecolepedia-marketplace'); ?></p>
                <button class="ecolepedia-button" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                    <?php echo esc_html__('Pay Now', 'ecolepedia-marketplace'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
