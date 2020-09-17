jQuery(document).ready(function(){
	

	/* checkout shipping options  */
	if(jQuery('body').hasClass('woocommerce-checkout')){
		jQuery('.woocommerce-shipping-methods').hide();
		jQuery( '.woocommerce-account-fields' ).insertBefore( "#custom_checkout_shipping_field" );
		//_uncheckAll_shippingOptionsCheckout();
	}
	

	jQuery(document).on('change','#custom_checkout_shipping_id_field input',function(){
		
		jQuery('body').addClass('custom_shipping_option_selected');
		console.log(jQuery(this).val());
		
		if(jQuery(this).val() == 'local_pickup'){


			if(jQuery('#ship-to-different-address-checkbox').is(':checked') == true){
				jQuery('#ship-to-different-address-checkbox').trigger('click');	
			}

			jQuery('#ship-to-different-address').hide(); 
			jQuery('.woocommerce-shipping-methods').show();

			jQuery('#shipping_method li').each(function(){
				if(jQuery(this).find('input').val() == 'local_pickup:12' || jQuery(this).find('input').val() == 'flat_rate:14' ){
					if(jQuery(this).find('input').is(':checked') == false){
						jQuery(this).find('input').trigger('click');
					}
				} else {
					jQuery(this).hide();
				}
			});

			jQuery('#custom_checkout_shipping_id_field').removeClass('selected_custom_method_ups');
			jQuery('#custom_checkout_shipping_id_field').addClass('selected_custom_method_local_pickup');

		}else{

			jQuery('#ship-to-different-address').show(); 
			if(jQuery('#ship-to-different-address-checkbox').is(':checked') == false){
				
				//jQuery('#ship-to-different-address-checkbox').trigger('click');
			}
			// flat_rate:16

			var upc_select = false;
			jQuery('#shipping_method li').each(function(){

			// if(str1.indexOf(str2) != -1){
			//     console.log(str2 + " found");
			// }

			if(jQuery(this).find('input').val() != 'local_pickup:12' || jQuery(this).find('input').val() != 'flat_rate:14'){
				if(jQuery(this).find('input').is(':checked') == false){
					if(upc_select == false){
						jQuery(this).find('input').trigger('click');
						upc_select = true;
					}
				}
			}
		});
			
			jQuery('#custom_checkout_shipping_id_field').removeClass('selected_custom_method_local_pickup');
			jQuery('#custom_checkout_shipping_id_field').addClass('selected_custom_method_ups');
		}
	});
});


/* perform actions on checout order update  */
jQuery( 'body' ).on( 'updated_checkout', function() {
	/*if checkout is updated on custom selection */
	if(jQuery('body').hasClass('custom_shipping_option_selected')){

		var selected_custom_method = '';
		if(jQuery('#custom_checkout_shipping_id_field').hasClass('selected_custom_method_local_pickup')){
			selected_custom_method = 'local_pickup';
		}
		if(jQuery('#custom_checkout_shipping_id_field').hasClass('selected_custom_method_ups')){
			selected_custom_method = 'ups';
		}

		if(jQuery('#shipping_method li').length > 1){
			var upc_select = false;
			jQuery('#shipping_method li').each(function(){

				if(selected_custom_method == 'local_pickup'){
					if(jQuery(this).find('input').val() != 'local_pickup:12'){
						jQuery(this).hide();
					}

				} else if(selected_custom_method == 'ups'){

					if(jQuery(this).find('input').val() == 'local_pickup:12'){

						jQuery(this).hide();
					}
				    //jQuery(this).find('input').prop('checked',false)
				}
			});
		}

	}else{ /* loaded by default with billing address */

		jQuery('.woocommerce-shipping-methods').hide();
		var ups_option_added = false;
		jQuery('#shipping_method li').each(function(){
			if(jQuery(this).find('input').val() != 'local_pickup:12' && jQuery('#custom_checkout_shipping_id_wf_shipping_ups').length == 0 ){
				if(ups_option_added == false){

					var cs_html = '<input type="radio" class="input-radio " value="wf_shipping_ups" name="custom_checkout_shipping" id="custom_checkout_shipping_id_wf_shipping_ups">';
					cs_html += '<label for="custom_checkout_shipping_id_wf_shipping_ups" class="radio ">UPS Shipping</label>';

					jQuery('.checkout_shipping_input_class .woocommerce-input-wrapper').append(cs_html);
					ups_option_added = true;
				}
			}
		});
	}


	jQuery('.woocommerce-shipping-totals.shipping .conditional-shipping-notice').each(function(e){
			//if(e >0){
				jQuery(this).remove();
				console.log(e+'.  e ')
			//}
		});
	

});
jQuery(window).load(function(){
	/* checkout fields re-oreder billing & shipping */
	_addCheckoutBillingFieldsWrapper();
});

function _uncheckAll_shippingOptionsCheckout(){
	if(jQuery('#shipping_method li').length > 0){
		jQuery('#shipping_method li').each(function(){
			jQuery(this).find('input').prop('checked',false)
		});
	}
}
function _addCheckoutBillingFieldsWrapper(){

	/* checkout fields re-oreder billing & shipping */
	if(jQuery('.woocommerce-billing-fields__field-wrapper .custom_reorder_field_section_right').length == 0){
		jQuery('.woocommerce-billing-fields__field-wrapper').append('<div class="custom_reorder_field_section_left"></div><div class="custom_reorder_field_section_right"></div>');

		jQuery('.woocommerce-billing-fields__field-wrapper .custom_reorder_field_right').each(function(){
			jQuery(this).appendTo('.woocommerce-billing-fields__field-wrapper .custom_reorder_field_section_right');
		});
		jQuery('.woocommerce-billing-fields__field-wrapper .custom_reorder_field_left').each(function(){
			jQuery(this).appendTo('.woocommerce-billing-fields__field-wrapper .custom_reorder_field_section_left');
		});
	}

	if(jQuery('.woocommerce-shipping-fields__field-wrapper .custom_reorder_field_section_right').length == 0){
		jQuery('.woocommerce-shipping-fields__field-wrapper').append('<div class="custom_reorder_field_section_left"></div><div class="custom_reorder_field_section_right"></div>');
		
		jQuery('.woocommerce-shipping-fields__field-wrapper .custom_reorder_field_right').each(function(){
			jQuery(this).appendTo('.woocommerce-shipping-fields__field-wrapper .custom_reorder_field_section_right');
		});
		jQuery('.woocommerce-shipping-fields__field-wrapper .custom_reorder_field_left').each(function(){
			jQuery(this).appendTo('.woocommerce-shipping-fields__field-wrapper .custom_reorder_field_section_left');
		});
	}

}

jQuery(document).on('change', '#billing_postcode, #billing_state', function() {
	// checkShipping();
	setInterval(checkShipping, 5000);
});

function checkShipping() {
	var findM = jQuery(document).find('#shipping_method_0_wf_shipping_ups13');
	var shipM = jQuery(document).find('#custom_checkout_shipping_id_wf_shipping_ups');

	var findL = jQuery(document).find('#custom_checkout_shipping_id_local_pickup');
	
	if( findM.length > 0) {
		shipM.show();
		jQuery(shipM).next('label').show();
	} else {
		shipM.hide();
		jQuery(shipM).next('label').hide();
	}

	jQuery('#shipping_method li').each(function(){
		if(jQuery(this).find('input').val() == 'local_pickup:12' || jQuery(this).find('input').val() == 'flat_rate:14' )
		{
			shipM.hide();
			jQuery(shipM).next('label').hide();
		} else {
			findL.hide();
			jQuery(findL).next('label').hide();
		}
	});

}