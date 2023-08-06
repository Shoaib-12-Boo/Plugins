<?php
/*
Plugin Name: product dropdown
Description: Fetches WooCommerce product data using jQuery.
Version: 1.0
Author: shoaib
*/


// Enqueue necessary scripts for AJAX
function woocommerce_product_details_ajax_scripts() {
    wp_enqueue_script('woocommerce-product-details-ajax', plugin_dir_url(__FILE__) . 'js/woocommerce-product-details-ajax.js', array('jquery'), '1.0', true);
    wp_localize_script('woocommerce-product-details-ajax', 'product_details_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'woocommerce_product_details_ajax_scripts');

// AJAX callback to retrieve product details
function woocommerce_product_details_ajax_callback() {
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $product = wc_get_product($product_id);

        if ($product) {
            $product_details = array(
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'description' => $product->get_description(),
                'sku' => $product->get_sku(),
                // Add more product details as needed
            );

            wp_send_json_success($product_details);
        } else {
            wp_send_json_error('Product not found');
        }
    } else {
        wp_send_json_error('Invalid product ID');
    }
}
add_action('wp_ajax_woocommerce_product_details_ajax', 'woocommerce_product_details_ajax_callback');
add_action('wp_ajax_nopriv_woocommerce_product_details_ajax', 'woocommerce_product_details_ajax_callback');

function woocommerce_product_details_shortcode() {
    ob_start();
    ?>
    <form id="product-details-form">
        <label for="product-dropdown">Select a product:</label>
        <select id="product-dropdown" name="product_id">
            <option value="">Select a product</option>
            <?php
            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );
            $products = new WP_Query($args);

            if ($products->have_posts()) {
                while ($products->have_posts()) {
                    $products->the_post();
                    $product_id = get_the_ID();
                    $product_name = get_the_title();
                    echo '<option value="' . esc_attr($product_id) . '">' . esc_html($product_name) . '</option>';
                }
            }
            wp_reset_postdata();
            ?>
        </select>
    </form>
    <div id="product-details-container">
        <!-- Product details will be displayed here via AJAX -->
    </div>
    <script>
        // JavaScript to handle the product details display
        jQuery(document).ready(function($) {
            $('#product-dropdown').on('change', function() {
                var product_id = $(this).val();

                if (product_id !== '') {
                    $.ajax({
                        type: 'POST',
                        url: product_details_ajax.ajax_url,
                        data: {
                            action: 'woocommerce_product_details_ajax',
                            product_id: product_id
                        },
                        success: function(response) {
                            if (response.success) {
                                var productDetailsContainer = $('#product-details-container');
                                productDetailsContainer.html(
                                    '<h3>' + response.data.name + '</h3>' +
                                    '<p><strong>Price:</strong> ' + response.data.price + '</p>' +
                                    '<p><strong>Description:</strong> ' + response.data.description + '</p>' +
                                    '<p><strong>SKU:</strong> ' + response.data.sku + '</p>'
                                    // Add more product details as needed
                                );
                            } else {
                                console.error(response.data);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                } else {
                    $('#product-details-container').html('');
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('woocommerce_product_details', 'woocommerce_product_details_shortcode');
