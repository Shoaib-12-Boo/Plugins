<?php
add_action('init', 'Boo');
function Boo(){
    add_shortcode('test', 'myshortcode_custom');
    add_shortcode('test', 'myshortcode_news_custom');
}
// shortcode
function myshortcode_custom($atts, $content=''){
    $atts = shortcode_atts(
        array(
        'message' => 'Shoaib Shafique',
    ),
    $atts , 'test');
    return $content;
}

function myshortcode_news_custom($atts, $content=''){
    $atts = shortcode_atts(
        array(
        'message' => 'Shoaib Shafique',
    ),
    $atts , 'news');
    $args = array(
        'post_type' => 'news',
        'post__status' => 'publish',
        'posts_per_page' => -1,
        'nopaging' => true
    );
    $query = new WP_Query($args,);
    if($query->have_posts()):
        while($query->have_post()):
            $query->the_post();
            $content.="<h2><a href=".get_the_permalink()." >".get_the_title()." </a></h2>";
            $content.="<p>".get_the_content()."</p>"; 
        endwhile;
    else:
        $content.="<p>No News post found...</P>";
    endif;
    return $content;
}