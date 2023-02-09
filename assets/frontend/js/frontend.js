jQuery( function( $ ) { 

	var block = function( $node ) {
        if ( ! is_blocked( $node ) ) {
            $node.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
        }
    };

    var is_blocked = function( $node ) {
        return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
    };

    var unblock = function( $node ) {
        $node.removeClass( 'processing' ).unblock();
    };

	$(window).bind('found_variation', function(event, variation) {
		if (variation == null) {
		}else{
			var variation_data = {};
			var count  = 0;
			var chosen = 0;
			var variation_selector = '';
			var variation_id = '';
			if(event.hasOwnProperty('target')){
				variation_selector = event.target;
			}else{
				variation_selector = 'form.variations_form.cart';
			}

			$(variation_selector).find( '.variations select' ).each( function() {
				var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
				var value          = $( this ).val() || '';

				if ( value.length > 0 ) {
					chosen ++;
				}

				count ++;
				variation_data[ attribute_name ] = value;
			});

			if(variation.hasOwnProperty('variation_id')){ /* from Woocommerce 3.0.9 */
				variation_id = variation.variation_id;
				$('#product-data-for-enquiry').val(variation.variation_id);
				$('.wcce-add-enquiry-cart-button').attr('data-product_id', variation.variation_id);
			}else if(variation.hasOwnProperty('id')){
				variation_id = variation.id;
				$('#product-data-for-enquiry').val(variation.id);
				$('.wcce-add-enquiry-cart-button').attr('data-product_id', variation.id);
			}else{
				variation_id = variation.id;
				$('#product-data-for-enquiry').val(variation.id);
				$('.wcce-add-enquiry-cart-button').attr('data-product_id', variation.id);
			}

			var data = {
				'action': 'add_variation_for_enquiry_mail',
				'product_id': variation_id,
				'variation_data': variation_data
			};
			$.post(catalog_enquiry_front.ajaxurl, data, function(response) { 
				console.log(response);														
			});
		}
	});
	$('.variations_form').trigger( 'found_variation' );

	// mini enquiry cart send enquiry modal
	$('#woocommerce-mini-catalog').hide();
	$(".woocommerce-catalog-mini-enquiry-buttons .woocommerce-catalog-send-enquiry").click(function(e){ 
		e.preventDefault();
		$('#woocommerce-mini-catalog').show();
		$("#woocommerce-mini-catalog #responsive").slideToggle(1000);	
	});

	/* Modal Close */
	$(".woocommerce-catalog-mini-enquiry-buttons .catalog-modal .close, .woocommerce-catalog-mini-enquiry-buttons .catalog-modal .btn-default").on('click',function(){
		$("#woocommerce-mini-catalog #responsive").slideToggle(500);
	});

	/* Send enquiry modal */
	$("#woocommerce-catalog-pro .woocommerce-catalog-send-enquiry").click(function(e){ 
		e.preventDefault();
		$("#woocommerce-catalog-pro #responsive").slideToggle(1000);	
	});

	/* Modal Close */
	$("#woocommerce-catalog-pro .catalog-modal .close, #woocommerce-catalog-pro .catalog-modal .btn-default, .woocommerce-catalog-mini-enquiry-buttons .catalog-modal .close, .woocommerce-catalog-mini-enquiry-buttons .catalog-modal .btn-default").on('click',function(){
		$("#woocommerce-catalog-pro #responsive").slideToggle(500);
	});

	/* Submit Enquiry */
	$("#woocommerce-enquiry-form").submit(function(e){
		e.preventDefault();
		/* validate FormData */
		var formData = $(this).serializeArray();
		var formFields = catalog_enquiry_front.wcce_enquiry_form_data;

		if(validateEnquiry(formData,formFields) === true){
			$('.woocommerce-catalog-enquiry-massage').html('<span class="ajaxloader" style="display: inline-block;"></span>');
			/* serialized FormData */
			var data = new FormData($(this)[0]);
			var ajax_url = catalog_enquiry_front.ajaxurl;
			data.append('action', 'send_enquiry_mail');
			if($('#product-enquiry-action').val() == 'single'){
				data.append('quantity', $('.quantity .qty').val());
			}
			block($( '#responsive' ));
			$.ajax({
		        type : 'post',
		        url : ajax_url,
		        data : data,
		        contentType: false,       
				cache: false,             
				processData:false,
		        success : function( response ) {
		        	$('.woocommerce-catalog-enquiry-massage').html('');
		        	if(response.value == 'norecaptcha'){
                                    $('.woocommerce-catalog-enquiry-massage').html('');
                                    $("#loader-after-sumitting-the-form").hide();
                                    $('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+catalog_enquiry_front.error_levels.norecaptcha+'</span>');
		        	}else if(response.value == 'spam'){
                                    $('.woocommerce-catalog-enquiry-massage').html('');
                                    $("#loader-after-sumitting-the-form").hide();
                                    $('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+catalog_enquiry_front.error_levels.spam+'</span>');
		        	}else{
                                    if(response.value==1) {	
                                        $("#loader-after-sumitting-the-form").hide();
                                        $("#responsive").slideToggle(500);
                                        $('#woocommerce-catalo-enquiry-msg').prepend('');
                                        $('#woocommerce-catalo-enquiry-msg').prepend('<div class="woocommerce-message">'+catalog_enquiry_front.ajax_success_msg+'.</div>');		

                                        if(response.for=='multiple'){
                                            $('.wcce-cart-wrapper').html(catalog_enquiry_front.no_more_product);
                                        }
                                        if(typeof(response.settings.is_page_redirect) != 'undefined' && response.settings.is_page_redirect !== null) {
                                            window.location.href=response.redirect_link;
                                        }
                                    }
                                    else {	
                                        $('.woocommerce-catalog-enquiry-massage').html('');
                                        $("#loader-after-sumitting-the-form").hide();
                                        if(response.error_report != '' || response.error_report != 'null'){
                                            $('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+response.error_report+'</span>');
                                        }else{
                                            $('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+catalog_enquiry_front.error_levels.ajax_error+'</span>');
                                        }
                                    }
                                }
                              	unblock($( '#responsive' ));
                                var target = $('#woocommerce-catalo-enquiry-msg');
                                $('html,body').animate({
                                	scrollTop: target.offset().top
                                }, 1000);
                                return false;
                            }
			});

		}
		
	});

	
	function validateEnquiry(formData,formFields){
		
		var valid = true;
		var customFilds = [];
		$.each($(formData), function(i, inputData) {

			if(inputData.name == 'enq_user_name' && inputData.value == ''){
				$('.woocommerce-catalog-enquiry-massage').html('');
				$('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+catalog_enquiry_front.error_levels.name_required+'</span>');		
				$('#'+inputData.name).focus();
				valid = false;
			}else if(inputData.name == 'enq_user_email' && inputData.value == ''){
				$('.woocommerce-catalog-enquiry-massage').html('');
				$('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+catalog_enquiry_front.error_levels.email_required+'</span>');
				$('#'+inputData.name).focus();
				valid = false;
			}else if( inputData.name == 'enq_user_email' && !validateEmail(inputData.value)) {
				$('.woocommerce-catalog-enquiry-massage').html('');
				$('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+catalog_enquiry_front.error_levels.email_invalid+'</span>');
				$('#'+inputData.name).focus();
				valid = false;
			}else{
				if(inputData.name.indexOf('woocommerce_catalog_enquiry_fields') != -1 ){
					$.each($(formFields), function(k, fieldData) {
						
						if(fieldData.label === inputData.value && fieldData.required === true){
							var inputField = inputData.name;
							if(fieldData.type === "file"){
								inputField = inputField.replace('label', '');
								var file = $('input[name="'+inputField+'"]')[0].files[0];
								var enable_types = [];
								var required_types = '';
								$.each($(fieldData.fileType), function(j, type) {
									if(type.selected == true){
										enable_types.push(type.value);
										required_types += type.label+', ';
									}
								});
								if($.inArray(file.type, enable_types ) > -1){
									$('.woocommerce-catalog-enquiry-massage').html('');
									valid = true;
								}else{
									valid = false;
									$('.woocommerce-catalog-enquiry-massage').html('');
									$('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+required_types.replace(/, +$/,'')+" types "+fieldData.label+" "+catalog_enquiry_front.error_levels.is_required+'</span>');
									$('input[name="'+inputField+'"]').focus();
								}
					
							}else{
								inputField = inputField.replace('label', 'value');
								if(findValue(inputField,formData) === ''){
									$('.woocommerce-catalog-enquiry-massage').html('');
									$('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+fieldData.label+" "+catalog_enquiry_front.error_levels.is_required+'</span>');
									$('input[name="'+inputField+'"]').focus();
									valid = false;
								}else{
									$('.woocommerce-catalog-enquiry-massage').html('');
									valid = true;
								}
							}
						}else if(fieldData.label === inputData.value && fieldData.type === "recaptcha"){
							var inputField = inputData.name;
							inputField = inputField.replace('label', 'value');
							if( findValue(inputField,formData) === catalog_enquiry_front.captcha){
								valid = true;
							}else{
								$('.woocommerce-catalog-enquiry-massage').html('');
								$('.woocommerce-catalog-enquiry-massage').html('<span style="color:red">'+fieldData.label+" "+catalog_enquiry_front.error_levels.is_required+'</span>');
								valid = false;
							}
						}
						/*else{
							valid = true;
						}*/
					});
					return valid;
				}
			}
			/* return valid; */
		
		});
		return valid;
	};

	function findValue(keyword, Obj){
		var value = '';
	    $.grep(Obj, function(item){
	    	if(item.name === keyword){
	      	value = item.value;
	      	}
	    });
	    return value;
	};

	function validateEmail($email) {
		var emailReg = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return emailReg.test( $email );
	};

});

var modal = document.getElementById('responsive');
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

