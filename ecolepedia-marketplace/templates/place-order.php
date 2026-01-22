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
                <h2><?php echo esc_html__('Place an Order', 'ecolepedia-marketplace'); ?></h2>
                <p class="ecolepedia-muted"><?php echo esc_html__('Complete the steps below to receive an instant quote.', 'ecolepedia-marketplace'); ?></p>
            </div>
            <span class="ecolepedia-badge"><?php echo esc_html__('Avg. turnaround 24-72 hrs', 'ecolepedia-marketplace'); ?></span>
        </div>
        <div class="ecolepedia-card">
            <div class="ecolepedia-stepper">
                <span>1</span><span>2</span><span>3</span><span>4</span>
            </div>
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
                    <div>
                        <label><?php echo esc_html__('Academic Level', 'ecolepedia-marketplace'); ?></label>
                        <select name="academic_level">
                            <option><?php echo esc_html__('Undergraduate', 'ecolepedia-marketplace'); ?></option>
                            <option><?php echo esc_html__('Masters', 'ecolepedia-marketplace'); ?></option>
                            <option><?php echo esc_html__('PhD', 'ecolepedia-marketplace'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label><?php echo esc_html__('Language', 'ecolepedia-marketplace'); ?></label>
                        <select name="language">
                            <option><?php echo esc_html__('English (US)', 'ecolepedia-marketplace'); ?></option>
                            <option><?php echo esc_html__('English (UK)', 'ecolepedia-marketplace'); ?></option>
                            <option><?php echo esc_html__('Spanish', 'ecolepedia-marketplace'); ?></option>
                        </select>
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
                <label><?php echo esc_html__('Sources Required', 'ecolepedia-marketplace'); ?></label>
                <input type="number" min="0" name="sources">
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
                <div class="ecolepedia-grid">
                    <label><input type="checkbox"> <?php echo esc_html__('Plagiarism Report', 'ecolepedia-marketplace'); ?></label>
                    <label><input type="checkbox"> <?php echo esc_html__('Slides', 'ecolepedia-marketplace'); ?></label>
                    <label><input type="checkbox"> <?php echo esc_html__('VIP Support', 'ecolepedia-marketplace'); ?></label>
                </div>
                <button class="ecolepedia-button secondary" data-ecolepedia-prev type="button"><?php echo esc_html__('Back', 'ecolepedia-marketplace'); ?></button>
                <button class="ecolepedia-button" data-ecolepedia-next type="button" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                    <?php echo esc_html__('Next', 'ecolepedia-marketplace'); ?>
                </button>
            </div>
            <div class="ecolepedia-step">
                <h3><?php echo esc_html__('Step 4: Review', 'ecolepedia-marketplace'); ?></h3>
                <div class="ecolepedia-card">
                    <p><?php echo esc_html__('Base rate: $', 'ecolepedia-marketplace'); ?><?php echo esc_html($options['base_rate']); ?></p>
                    <p><?php echo esc_html__('Tax rate: ', 'ecolepedia-marketplace'); ?><?php echo esc_html($options['tax_rate']); ?>%</p>
                    <p><strong><?php echo esc_html__('Estimated Total: $240', 'ecolepedia-marketplace'); ?></strong></p>
                </div>
                <button class="ecolepedia-button secondary" data-ecolepedia-prev type="button"><?php echo esc_html__('Back', 'ecolepedia-marketplace'); ?></button>
                <a class="ecolepedia-button" href="#checkout" style="background: <?php echo esc_attr($options['accent_color']); ?>">
                    <?php echo esc_html__('Continue to checkout', 'ecolepedia-marketplace'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
