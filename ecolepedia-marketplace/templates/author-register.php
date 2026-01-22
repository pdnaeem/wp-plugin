<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-card">
        <h2><?php echo esc_html__('Apply as an Ecolepedia Author', 'ecolepedia-marketplace'); ?></h2>
        <p><?php echo esc_html__('Share your expertise and start earning from quality writing projects.', 'ecolepedia-marketplace'); ?></p>
        <form class="ecolepedia-form" method="post">
            <input type="hidden" name="ecolepedia_action" value="author_register">
            <input type="hidden" name="ecolepedia_nonce" value="<?php echo esc_attr(wp_create_nonce('ecolepedia_form_action')); ?>">
            <label for="ecolepedia-author-name"><?php echo esc_html__('Full name', 'ecolepedia-marketplace'); ?></label>
            <input id="ecolepedia-author-name" type="text" name="name" required>
            <label for="ecolepedia-author-email"><?php echo esc_html__('Email', 'ecolepedia-marketplace'); ?></label>
            <input id="ecolepedia-author-email" type="email" name="email" required>
            <label for="ecolepedia-author-password"><?php echo esc_html__('Password', 'ecolepedia-marketplace'); ?></label>
            <input id="ecolepedia-author-password" type="password" name="password" required>
            <button class="ecolepedia-button" type="submit" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                <?php echo esc_html__('Submit application', 'ecolepedia-marketplace'); ?>
            </button>
        </form>
    </div>
</div>
