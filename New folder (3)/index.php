<?php
/*
Plugin Name: Amiri T-Shirt Price Updater
Plugin URI: http://TechoSolution.com/
Description: This plugin updates the prices of Amiri T-Shirts to specified values or by dividing the current prices by 100.
Version: 1.3
Author: Shoaib
Author URI: http://yourwebsite.com/
License: GPL2
*/

function update_amiri_tshirt_prices() {
    // List of category IDs with their specific prices
    $category_prices = array(
        121963 => 99.59,
        121965 => 138.98,
        121969 => 67.56 // For this category, we'll divide the prices by 100
    );

    foreach ($category_prices as $category_id => $new_price) {
        // Query to get products in the specified category
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category_id,
                ),
            ),
        );

        $products = new WP_Query($args);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product_id = get_the_ID();
                $product = wc_get_product($product_id);

                if ($product) {
                    // Get the current prices
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();

                    if ($new_price !== null) {
                        // Set the specific price
                        $product->set_regular_price($new_price);
                        if ($sale_price) {
                            $product->set_sale_price($new_price);
                        }
                    } else {
                        // Divide the current prices by 100
                        if (is_numeric($regular_price)) {
                            $product->set_regular_price($regular_price / 100);
                        }
                        if (is_numeric($sale_price)) {
                            $product->set_sale_price($sale_price / 100);
                        }
                    }

                    // Save the product
                    $product->save();
                } else {
                    error_log("Product with ID $product_id could not be retrieved.");
                }
            }
        } else {
            error_log("No products found in category ID $category_id.");
        }

        wp_reset_postdata();
    }
}

// Hook the function to an action or run it directly
add_action('init', 'update_amiri_tshirt_prices');
