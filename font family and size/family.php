<?php
/**
 * Plugin Name: Font Selector Plugin
 * Description: Adds settings pages with font size and font family selection dropdowns.
 * Version: 1.0
 * Author: Your Name
 */

function font_selector_plugin_menu() {
    add_menu_page(
        'Font Selector Settings',
        'Font Selector',
        'manage_options',
        'font-selector-settings',
        'font_selector_plugin_settings_page',
        'dashicons-editor-text',
        99
    );
}
add_action('admin_menu', 'font_selector_plugin_menu');

function font_selector_plugin_settings_page() {
    if (isset($_POST['font_selector_submit'])) {
        if (isset($_POST['font_size_selector_nonce']) && wp_verify_nonce($_POST['font_size_selector_nonce'], 'font_size_selector_nonce')) {
            update_option('selected_font_size', sanitize_text_field($_POST['fontSizeDropdown']));
        }
        if (isset($_POST['font_selector_nonce']) && wp_verify_nonce($_POST['font_selector_nonce'], 'font_selector_nonce')) {
            update_option('selected_font', sanitize_text_field($_POST['fontDropdown']));
        }
    }

    $saved_font_size = get_option('selected_font_size', '16px');
    $saved_font = get_option('selected_font', 'Arial');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('font_size_selector_nonce', 'font_size_selector_nonce'); ?>
            <label for="fontSizeDropdown">Select Font Size:</label>
            <select id="fontSizeDropdown" name="fontSizeDropdown">
                <option value="12px" <?php selected($saved_font_size, '12px'); ?>>12px</option>
                <option value="14px" <?php selected($saved_font_size, '14px'); ?>>14px</option>
                <option value="16px" <?php selected($saved_font_size, '16px'); ?>>16px</option>
                <option value="18px" <?php selected($saved_font_size, '18px'); ?>>18px</option>
                <option value="20px" <?php selected($saved_font_size, '20px'); ?>>20px</option>
            </select>
            <label for="fontDropdown">Select Font Family:</label>
            <select id="fontDropdown" name="fontDropdown">
                <option value="Arial" <?php selected($saved_font, 'Arial'); ?>>Arial</option>
                <option value="Verdana" <?php selected($saved_font, 'Verdana'); ?>>Verdana</option>
                <option value="Times New Roman" <?php selected($saved_font, 'Times New Roman'); ?>>Times New Roman</option>
                <option value="Impact" <?php selected($saved_font, 'Impact'); ?>>Impact</option>
                <option value="Lucida Console" <?php selected($saved_font, 'Lucida Console'); ?>>Lucida Console</option>
                <option value="Courier New" <?php selected($saved_font, 'Courier New'); ?>>Courier New</option>
            </select>
            <h2 style="font-size: <?php echo esc_attr($saved_font_size); ?>; font-family: <?php echo esc_attr($saved_font); ?>">
                This is a sample heading with the selected font size and font family.
            </h2>
            <?php wp_nonce_field('font_selector_nonce', 'font_selector_nonce'); ?>
            <input type="submit" name="font_selector_submit" class="button button-primary" value="Save Changes">
        </form>
    </div>
    <?php
}