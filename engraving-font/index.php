<?php
/*
Plugin Name: Custom Font Settings
Description: A plugin to set custom font URLs, font families, and font names.
Version: 1.2
Author: Shoaib
*/

// Hook to add the settings page to the admin menu
add_action('admin_menu', 'cfs_add_settings_page');

function cfs_add_settings_page() {
    add_menu_page(
        'Custom Font Settings',
        'Font Settings',
        'manage_options',
        'cfs-settings',
        'cfs_render_settings_page'
    );
}

// Render the settings page
function cfs_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Custom Font Settings</h1>
        <style>
            .cfs-settings-field {
                width: 100%;
                max-width: 600px;
            }
            .cfs-settings-field input {
                width: 100%;
                box-sizing: border-box; /* Ensures padding and border are included in the width */
            }
        </style>
        <form method="post" action="options.php">
            <?php
            settings_fields('cfs_settings_group');
            do_settings_sections('cfs-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Hook to register the settings
add_action('admin_init', 'cfs_register_settings');

function cfs_register_settings() {
    register_setting('cfs_settings_group', 'cfs_font_urls', 'sanitize_text_field');
    register_setting('cfs_settings_group', 'cfs_font_family', 'sanitize_text_field');
    register_setting('cfs_settings_group', 'cfs_font_name', 'sanitize_text_field');

    add_settings_section(
        'cfs_settings_section',
        'Font Settings',
        'cfs_settings_section_callback',
        'cfs-settings'
    );

    add_settings_field(
        'cfs_font_urls',
        'Font URLs (semicolon-separated)',
        'cfs_font_url_callback',
        'cfs-settings',
        'cfs_settings_section'
    );

    add_settings_field(
        'cfs_font_family',
        'Font Families (semicolon-separated)',
        'cfs_font_family_callback',
        'cfs-settings',
        'cfs_settings_section'
    );

    add_settings_field(
        'cfs_font_name',
        'Font Names (semicolon-separated)',
        'cfs_font_name_callback',
        'cfs-settings',
        'cfs_settings_section'
    );
}

function cfs_settings_section_callback() {
    echo 'Enter the custom font settings below, using semicolons to separate each entry:';
}

function cfs_font_url_callback() {
    $font_urls = get_option('cfs_font_urls', '');
    echo '<input type="text" id="cfs_font_urls" name="cfs_font_urls" value="' . esc_attr($font_urls) . '" class="cfs-settings-field" />';
}

function cfs_font_family_callback() {
    $font_families = get_option('cfs_font_family', '');
    echo '<input type="text" id="cfs_font_family" name="cfs_font_family" value="' . esc_attr($font_families) . '" class="cfs-settings-field" />';
}

function cfs_font_name_callback() {
    $font_names = get_option('cfs_font_name', '');
    echo '<input type="text" id="cfs_font_name" name="cfs_font_name" value="' . esc_attr($font_names) . '" class="cfs-settings-field" />';
}

// Enqueue the custom font
add_action('wp_enqueue_scripts', 'cfs_enqueue_custom_font');

function cfs_enqueue_custom_font() {
    $font_urls = get_option('cfs_font_urls', '');
    $font_families = get_option('cfs_font_family', '');

    $urls = array_map('trim', explode(';', $font_urls));
    $families = array_map('trim', explode(';', $font_families));

    foreach ($urls as $index => $url) {
        if ($url && isset($families[$index])) {
            $family = $families[$index];
            wp_enqueue_style('custom-font-' . md5($url), $url);
            $custom_css = "
                body {
                    font-family: '" . esc_attr($family) . "';
                }
            ";
            wp_add_inline_style('custom-font-' . md5($url), $custom_css);
        }
    }
}

// Register REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('custom-font/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'cfs_get_font_settings',
    ));
});

function cfs_get_font_settings() {
    $font_urls = get_option('cfs_font_urls', '');
    $font_families = get_option('cfs_font_family', '');
    $font_names = get_option('cfs_font_name', '');

    $urls = array_map('trim', explode(';', $font_urls));
    $families = array_map('trim', explode(';', $font_families));
    $names = array_map('trim', explode(';', $font_names));

    $settings = [];
    foreach ($urls as $index => $url) {
        $settings[] = [
            'font_url' => $url,
            'font_family' => $families[$index] ?? '',
            'font_name' => $names[$index] ?? '',
        ];
    }

    return new WP_REST_Response($settings, 200);
}

add_action('admin_enqueue_scripts', 'cfs_enqueue_admin_styles');

function cfs_enqueue_admin_styles($hook) {
    // Only load styles on the plugin settings page
    if ($hook !== 'toplevel_page_cfs-settings') {
        return;
    }
    wp_enqueue_style('cfs-admin-styles', plugin_dir_url(__FILE__) . 'style.css');
}
?>
