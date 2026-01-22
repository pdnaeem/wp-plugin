<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-card">
        <h2><?php echo esc_html__('Place an Order', 'ecolepedia-marketplace'); ?></h2>
        <div class="ecolepedia-step">
            <h3><?php echo esc_html__('Step 1: Basics', 'ecolepedia-marketplace'); ?></h3>
            <div class="ecolepedia-grid">
                <div>
                    <label><?php echo esc_html__('Subject', 'ecolepedia-marketplace'); ?></label>
                    <?php wp_dropdown_categories(['taxonomy' => 'ecolepedia_subject', 'hide_empty' => false, 'name' => 'subject']); ?>
                </div>
                <div>
                    <label><?php echo esc_html__('Document Type', 'ecolepedia-marketplace'); ?></label>
                    <?php wp_dropdown_categories(['taxonomy' => 'ecolepedia_document_type', 'hide_empty' => false, 'name' => 'document_type']); ?>
                </div>
            </div>
            <button class="ecolepedia-button" data-ecolepedia-next type="button" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                <?php echo esc_html__('Next', 'ecolepedia-marketplace'); ?>
            </button>
        </div>
        <div class="ecolepedia-step">
            <h3><?php echo esc_html__('Step 2: Instructions', 'ecolepedia-marketplace'); ?></h3>
            <label><?php echo esc_html__('Topic / Title', 'ecolepedia-marketplace'); ?></label>
            <input type="text" name="title">
            <label><?php echo esc_html__('Instructions', 'ecolepedia-marketplace'); ?></label>
            <textarea name="instructions"></textarea>
            <button class="ecolepedia-button secondary" data-ecolepedia-prev type="button"><?php echo esc_html__('Back', 'ecolepedia-marketplace'); ?></button>
            <button class="ecolepedia-button" data-ecolepedia-next type="button" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                <?php echo esc_html__('Next', 'ecolepedia-marketplace'); ?>
            </button>
        </div>
        <div class="ecolepedia-step">
            <h3><?php echo esc_html__('Step 3: Timeline & Extras', 'ecolepedia-marketplace'); ?></h3>
            <label><?php echo esc_html__('Deadline', 'ecolepedia-marketplace'); ?></label>
            <input type="datetime-local" name="deadline">
            <label><?php echo esc_html__('Pages', 'ecolepedia-marketplace'); ?></label>
            <input type="number" min="1" name="pages">
            <button class="ecolepedia-button secondary" data-ecolepedia-prev type="button"><?php echo esc_html__('Back', 'ecolepedia-marketplace'); ?></button>
            <a class="ecolepedia-button" href="#checkout" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                <?php echo esc_html__('Review pricing', 'ecolepedia-marketplace'); ?>
            </a>
        </div>
    </div>
</div>
