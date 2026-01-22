<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-card">
        <h2><?php echo esc_html__('Welcome back', 'ecolepedia-marketplace'); ?></h2>
        <?php wp_login_form(['redirect' => home_url()]); ?>
        <a class="ecolepedia-button secondary" href="<?php echo esc_url(wp_lostpassword_url()); ?>" style="color: <?php echo esc_attr($options['accent_color']); ?>">
            <?php echo esc_html__('Forgot password?', 'ecolepedia-marketplace'); ?>
        </a>
    </div>
</div>
