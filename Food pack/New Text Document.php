<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://techosolution.com
 * @since             1.0.0
 * @package           Meal_Customization
 *
 * @wordpress-plugin
 * Plugin Name:       Meal Customization
 * Plugin URI:        https://techosolution.com
 * Description:       Meal Customization
 * Version:           1.0.0
 * Author:            Shoaib
 * Author URI:        https://techosolution.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       meal-customization
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MEAL_CUSTOMIZATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-meal-customization-activator.php
 */
function activate_meal_customization() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-meal-customization-activator.php';
	Meal_Customization_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-meal-customization-deactivator.php
 */
function deactivate_meal_customization() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-meal-customization-deactivator.php';
	Meal_Customization_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_meal_customization' );
register_deactivation_hook( __FILE__, 'deactivate_meal_customization' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-meal-customization.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_meal_customization() {
    $plugin = new Meal_Customization();
    $plugin->run();
}
run_meal_customization();

add_action('woocommerce_before_cart', 'check_cart_quantities');

function check_cart_quantities() {
    $cart_items = WC()->cart->get_cart();
    $pack_quantity = 0;
    $cart_quantity = 0;

    foreach ($cart_items as $cart_item_key => $cart_item) {
        if (isset($cart_item['wsf_item_data']) && !empty($cart_item['wsf_item_data'])) {
            foreach ($cart_item['wsf_item_data'] as $meta_data) {
                if (isset($meta_data['key']) && $meta_data['key'] === 'Select your pack') {
                    $pack_quantity = intval($meta_data['value']);
                }
            }
        }
        $cart_quantity += $cart_item['quantity'];
    }

    if ($cart_quantity < $pack_quantity) {
        wc_print_notice('Please complete your food pack.', 'error');
        add_action('wp_footer', 'disable_checkout_button');
    } elseif ($cart_quantity > $pack_quantity) {
        wc_print_notice('Please Select Another food pack.', 'error');
        add_action('wp_footer', 'disable_checkout_button');
    }
}
function disable_checkout_button() {
    ?>
    <style>
        ul.woocommerce-error {
    width: 100%;
}
    </style>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var checkoutButton = $('.checkout-button');
            checkoutButton.addClass('disabled').click(function(event) {
                event.preventDefault();
            });
        });
    </script>
    <?php
}
