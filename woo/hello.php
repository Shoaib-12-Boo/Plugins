<?php
/*
Plugin Name: woo
Description: Creates a custom menu page in WordPress.
Version: 1.0
Author: Majen Boo
*/

echo 'hello working ';
function my_custom_function() {
    echo '<p>This is a custom message for the single product.</p>';
}
add_action( 'woocommerce_before_add_to_cart_form', 'my_custom_function' );
