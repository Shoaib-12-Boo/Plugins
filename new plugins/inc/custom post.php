<?php
defined('ABSPATH') || die("Nice Try");

add_action('init', 'plugin_news_post');

function plugin_news_post(){
    register_post_type(
        'news',
        array(
            'label' => "Global News",
            // 'label' => array(

            // ),  
            'public' => true,
            'description' => 'Test custom post type of news...',
            'suppirts' => ['title', 'editor', 'comments', 'custom-fields' ,'thumbnail'],         
        ));
}

// add_filter("template_include", "plugin_template_news");

// function plugin_template_news($template){
//     global $post;
//     if(is_single() AND $post->post_type == 'news'){
//     $template = plugin_dir_path(__FILE__). "templates/plugin-news.php";
//     print_r($template);
//     exit;
//     }
//     return $template;
// }