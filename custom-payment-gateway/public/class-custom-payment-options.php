<?php

// Add custom payment gateways
add_filter('woocommerce_payment_gateways', 'add_custom_payment_gateways');
function add_custom_payment_gateways($gateways){
    $gateways[] = 'WC_Custom_CashApp_Gateway';
    $gateways[] = 'WC_Custom_Paypal_Gateway';
    $gateways[] = 'WC_Custom_Bitcoin_Gateway';
    $gateways[] = 'WC_Custom_Western_Union_Gateway';
    $gateways[] = 'WC_Custom_Credit_Card_Gateway';
    return $gateways;
}

// Custom CashApp Gateway
add_action('plugins_loaded', 'init_custom_cashapp_gateway');
function init_custom_cashapp_gateway(){
    class WC_Custom_CashApp_Gateway extends WC_Payment_Gateway {
        public function __construct(){
            $this->id = 'custom_cashapp_gateway';
            $this->method_title = 'CashAPP';
            $this->method_description = 'Place order and we will share the details.';
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();
            
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
        
        public function init_form_fields(){
            $this->form_fields = array(
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Payment method title.',
                    'default'     => 'CashAPP'
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Payment method description.',
                    'default'     => 'Place order and we will share the details.'
                )
            );
        }
        
        public function process_payment($order_id){
            $order = wc_get_order($order_id);
            $order->update_status('on-hold', __('Awaiting payment confirmation.', 'woocommerce'));
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }
}

// Custom Paypal Gateway
add_action('plugins_loaded', 'init_custom_paypal_gateway');
function init_custom_paypal_gateway(){
    class WC_Custom_Paypal_Gateway extends WC_Payment_Gateway {
        public function __construct(){
            $this->id = 'custom_paypal_gateway';
            $this->method_title = 'Paypal';
            $this->method_description = 'None of the other payment options are suitable for you? Please drop us a note about your favourable payment option, and we will contact you as soon as possible.';
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();
            
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
        
        public function init_form_fields(){
            $this->form_fields = array(
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Payment method title.',
                    'default'     => 'Paypal'
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Payment method description.',
                    'default'     => 'None of the other payment options are suitable for you? Please drop us a note about your favorable payment option, and we will contact you as soon as possible.'
                )
            );
        }
        
        public function process_payment($order_id){
            $order = wc_get_order($order_id);
            $order->update_status('on-hold', __('Awaiting payment confirmation.', 'woocommerce'));
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }
}

// Custom Bitcoin Gateway
add_action('plugins_loaded', 'init_custom_bitcoin_gateway');
function init_custom_bitcoin_gateway(){
    class WC_Custom_Bitcoin_Gateway extends WC_Payment_Gateway {
        public function __construct(){
            $this->id = 'custom_bitcoin_gateway';
            $this->method_title = 'Bitcoin';
            $this->method_description = 'Place order and we will share the details.';
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();
            
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
        
        public function init_form_fields(){
            $this->form_fields = array(
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Payment method title.',
                    'default'     => 'Bitcoin'
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Payment method description.',
                    'default'     => 'Place order and we will share the details.'
                )
            );
        }
        
        public function process_payment($order_id){
            $order = wc_get_order($order_id);
            $order->update_status('on-hold', __('Awaiting payment confirmation.', 'woocommerce'));
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }
}

// Custom Western Union / MoneyGram Gateway
add_action('plugins_loaded', 'init_custom_western_union_gateway');
function init_custom_western_union_gateway(){
    class WC_Custom_Western_Union_Gateway extends WC_Payment_Gateway {
        public function __construct(){
            $this->id = 'custom_western_union_gateway';
            $this->method_title = 'Western Union / MoneyGram';
            $this->method_description = 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.';
            $this->has_fields = false;
            $this->init_form_fields();
            $this->init_settings();
            
            $this->title =
            $this->get_option('title');
            $this->description = $this->get_option('description');
            
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
        
        public function init_form_fields(){
            $this->form_fields = array(
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Payment method title.',
                    'default'     => 'Western Union / MoneyGram'
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Payment method description.',
                    'default'     => 'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.'
                )
            );
        }
        
        public function process_payment($order_id){
            $order = wc_get_order($order_id);
            $order->update_status('on-hold', __('Awaiting payment confirmation.', 'woocommerce'));
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
    }
}

// Custom Credit Card Gateway
add_action('plugins_loaded', 'init_custom_credit_card_gateway');
function init_custom_credit_card_gateway(){
    class WC_Custom_Credit_Card_Gateway extends WC_Payment_Gateway {
        public function __construct(){
            $this->id = 'custom_credit_card_gateway';
            $this->method_title = 'Credit Card';
            $this->method_description = 'Please enter your credit card details.';
            $this->has_fields = true;
            $this->init_form_fields();
            $this->init_settings();
            
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_credit_card_form_fields', array($this, 'custom_credit_card_form_fields'));
            add_action('woocommerce_checkout_process', array($this, 'validate_credit_card_fields'));
            add_action('woocommerce_checkout_update_order_meta', array($this, 'save_credit_card_fields'));
        }
        
        public function init_form_fields(){
            $this->form_fields = array(
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'Payment method title.',
                    'default'     => 'Credit Card'
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'Payment method description.',
                    'default'     => 'Please enter your credit card details.'
                )
            );
        }
        
        public function process_payment($order_id){
            $order = wc_get_order($order_id);
            $order->update_status('on-hold', __('Awaiting payment confirmation.', 'woocommerce'));
            $order->reduce_order_stock();
            WC()->cart->empty_cart();
            
            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }
        
        public function custom_credit_card_form_fields(){
            echo '<div id="custom_credit_card_fields">';
            echo '<h3>' . __('Credit Card Details', 'woocommerce') . '</h3>';
            echo '<div class="form-row form-row-wide">';
            echo '<label for="' . esc_attr($this->id) . '-card-number">' . __('Card Number', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input type="text" class="input-text" name="' . esc_attr($this->id) . '-card-number" id="' . esc_attr($this->id) . '-card-number" />';
            echo '</div>';
            echo '<div class="form-row form-row-first">';
            echo '<label for="' . esc_attr($this->id) . '-card-name">' . __('Cardholder Name', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input type="text" class="input-text" name="' . esc_attr($this->id) . '-card-name" id="' . esc_attr($this->id) . '-card-name" />';
            echo '</div>';
            echo '<div class="form-row form-row-last">';
            echo '<label for="' . esc_attr($this->id) . '-card-expiry">' . __('Expiry Date', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input type="text" class="input-text" name="' . esc_attr($this->id) . '-card-expiry" id="' . esc_attr($this->id) . '-card-expiry" />';
            echo '</div>';
            echo '<div class="form-row form-row-wide">';
            echo '<label for="' . esc_attr($this->id) . '-card-cvc">' . __('CVC Code', 'woocommerce') . ' <span class="required">*</span></label>';
            echo '<input type="text" class="input-text" name="' . esc_attr($this->id) . '-card-cvc" id="' . esc_attr($this->id) . '-card-cvc" />';
            echo '</div>';
            echo '<div class="clear"></div>';
            echo '</div>';
        }
        
        public function validate_credit_card_fields(){
            if (empty($_POST[$this->id . '-card-number']) || empty($_POST[$this->id . '-card-name']) || empty($_POST[$this->id . '-card-expiry']) || empty($_POST[$this->id . '-card-cvc'])){
                wc_add_notice(__('All credit card fields are required.', 'woocommerce'), 'error');
            }
        }
        
        public function save_credit_card_fields($order_id){
            $order = wc_get_order($order_id);
            $order->update_meta_data('_' . $this->id . '_card_number', wc_clean($_POST[$this->id . '-card-number']));
            $order->update_meta_data('_' . $this->id . '_card_name', wc_clean($_POST[$this->id . '-card-name']));
            $order->update_meta_data('_' . $this->id . '_card_expiry', wc_clean($_POST[$this->id . '-card-expiry']));
            $order->update_meta_data('_' . $this->id . '_card_cvc', wc_clean($_POST[$this->id . '-card-cvc']));
            $order->save();
        }
    }
}

