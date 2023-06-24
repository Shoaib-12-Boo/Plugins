<?php
defined('ABSPATH') || die("Nice Try");
 
add_action('admin_init', function(){
    add_meta_box(
        'mycustommetaboxe',
        'My Custom MetaBox',
        'plugin_custom_metabox',
        ['post','page'],
    );
});

function plugin_custom_metabox($post){
    $mycustommetaboxe = get_post_meta($post->ID, 'mycustommetaboxe', true) ? get_post_meta($post->ID, 'mycustommetaboxe', true): '';
    ?>
    <input type="text" id="" name="mycustommetaboxe" class="" value="">
    <?php       
}

add_action('save_post', 'plugin_save_post');

function plugin_save_post($post_id){
    if(array_key_exists('mycustommetaboxe', $_POST)){
        update_post_meta($post_id, 'mycustommetaboxe', $_POST['mycustommetaboxe']);
    }
}