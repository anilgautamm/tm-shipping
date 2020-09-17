<?php
class TM_SHIPPING_CHECKOUT {

    public function __construct() {

    	 add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts'), 100);
       	 add_filter( 'woocommerce_cart_ready_to_calc_shipping', array($this,'disable_shipping_calc_on_cart'), 99 );
       	 add_action( 'woocommerce_after_checkout_billing_form', array($this,'display_extra_fields_after_billing_address') , 10, 1 );
       	 add_action( 'woocommerce_checkout_process', array($this,'custom_shipping_checkout_field_process'));
       	 add_action( 'woocommerce_checkout_update_order_meta', array($this,'custom_shipping_checkout_field_update_order_meta'), 10, 1);
       	 add_action( 'woocommerce_after_checkout_validation', array($this,'custom_shipping_after_checkout_validate'),10,2);
       	 add_filter( 'woocommerce_checkout_fields',array($this, 'custom_reorder_fields'),10,1 );
    }

    function enqueueScripts(){

        wp_enqueue_style(' tm-shipping-checkout-css', TM_SHIPPING_CHECKOUT_URL . "css/tm_shipping_checkout.css");
        wp_enqueue_script('tm-shipping-checkout-js',  TM_SHIPPING_CHECKOUT_URL . "js/tm_shiping_checkout.js", array('jquery'));

    }

    function disable_shipping_calc_on_cart( $show_shipping ) {
	    if( is_cart() ) {
	        return false;
	    }
	    return $show_shipping;
	}


	function display_extra_fields_after_billing_address($checkout){


		$checkout_methods = $checkout_method_filed_options =[];
		$not_only_local_pickup_method = false;

		 $packages  = WC()->shipping()->get_packages();
	
		 foreach ( $packages as $i => $package ) {
		 	
		 	$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
		
		 	$available_methods  = $package['rates'];
		 	foreach ( $available_methods as $j => $method ) {

		 		if( ! array_key_exists($method->method_id, $checkout_methods) ){

	 				$checkout_methods[$method->method_id] = $method->label;
	 				
	 				if( $method->method_id != 'local_pickup'){
	 					$not_only_local_pickup_method = true;
	 				}
		 		}
		 	}

		}

        if(!empty($checkout_methods)){

    			$checkout_method_filed_options['local_pickup'] = 'Local pickup';

    		// if($checkout_methods['wf_shipping_ups'] == true){
    			$checkout_method_filed_options['wf_shipping_ups'] = 'UPS Shipping';
    		// }

        }

     
	     echo '<div id="custom_checkout_shipping_field">';
	     woocommerce_form_field('custom_checkout_shipping', array(
	     												'type'  => 'radio',
	     												'id'	=> 'custom_checkout_shipping_id',
	     												'class' => array('form-row-wide','checkout_shipping_input_class'),
	     												'label' => __('Shipping Options') ,
	     												'options' => $checkout_method_filed_options,
	     												'input_class' => 'cos_input_f_class',
	     												'required' => true
	     											),
	     $checkout->get_value('custom_checkout_shipping'));
	     echo '</div>';

	     //remove checkbox
	     if($not_only_local_pickup_method == false){
	     		$this->tm_remove_ship_to_different_add_checkbox_action();
	     }

	}


	function tm_remove_ship_to_different_add_checkbox_action(){
		add_action('wp_footer',array($this,'tm_remove_ship_to_different_add_checkbox'),100);
	}

	function tm_remove_ship_to_different_add_checkbox(){
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){ 
				if(jQuery('#ship-to-different-address-checkbox').is(':checked') == true){
					jQuery('#ship-to-different-address-checkbox').trigger('click');
					jQuery('#ship-to-different-address').hide();
				}
			});
		</script>
		<?php
	}


	function custom_shipping_checkout_field_process(){
		//pr($_POST);die;
		if ( ! $_POST['custom_checkout_shipping'] ){
        	wc_add_notice( __( 'Please choose Shipping Option.' ), 'error' );
		}
	}

	function custom_shipping_checkout_field_update_order_meta( $order_id ){

		if ( ! empty( $_POST['custom_checkout_shipping'] ) ) {
	        update_post_meta( $order_id, 'custom_checkout_shipping', sanitize_text_field( $_POST['custom_checkout_shipping'] ) );
	    }
	}

	function custom_shipping_after_checkout_validate($data,$errors){

		if( in_array('local_pickup:12', $data['shipping_method']) && !empty($_POST['custom_checkout_shipping']) && $_POST['custom_checkout_shipping'] === 'wf_shipping_ups' ){
			
			$errors->add( 'validation', 'Please choose Shipping Method in Your Order section.' ); 

		}elseif( !empty($data['shipping_method']) && !in_array('local_pickup:12', $data['shipping_method']) && !empty($_POST['custom_checkout_shipping']) && $_POST['custom_checkout_shipping'] === 'local_pickup' ){
				
			$errors->add( 'validation', 'Please choose Shipping Method in Your Order section.' ); 
		}
	}

	function custom_reorder_fields($checkout_fields ){

		$checkout_fields['billing']['billing_phone']['priority'] = 31;
		$checkout_fields['billing']['billing_email']['priority'] = 32;
		$checkout_fields['billing']['billing_country']['priority'] = 81;
		$checkout_fields['shipping']['shipping_country']['priority'] = 110;


		$checkout_fields['billing']['billing_first_name']['class'][] = 'custom_reorder_field_left';
		$checkout_fields['billing']['billing_last_name']['class'][]  = 'custom_reorder_field_left';
		$checkout_fields['billing']['billing_company']['class'][] 	 = 'custom_reorder_field_right';
		$checkout_fields['billing']['billing_phone']['class'][] 	 = 'custom_reorder_field_right';
		$checkout_fields['billing']['billing_email']['class'][] 	 = 'custom_reorder_field_left';
		$checkout_fields['billing']['billing_country']['class'][] 	 = 'custom_reorder_field_right';
		$checkout_fields['billing']['billing_address_1']['class'][]	 = 'custom_reorder_field_left';
		$checkout_fields['billing']['billing_address_2']['class'][]  = 'custom_reorder_field_right';
		$checkout_fields['billing']['billing_city']['class'][] 	 	 = 'custom_reorder_field_left';
		$checkout_fields['billing']['billing_state']['class'][] 	 = 'custom_reorder_field_right';
		$checkout_fields['billing']['billing_postcode']['class'][] 	 = 'custom_reorder_field_left';


		$checkout_fields['shipping']['shipping_first_name']['class'][] 	= 'custom_reorder_field_left';
		$checkout_fields['shipping']['shipping_last_name']['class'][]  	= 'custom_reorder_field_left';
		$checkout_fields['shipping']['shipping_company']['class'][]		= 'custom_reorder_field_right';
		$checkout_fields['shipping']['shipping_country']['class'][] 	= 'custom_reorder_field_right';
		$checkout_fields['shipping']['shipping_address_1']['class'][]	= 'custom_reorder_field_left';
		$checkout_fields['shipping']['shipping_address_2']['class'][]   = 'custom_reorder_field_right';
		$checkout_fields['shipping']['shipping_city']['class'][] 	 	= 'custom_reorder_field_left';
		$checkout_fields['shipping']['shipping_state']['class'][] 	 	= 'custom_reorder_field_right';
		$checkout_fields['shipping']['shipping_postcode']['class'][] 	= 'custom_reorder_field_left';

		return $checkout_fields;
	}

}

$obj = new TM_SHIPPING_CHECKOUT();