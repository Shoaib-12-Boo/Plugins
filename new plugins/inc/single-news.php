<?php defined('ABSPATH') || die("Nice Try"); ?>
<?php get_header(); ?>
<article id="post-news-<?php echo get_the_ID(); ?>" class="post_news";
<?php
    if(have_posts()):
        while(have_posts()):
            the_post();
            the_title("<h2>", "</h2>");
            the_content("<p>", "</p>");
        endwhile;
    endif;
?>
</article>
<?php get_footer(); ?>