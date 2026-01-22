<?php
if (!defined('ABSPATH')) {
    exit;
}
$order_id = isset($data['order_id']) ? (int) $data['order_id'] : 0;
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-card">
        <h2><?php echo esc_html__('Order Details', 'ecolepedia-marketplace'); ?></h2>
        <?php if ($order_id) : ?>
            <p><?php echo esc_html(sprintf(__('Viewing order #%d', 'ecolepedia-marketplace'), $order_id)); ?></p>
        <?php else : ?>
            <p><?php echo esc_html__('Select an order to view details.', 'ecolepedia-marketplace'); ?></p>
        <?php endif; ?>
    </div>
</div>
