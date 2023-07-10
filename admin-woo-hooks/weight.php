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

add_action('woocommerce_after_cart_table', 'woo_show_custom_fields_at_cart_page');

function  woo_show_custom_fields_at_cart_page(){
echo 'hello world';
    echo get_post_meta(get_the_ID(), '_custom_product_text_field', true);

    echo get_post_meta(get_the_ID(), '_custom_product_number_field', true);

    echo get_post_meta(get_the_ID(), '_custom_product_textarea', true);
}


add_action('woocommerce_after_checkout_billing_form', 'woo_show_custom_fields_at_checkout_page');

function  woo_show_custom_fields_at_checkout_page(){
echo 'hello world';
    echo get_post_meta(get_the_ID(), '_custom_product_text_field', true);

    echo get_post_meta(get_the_ID(), '_custom_product_number_field', true);

    echo get_post_meta(get_the_ID(), '_custom_product_textarea', true);
}
    ?>