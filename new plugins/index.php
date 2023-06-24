<?php
/*
Plugin Name: plugin3
Description: Creates a custom menu page in WordPress.
Version: 1.0
Author: Majen Boo
*/

// for file checking or security
// if(!defined('ABSPATH')):
//     die("You can not acess this file directly.");
// endif;
    // echo "This is plugin checking";

//  for activation and deactivation hooks
// function my_plugin_activation() {
//     add_option('Update file title', 'Your title is under control');
// }
// register_activation_hook( __FILE__, 'my_plugin_activation' );

// function my_plugin_deactivation() {
//     delete_option('Update file title');
// }
// register_deactivation_hook( __FILE__, 'my_plugin_deactivation' );


// Hooks for Wordpress plugin development
// Action Hooks

// echo plugin_dir_url();
// echo plugin_dir_path();

Define('PLUGIN_FILE', __FILE__);

include plugin_dir_path(__FILE__)."inc/shortcode.php";
include plugin_dir_path(__FILE__)."inc/metaboxes.php";
include plugin_dir_path(__FILE__)."inc/custom post.php";
include plugin_dir_path(__FILE__)."inc/ajax.php";
include plugin_dir_path(__FILE__)."inc/db.php";
include plugin_dir_path(__FILE__)."inc/user.php";

// filter hook
// add_filter('the_title', 'page_title');

function page_title($title){
    return 'New Page Post Title';
}
add_action('wp_enqueue_scripts', 'plugin_enqueue_scripts');
add_action('admin_enqueue_scripts', 'plugin_enqueue_scripts');

function plugin_enqueue_scripts(){
    wp_enqueue_script('jquery');
    wp_enqueue_style('plugin_dev_plugin', plugin_dir_url(__FILE__)."assets/css/style.css");
    wp_enqueue_script('plugin_dev_plugin', plugin_dir_url(__FILE__)."assests/js/custom.js", array(), '1.0.0', false);
    wp_localize_script('plugin_dev_plugin', 'ajax_object', array('ajaxurl'=>admin_url('admin_ajax.php'),'num'=>10));
}

// for Ajax jquert
function plugin_wp_enqueue_scripts() {
    wp_enqueue_script('plugin_dev_plugin', plugin_dir_url(__FILE__)."assests/js/custom.js", array(), '1.0.0', false);
}

// custom WordPress menu with plugin
add_action('admin_menu', 'plugin_menu');
// for get add and remove data in the option page of the wrdpress with activation and deactivation
add_action('admin_menu', 'Plugin_process_form_settings');


function plugin_menu() {
    add_menu_page(
    'Plugin Options', 
    'Plugin Options', 
    'manage_options', 
    'Plugin-Options', 
    'Plugin_Options_func', 
   
);
    add_submenu_page(
        'Plugin-Options', 
        'plugin settings',
        'plugin settings', 
        'manage_options', 
        'plugin_settings', 
        'plugin_setting_func'
    );
    add_submenu_page(
        'Plugin-Options', 
        'plugin Layout',
        'plugin Layout', 
        'manage_options', 
        'plugin_layout', 
        'plugin_layout_func'
    );
// another menu to submenu 
    // add_dashboard_page(
    //     'Themes Option',
    //     'Theme Options',
    //     'manage_options',
    //     'plugin_theme_settings',
    //     'plugin_theme_setting_func',
    // );
}

register_activation_hook(__FILE__, function() {
    add_option('plugin_option_1', '');
});

register_deactivation_hook(__FILE__, function() {
    delete_option('plugin_option_1', '');
});

// for get add and remove data in the option page of the wrdpress with activation and deactivation
function Plugin_process_form_settings(){
    register_setting(
        'plugin_option_group',
        'plugin_option_name',
    );
    if(isset($_POST['action']) && current_user_can('manage_options')){
        update_option('plugin_option_1', sanitize_text_field($_POST['plugin_option_1']));
    }
}
function Plugin_Options_func(){ ?>
    <div class="wrap">
        <?php settings_errors(); ?>
        <h1>Plugin Option menu</h1>
        <form id="ajax_form" action="options.php" method="post">
            <?php settings_fields('plugin_option_group');?>
            <label for="">Setting One<input type="text" name="plugin_option_1" 
            value="<?php echo esc_html(get_option('plugin_option_1'));?>"/></label>
            <?php submit_button('Save Changes'); ?>
        </form>
        <?php include plugin_dir_path(__FILE__)."inc/api.php"; ?>
    </div>
<?php
}

function plugin_setting_func(){
    echo "<h1>This is Setting menu</h1>";
}

function plugin_layout_func(){
    echo "<h1>This is menu Layout</h1>";
}

// function plugin_theme_setting_func(){
//     echo "<h1>Theme Options</h1>";
// }
