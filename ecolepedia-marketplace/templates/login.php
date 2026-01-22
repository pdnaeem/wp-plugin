<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-container ecolepedia-auth-layout">
        <div class="ecolepedia-card">
            <h2><?php echo esc_html__('Welcome back', 'ecolepedia-marketplace'); ?></h2>
            <p class="ecolepedia-muted"><?php echo esc_html__('Log in to access your dashboard, orders, and messages.', 'ecolepedia-marketplace'); ?></p>
            <?php wp_login_form(['redirect' => home_url()]); ?>
            <a class="ecolepedia-button secondary" href="<?php echo esc_url(wp_lostpassword_url()); ?>">
                <?php echo esc_html__('Forgot password?', 'ecolepedia-marketplace'); ?>
            </a>
        </div>
        <div class="ecolepedia-card accent">
            <h3><?php echo esc_html__('Your workspace in one view', 'ecolepedia-marketplace'); ?></h3>
            <ul class="ecolepedia-list">
                <li><?php echo esc_html__('Centralized order timelines and files.', 'ecolepedia-marketplace'); ?></li>
                <li><?php echo esc_html__('Secure chat with authors and support.', 'ecolepedia-marketplace'); ?></li>
                <li><?php echo esc_html__('Instant status updates and notifications.', 'ecolepedia-marketplace'); ?></li>
            </ul>
        </div>
    </div>
</div>
