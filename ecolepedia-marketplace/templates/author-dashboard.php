<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-grid">
        <div class="ecolepedia-card">
            <h3><?php echo esc_html__('Assigned Orders', 'ecolepedia-marketplace'); ?></h3>
            <p class="ecolepedia-pill">2</p>
        </div>
        <div class="ecolepedia-card">
            <h3><?php echo esc_html__('Earnings (This Month)', 'ecolepedia-marketplace'); ?></h3>
            <p class="ecolepedia-pill">$420</p>
        </div>
        <div class="ecolepedia-card">
            <h3><?php echo esc_html__('On-time Rate', 'ecolepedia-marketplace'); ?></h3>
            <p class="ecolepedia-pill">98%</p>
        </div>
    </div>
    <div class="ecolepedia-card">
        <h2><?php echo esc_html__('Workspace', 'ecolepedia-marketplace'); ?></h2>
        <p><?php echo esc_html__('View requirements, chat with customers, and submit drafts.', 'ecolepedia-marketplace'); ?></p>
        <a class="ecolepedia-button" href="#" style="background: <?php echo esc_attr($options['accent_color']); ?>">
            <?php echo esc_html__('Open Workspace', 'ecolepedia-marketplace'); ?>
        </a>
    </div>
</div>
