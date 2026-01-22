<?php
if (!defined('ABSPATH')) {
    exit;
}
$options = Ecolepedia\Marketplace\Settings::get();
?>
<div class="ecolepedia-app">
    <div class="ecolepedia-header">
        <div>
            <h2><?php echo esc_html__('Customer Dashboard', 'ecolepedia-marketplace'); ?></h2>
            <p class="ecolepedia-muted"><?php echo esc_html__('Track your orders and manage conversations in one place.', 'ecolepedia-marketplace'); ?></p>
        </div>
        <a class="ecolepedia-button" href="#" style="background: <?php echo esc_attr($options['accent_color']); ?>">
            <?php echo esc_html__('New Order', 'ecolepedia-marketplace'); ?>
        </a>
    </div>
    <div class="ecolepedia-layout">
        <aside class="ecolepedia-sidebar">
            <h3><?php echo esc_html__('Navigation', 'ecolepedia-marketplace'); ?></h3>
            <ul class="ecolepedia-nav">
                <li><a href="#"><?php echo esc_html__('Overview', 'ecolepedia-marketplace'); ?></a></li>
                <li><a href="#"><?php echo esc_html__('Orders', 'ecolepedia-marketplace'); ?></a></li>
                <li><a href="#"><?php echo esc_html__('Messages', 'ecolepedia-marketplace'); ?></a></li>
                <li><a href="#"><?php echo esc_html__('Invoices', 'ecolepedia-marketplace'); ?></a></li>
            </ul>
        </aside>
        <div>
            <div class="ecolepedia-grid">
                <div class="ecolepedia-card">
                    <h3><?php echo esc_html__('Active Orders', 'ecolepedia-marketplace'); ?></h3>
                    <p class="ecolepedia-pill">3</p>
                </div>
                <div class="ecolepedia-card">
                    <h3><?php echo esc_html__('Unread Messages', 'ecolepedia-marketplace'); ?></h3>
                    <p class="ecolepedia-pill">1</p>
                </div>
                <div class="ecolepedia-card">
                    <h3><?php echo esc_html__('Pending Actions', 'ecolepedia-marketplace'); ?></h3>
                    <p class="ecolepedia-pill">2</p>
                </div>
            </div>
            <div class="ecolepedia-card">
                <h2><?php echo esc_html__('Your Orders', 'ecolepedia-marketplace'); ?></h2>
                <table class="ecolepedia-table">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Order', 'ecolepedia-marketplace'); ?></th>
                            <th><?php echo esc_html__('Status', 'ecolepedia-marketplace'); ?></th>
                            <th><?php echo esc_html__('Deadline', 'ecolepedia-marketplace'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#1024 - <?php echo esc_html__('Marketing Strategy Essay', 'ecolepedia-marketplace'); ?></td>
                            <td><span class="ecolepedia-pill"><?php echo esc_html__('In Progress', 'ecolepedia-marketplace'); ?></span></td>
                            <td><?php echo esc_html__('3 days', 'ecolepedia-marketplace'); ?></td>
                        </tr>
                        <tr>
                            <td>#1025 - <?php echo esc_html__('Business Case Study', 'ecolepedia-marketplace'); ?></td>
                            <td><span class="ecolepedia-pill"><?php echo esc_html__('Draft Submitted', 'ecolepedia-marketplace'); ?></span></td>
                            <td><?php echo esc_html__('12 hours', 'ecolepedia-marketplace'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
