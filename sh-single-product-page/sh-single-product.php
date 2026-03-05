<?php

/**
 * Plugin Name: SH Custom Single Product Page
 * Description: Custom WooCommerce single product layout shortcode [sh_custom_product_page]
 * Version: 1.0.1
 * Author: Shoaib
 */

if (! defined('ABSPATH')) {
  exit;
}

class SH_Custom_Product_Page
{

  public function __construct()
  {
    add_action('wp_enqueue_scripts', array($this, 'register_assets'));
    add_shortcode('sh_custom_product_page', array($this, 'render_shortcode'));
    add_action('wp_ajax_sh_add_to_cart', array($this, 'ajax_add_to_cart'));
    add_action('wp_ajax_nopriv_sh_add_to_cart', array($this, 'ajax_add_to_cart'));
  }

  public function register_assets()
  {
    wp_register_style('sh-custom-product-style', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0.1');
    wp_register_script('sh-custom-product-script', plugin_dir_url(__FILE__) . 'script.js', array(), '1.0.1', true);
  }

  public function render_shortcode()
  {
    // Enqueue dynamically to handle page builders and shortcodes anywhere
    wp_enqueue_style('sh-custom-product-style');
    wp_enqueue_script('sh-custom-product-script');

    global $product;
    if (! is_a($product, 'WC_Product')) {
      $product = wc_get_product(get_the_ID());
    }

    if (! is_a($product, 'WC_Product')) {
      return '<p>No product found.</p>';
    }

    // Prepare variation data if it's a variable product
    $variations_data = array();
    if ($product->is_type('variable')) {
      $available_variations = $product->get_available_variations();
      foreach ($available_variations as $variation) {
        $var_obj = wc_get_product($variation['variation_id']);

        // Get standard WooCommerce variation image
        $image_id = $variation['image_id'];
        $main_image_src = wp_get_attachment_image_url($image_id, 'woocommerce_single');
        $main_image_thumb = wp_get_attachment_image_url($image_id, 'woocommerce_gallery_thumbnail');

        // Get Variation Product Gallery images 
        // Based on the user's DOM snapshot, the input name is woo_variation_gallery[1728][]
        // Let's assume the meta key is woo_variation_gallery
        $gallery_images = array();

        $gallery_image_ids = get_post_meta($variation['variation_id'], 'woo_variation_gallery', true);

        // Fallback checks just in case
        if (empty($gallery_image_ids)) {
          $gallery_image_ids = get_post_meta($variation['variation_id'], 'woo_variation_gallery_images', true);
        }
        if (empty($gallery_image_ids)) {
          $gallery_image_ids = get_post_meta($variation['variation_id'], '_wc_additional_variation_images', true);
          if (! is_array($gallery_image_ids) && ! empty($gallery_image_ids)) {
            $gallery_image_ids = explode(',', $gallery_image_ids);
          }
        }

        if (! empty($gallery_image_ids) && is_array($gallery_image_ids)) {
          foreach ($gallery_image_ids as $gal_id) {
            $gallery_images[] = array(
              'full'  => wp_get_attachment_image_url($gal_id, 'woocommerce_single'),
              'thumb' => wp_get_attachment_image_url($gal_id, 'woocommerce_gallery_thumbnail'),
            );
          }
        }

        $variations_data[] = array(
          'variation_id' => $variation['variation_id'],
          'attributes'   => $variation['attributes'], // e.g., array('attribute_pa_color' => 'red')
          'main_image'   => array(
            'full'  => $main_image_src,
            'thumb' => $main_image_thumb,
          ),
          'gallery'      => $gallery_images,
        );
      }
    }

    wp_localize_script('sh-custom-product-script', 'shProductData', array(
      'variations' => $variations_data,
      'is_variable' => $product->is_type('variable'),
      'product_id' => $product->get_id(),
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('sh_add_to_cart_nonce'),
      'quote_url' => $this->get_quote_page_url(),
    ));

    ob_start();
    include plugin_dir_path(__FILE__) . 'layout.php';
    return ob_get_clean();
  }

  public function ajax_add_to_cart()
  {
    check_ajax_referer('sh_add_to_cart_nonce', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $items = isset($_POST['items']) ? $_POST['items'] : array();

    if (! $product_id || empty($items)) {
      wp_send_json_error(array('message' => 'Invalid data.'));
    }

    $added = false;

    // Check if YITH Request a Quote is active
    $yith_active = function_exists('YITH_Request_Quote');

    foreach ($items as $item) {
      $qty = isset($item['qty']) ? absint($item['qty']) : 1;
      if ($qty <= 0) continue;

      if ($yith_active) {
        // Prepare item for YITH Request a Quote
        $quote_item = array(
          'product_id' => $product_id,
          'quantity'   => $qty,
        );

        if (isset($item['variation_id']) && $item['variation_id'] > 0) {
          $quote_item['variation_id'] = absint($item['variation_id']);
          // YITH usually expects the full varied attributes if any
          if (isset($item['attributes'])) {
            $quote_item['variation'] = array_map('sanitize_text_field', $item['attributes']);
          }
        }

        // Add to YITH list
        YITH_Request_Quote()->add_item($quote_item);
        $added = true;
      } else {
        // Fallback to WooCommerce Cart
        if (isset($item['variation_id']) && $item['variation_id'] > 0) {
          $variation_id = absint($item['variation_id']);
          $variation_data = isset($item['attributes']) ? array_map('sanitize_text_field', $item['attributes']) : array();
          $cart_item_key = WC()->cart->add_to_cart($product_id, $qty, $variation_id, $variation_data);
        } else {
          $cart_item_key = WC()->cart->add_to_cart($product_id, $qty);
        }

        if ($cart_item_key) {
          $added = true;
        }
      }
    }

    if ($added) {
      if ($yith_active) {
        // Always redirect to quote page when YITH is active.
        $raq_url = $this->get_quote_page_url();
        wp_send_json_success(array(
          'message'  => 'Items added to quote successfully.',
          'cart_url' => $raq_url
        ));
      } else {
        WC()->cart->calculate_totals();
        wp_send_json_success(array(
          'message'  => 'Items added to cart successfully.',
          'cart_url' => wc_get_cart_url()
        ));
      }
    } else {
      wp_send_json_error(array('message' => 'Could not add items to quote.'));
    }
  }

  private function get_quote_page_url()
  {
    if (function_exists('yith_get_request_quote_page_url')) {
      $url = yith_get_request_quote_page_url();
      if (! empty($url)) {
        return $url;
      }
    }

    $page_id = (int) get_option('ywraq_page_id');
    if (! $page_id) {
      $page_id = (int) get_option('yith_ywraq_page_id');
    }
    if ($page_id > 0) {
      $url = get_permalink($page_id);
      if (! empty($url)) {
        return $url;
      }
    }

    return home_url('/request-a-quote/');
  }
}

new SH_Custom_Product_Page();
