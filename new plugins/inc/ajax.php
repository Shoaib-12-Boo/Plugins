<?php
defined('ABSPATH') || die("Nice Try");

add_action('wp_ajax_my_ajax_function', 'plugin_ajax_action');
add_action('wp_ajax_my_front_ajax_function', 'plugin_my_front_ajax_action');

function plugin_ajax_action(){
    if(isset($_POST['action']) && isset($_POST['optionl'])){
        update_option('plugin_option_1', sanitize_text_file($_POST['optionl']));
        echo ' Field succesfully updated'; 
    }else{
        echo "Error updating field";
    }
    wp_die();
}

function plugin_my_front_ajax_action(){
    if(isset($_POST['action']) && isset($_POST['value'])){
        echo absint($_POST['value']) + 10; 
    }else{
        echo "Error getting field";
    }
    wp_die();
}