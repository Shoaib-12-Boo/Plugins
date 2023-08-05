<?php
/*
Plugin Name: product
Description: Fetches WooCommerce product data using jQuery.
Version: 1.0
Author: Your Name
*/


if (!defined('ABSPATH')) {
    exit;
}
function get_woocommerce_product_data() {
    if (class_exists('WooCommerce')) {
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );

        $products = new WP_Query($args);

        $product_data = array();

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product_id = get_the_ID();
                $product_data[] = array(
                    'id' => $product_id,
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'price' => get_post_meta($product_id, '_regular_price', true),
                     'description' => get_the_excerpt(),
                );
            }
        }

        wp_reset_postdata();

        return $product_data;
    } else {
        return array(); 
    }
}

?>

<?php
function woocommerce_product_data_shortcode() {
    $products = get_woocommerce_product_data();

    if (!empty($products)) {
        $output = '<div id="product-data-container">';
        foreach ($products as $product) {
            $output .= '<div>';
            $output .= '<h3><a href="' . $product['permalink'] . '">' . $product['title'] . '</a></h3>';
            $output .= '<p>Price: ' . $product['price'] . '</p>';
             $output .= '<p>Description: ' . $product['description'] . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    } else {
        return '<p>No products found.</p>';
    }
}
add_shortcode('woocommerce_product_data_1', 'woocommerce_product_data_shortcode');
