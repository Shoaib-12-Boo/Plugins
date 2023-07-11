<?php
/*
Plugin Name: woocomerce admin hook
Description: Creates a custom menu page in WordPress.
Version: 1.0
Author: Majen Boo
*/

// The code for displaying WooCommerce Product Custom Fields
add_action( 'woocommerce_product_options_general_product_data', 
'woocommerce_product_custom_fields' ); 
// Following code Saves  WooCommerce Product Custom Fields
add_action( 'woocommerce_process_product_meta', 
'woocommerce_product_custom_fields_save' );



function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_text_field',
            'placeholder' => 'Custom Product Text Field',
            'label' => __('Custom Product Text Field', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );
    //Custom Product Number Field
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_product_number_field',
            'placeholder' => 'Custom Product Number Field',
            'label' => __('Custom Product Number Field', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            )
        )
    );
    //Custom Product  Textarea
    woocommerce_wp_textarea_input(
        array(
            'id' => '_custom_product_textarea',
            'placeholder' => 'Custom Product Textarea',
            'label' => __('Custom Product Textarea', 'woocommerce')
        )
    );
    echo '</div>';
}

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');

function woocommerce_product_custom_fields_save($post_id){
    // Custom Product Text Field
    $woocommerce_custom_product_text_field = $_POST['_custom_product_text_field'];
    if (!empty($woocommerce_custom_product_text_field))
        update_post_meta($post_id, '_custom_product_text_field', esc_attr($woocommerce_custom_product_text_field));
// Custom Product Number Field
    $woocommerce_custom_product_number_field = $_POST['_custom_product_number_field'];
    if (!empty($woocommerce_custom_product_number_field))
        update_post_meta($post_id, '_custom_product_number_field', esc_attr($woocommerce_custom_product_number_field));

    $woocommerce_custom_procut_textarea = $_POST['_custom_product_textarea'];
    if (!empty($woocommerce_custom_procut_textarea))
        update_post_meta($post_id, '_custom_product_textarea', esc_html($woocommerce_custom_procut_textarea));
}

?>
<?php
// add_action('woocommerce_before_add_to_cart_quantity', 'woo_show_custom_fields_at_product_page');
// function  woo_show_custom_fields_at_product_page(){

//     echo get_post_meta(get_the_ID(), '_custom_product_text_field', true);

//     echo get_post_meta(get_the_ID(), '_custom_product_number_field', true);

//     echo get_post_meta(get_the_ID(), '_custom_product_textarea', true);
// }

// add_action('woocommerce_after_cart_table', 'woo_show_custom_fields_at_cart_page');

// function  woo_show_custom_fields_at_cart_page(){
// echo 'hello world';
//     echo get_post_meta(get_the_ID(), '_custom_product_text_field', true);

//     echo get_post_meta(get_the_ID(), '_custom_product_number_field', true);

//     echo get_post_meta(get_the_ID(), '_custom_product_textarea', true);
// }


add_action('woocommerce_before_cart_contents', 'woo_show_custom_fields_at_checkout_page');

function  woo_show_custom_fields_at_checkout_page(){
echo 'hello world';
    echo get_post_meta(get_the_ID(), '_custom_product_text_field', true);

    echo get_post_meta(get_the_ID(), '_custom_product_number_field', true);

    echo get_post_meta(get_the_ID(), '_custom_product_textarea', true);
}

// Display in cart and checkout pages
add_filter( 'woocommerce_cart_item_name', 'customizing_cart_item_name', 10, 3 );
function customizing_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
    $product = $cart_item['data']; // Get the WC_Product Object


    if ( $value = $product->get_meta('my_custom_field') ) {
        $product_name .= '<span class="custom_field_class">'.$value.'</span>';
    }

    return $product_name;


}

// add_filter( 'woocommerce_cart_item_name', 'bbloomer_cart_item_category', 9999, 3 );

// function bbloomer_cart_item_category(){
//     echo 'hello world'
// }


add_filter( 'woocommerce_cart_item_name', 'bbloomer_cart_item_category', 9999, 3 );
 
function bbloomer_cart_item_category( $name, $cart_item, $cart_item_key ) {
 
    echo 'hello world';
   $product = $cart_item['data'];
   if ( $product->is_type( 'variation' ) ) {
      $product = wc_get_product( $product->get_parent_id() );
   }
   
 
   $cat_ids = $product->get_category_ids();
   '<br>';
 
   
   if ( $cat_ids ) $name .= '<br>' . wc_get_product_category_list( $product->get_id(),
    ', ', '<span class="posted_in">' . _n( 'Category:', 
    'Categories:', count( $cat_ids ), 'woocommerce' ) . ' ',
    '</span>' );
 
   return $name;
 
}
    ?>


<?php

    add_filter( 'woocommerce_billing_fields', 'ts_unrequire_wc_phone_field');
function ts_unrequire_wc_phone_field( $fields ) {
$fields['billing_email']['required'] = false;
return $fields;
}

?>

<?php
add_action( 'woocommerce_before_order_itemmeta', 'so_32457241_before_order_itemmeta', 10, 3 );
function so_32457241_before_order_itemmeta( $item_id, $item, $_product ){
    echo 'heloo';
    echo '<p>bacon</p>';
}

?>
<!-- Thank you on admin page -->
<?php

add_filter( 'woocommerce_order_actions', 'bbloomer_show_thank_you_page_order_admin_actions', 9999, 2 );
  
function bbloomer_show_thank_you_page_order_admin_actions( $actions, $order ) {
   if ( $order->has_status( wc_get_is_paid_statuses() ) ) {
      $actions['view_thankyou'] = 'Display thank you page';
   }
   return $actions;
}
  
add_action( 'woocommerce_order_action_view_thankyou', 'bbloomer_redirect_thank_you_page_order_admin_actions' );
  
function bbloomer_redirect_thank_you_page_order_admin_actions( $order ) {
   $url = add_query_arg( 'adm', $order->get_customer_id(), $order->get_checkout_order_received_url() );
   add_filter( 'redirect_post_location', function() use ( $url ) {
      return $url;
   });
}
 
add_filter( 'determine_current_user', 'bbloomer_admin_becomes_user_if_viewing_thank_you_page' );
 
function bbloomer_admin_becomes_user_if_viewing_thank_you_page( $user_id ) {
   if ( ! empty( $_GET['adm'] ) ) {
      $user_id = wc_clean( wp_unslash( $_GET['adm'] ) );
   }
   return $user_id;
}
?>

<!-- coupon code sales calculate on admin page -->

<?php

// 1. Create function that calculates sales based on coupon code
  
function bbloomer_get_sales_by_coupon( $coupon_code ) {
   global $wpdb;
    $total = $wpdb->get_var( "
        SELECT SUM(pm.meta_value)
        FROM $wpdb->posts p
      INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
        INNER JOIN {$wpdb->prefix}woocommerce_order_items as oi ON p.ID = oi.order_id
        WHERE p.post_type = 'shop_order'
      AND pm.meta_key = '_order_total'
        AND p.post_status IN ( 'wc-completed', 'wc-processing')
        AND oi.order_item_type = 'coupon'
        AND oi.order_item_name LIKE '" . $coupon_code . "'
    " );
   return wc_price( $total );
}
  
// -------------------------
// 2. Add new column to WooCommerce Coupon admin table with total sales
  
add_filter( 'manage_edit-shop_coupon_columns', 'bbloomer_admin_shop_coupon_sales_column', 9999 );
  
function bbloomer_admin_shop_coupon_sales_column( $columns ) {
   $columns['totsales'] = 'Total Sales';
   return $columns;
}
  
add_action( 'manage_shop_coupon_posts_custom_column', 'bbloomer_admin_shop_coupon_sales_column_content', 9999, 2 );
  
function bbloomer_admin_shop_coupon_sales_column_content( $column, $coupon_id ) {
    if ( $column == 'totsales' ) {
      echo bbloomer_get_sales_by_coupon( get_the_title( $coupon_id ) );
    }
}

?>

<!-- add table in order page admin panel -->

<?php 
add_filter( 'manage_edit-shop_order_columns', 'bbloomer_add_new_order_admin_list_column' );
 
function bbloomer_add_new_order_admin_list_column( $columns ) {
    $columns['billing_country'] = 'Country';
    return $columns;
}
 
add_action( 'manage_shop_order_posts_custom_column', 'bbloomer_add_new_order_admin_list_column_content' );
 
function bbloomer_add_new_order_admin_list_column_content( $column ) {
   
    global $post;
 
    if ( 'billing_country' === $column ) {
 
        $order = wc_get_order( $post->ID );
        echo $order->get_billing_country();
      
    }
}
?>

<!-- free shipping on cart page and checkout page -->
<?php

add_filter( 'woocommerce_cart_shipping_method_full_label', 'bbloomer_add_0_to_shipping_label', 9999, 2 );
 
function bbloomer_add_0_to_shipping_label( $label, $method ) {
   if ( ! ( $method->cost > 0 ) ) {
      $label .= ': ' . wc_price( 0 );
   }
   return $label;
}
 
add_filter( 'woocommerce_order_shipping_to_display', 'bbloomer_add_0_to_shipping_label_ordered', 9999, 3 );
 
function bbloomer_add_0_to_shipping_label_ordered( $shipping, $order, $tax_display ) {
   if ( ! ( 0 < abs( (float) $order->get_shipping_total() ) ) && $order->get_shipping_method() ) {
      $shipping .= ': ' . wc_price( 0 );
   }
   return $shipping;
}
?>

<!-- for specific ammount -->
<?php
// add_action( 'woocommerce_cart_calculate_fees','bbloomer_woocommerce_deposit' );
 
// function bbloomer_woocommerce_deposit() {
//     $total_minus_100 = WC()->cart->get_cart_contents_total() - 100;
//     WC()->cart->add_fee( 'Balance', $total_minus_100 * -1 );
// }
// ?>


<!-- snippts  -->

<?php
add_action( 'woocommerce_single_product_summary', 'bbloomer_display_pressure_badge', 6 );
 
function bbloomer_display_pressure_badge() {
   echo '<div class="woocommerce-message">Order by 6pm and get it delivered tomorrow!</div>';
}

add_filter( 'woocommerce_get_availability_text', 'bbloomer_edit_left_stock', 9999, 2 );
 
function bbloomer_edit_left_stock( $text, $product ) {
   $stock = $product->get_stock_quantity();
   if ( $product->is_in_stock() && $product->managing_stock() && $stock <= get_option( 'woocommerce_notify_low_stock_amount' ) ) $text .= '. Get it today to avoid 5+ days restocking delay!';
   return $text;
}

add_action( 'woocommerce_single_product_summary', 'bbloomer_add_free_sample_add_cart', 35 );
 
function bbloomer_add_free_sample_add_cart() {
   echo '<p><a href="/?add-to-cart=953" class="button">Add Sample to Cart</a><p>';
}
?>

<!-- create an account on checkout for gust members -->
<?php

add_action( 'woocommerce_thankyou', 'bbloomer_register_guests', 9999 );
 
function bbloomer_register_guests( $order_id ) {
   $order = wc_get_order( $order_id );
   $email = $order->get_billing_email();
   if ( ! email_exists( $email ) && ! username_exists( $email ) ) {
      $customer_id = wc_create_new_customer( $email, '', '', array(
         'first_name' => $order->get_billing_first_name(),
         'last_name'  => $order->get_billing_last_name(),
      ));
      if ( is_wp_error( $customer_id ) ) {
         throw new Exception( $customer_id->get_error_message() );
      }
      wc_update_new_customer_past_orders( $customer_id );
      wc_set_customer_auth_cookie( $customer_id );
   } else {
      $user = get_user_by( 'email', $email );
      wc_update_new_customer_past_orders( $user->ID );
      wc_set_customer_auth_cookie( $user->ID );
   }
}
?>

<?php
add_action('woocommerce_cart_shipping_packages', 'alter_item_shipping_class_for_shipping_cost_change' );
function alter_item_shipping_class_for_shipping_cost_change( $shipping_packages ) {
    foreach ( $shipping_packages as $key => $package ) {
        foreach ( $package['contents'] as $cart_item_key => $cart_item ) {
            if ( true ) {
                $package['contents'][$cart_item_key]['data']->set_shipping_class_id(1947);
            }
        }
    }
    return $shipping_packages;
}
?>

<?php
add_filter( 'woocommerce_package_rates', 'bbloomer_woocommerce_tiered_shipping', 10, 2 );
 
function bbloomer_woocommerce_tiered_shipping( $rates, $package ) {
   $threshold = 100;
   if ( WC()->cart->subtotal < $threshold ) {
      unset( $rates['flat_rate:1'] );
   } else {
      unset( $rates['flat_rate:2'] );
   }
   return $rates;
}
?>