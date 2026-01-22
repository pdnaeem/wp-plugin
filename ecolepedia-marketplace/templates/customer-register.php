<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-container ecolepedia-auth-layout">
        <div class="ecolepedia-card">
            <h2><?php echo esc_html__('Create your Ecolepedia account', 'ecolepedia-marketplace'); ?></h2>
            <p class="ecolepedia-muted"><?php echo esc_html__('Join to place orders, track progress, and chat with your writer.', 'ecolepedia-marketplace'); ?></p>
            <form class="ecolepedia-form" method="post">
                <input type="hidden" name="ecolepedia_action" value="customer_register">
                <input type="hidden" name="ecolepedia_nonce" value="<?php echo esc_attr(wp_create_nonce('ecolepedia_form_action')); ?>">
                <label for="ecolepedia-name"><?php echo esc_html__('Full name', 'ecolepedia-marketplace'); ?></label>
                <input id="ecolepedia-name" type="text" name="name" required>
                <label for="ecolepedia-email"><?php echo esc_html__('Email', 'ecolepedia-marketplace'); ?></label>
                <input id="ecolepedia-email" type="email" name="email" required>
                <label for="ecolepedia-password"><?php echo esc_html__('Password', 'ecolepedia-marketplace'); ?></label>
                <input id="ecolepedia-password" type="password" name="password" required>
                <button class="ecolepedia-button" type="submit" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                    <?php echo esc_html__('Create account', 'ecolepedia-marketplace'); ?>
                </button>
            </form>
        </div>
        <div class="ecolepedia-card accent">
            <h3><?php echo esc_html__('Why customers choose Ecolepedia', 'ecolepedia-marketplace'); ?></h3>
            <ul class="ecolepedia-list">
                <li><?php echo esc_html__('Verified authors and transparent progress tracking.', 'ecolepedia-marketplace'); ?></li>
                <li><?php echo esc_html__('Secure escrow payments and flexible revision policy.', 'ecolepedia-marketplace'); ?></li>
                <li><?php echo esc_html__('Real-time chat, file sharing, and order timelines.', 'ecolepedia-marketplace'); ?></li>
            </ul>
            <span class="ecolepedia-badge"><?php echo esc_html__('Trusted by global students', 'ecolepedia-marketplace'); ?></span>
        </div>
    </div>
</div>
