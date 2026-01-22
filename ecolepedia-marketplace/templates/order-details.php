<?php
if (!defined('ABSPATH')) {
    exit;
}
$order_id = isset($data['order_id']) ? (int) $data['order_id'] : 0;
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-container">
        <div class="ecolepedia-card">
            <h2><?php echo esc_html__('Order Details', 'ecolepedia-marketplace'); ?></h2>
            <?php if ($order_id) : ?>
                <p><?php echo esc_html(sprintf(__('Viewing order #%d', 'ecolepedia-marketplace'), $order_id)); ?></p>
                <div class="ecolepedia-grid">
                    <div class="ecolepedia-card">
                        <h3><?php echo esc_html__('Status', 'ecolepedia-marketplace'); ?></h3>
                        <span class="ecolepedia-pill"><?php echo esc_html__('In Progress', 'ecolepedia-marketplace'); ?></span>
                    </div>
                    <div class="ecolepedia-card">
                        <h3><?php echo esc_html__('Deadline', 'ecolepedia-marketplace'); ?></h3>
                        <p><?php echo esc_html__('3 days', 'ecolepedia-marketplace'); ?></p>
                    </div>
                    <div class="ecolepedia-card">
                        <h3><?php echo esc_html__('Assigned Author', 'ecolepedia-marketplace'); ?></h3>
                        <p><?php echo esc_html__('Verified Author', 'ecolepedia-marketplace'); ?></p>
                    </div>
                </div>
            <?php else : ?>
                <p><?php echo esc_html__('Select an order to view details.', 'ecolepedia-marketplace'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
