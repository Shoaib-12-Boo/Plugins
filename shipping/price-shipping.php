<?php
/*
Plugin Name: shopping cost
Description: Creates a custom menu page in WordPress.
Version: 1.0
Author: Majen Boo
*/

// Add shipping when the price range increases by more than 30
// function price_range_shipping($cart) {
//     $shipping_cost = 5; // Example shipping cost

//     $cart_total = $cart->get_cart_contents_total();

//     if ($cart_total > 30) {
//         $cart->add_fee(__('Shipping', 'text-domain'), $shipping_cost);
//     }
// }
// add_action('woocommerce_cart_calculate_fees', 'price_range_shipping', 10, 1);
?>


<!-- hide shipping method when free available  -->
<?php

// function my_hide_shipping_when_free_is_available( $rates ) {
// 	$free = array();
// 	foreach ( $rates as $rate_id => $rate ) {
// 		if ( 'free_shipping' === $rate->method_id ) {
// 			$free[ $rate_id ] = $rate;
// 			break;
// 		}
// 	}
// 	return ! empty( $free ) ? $free : $rates;
// }
// add_filter( 'woocommerce_package_rates', 'my_hide_shipping_when_free_is_available', 100 );

?>


<?php

add_filter('woocommerce_package_rates', 'disable_flat_rate_based_on_price', 10, 2);

function disable_flat_rate_based_on_price($rates, ) {
    $cart_total = WC()->cart->get_cart_total();
    $threshold = 30;

    if (floatval($cart_total) > $threshold) {
        foreach ($rates as $rate_key => $rate) {
            if ('flat_rate' === $rate->method_id) {
                $rates[$rate_key]->cost = 0;
                break; // Stop the loop if flat rate is disabled
            }
        }
    }
    return $rates;
}

?>