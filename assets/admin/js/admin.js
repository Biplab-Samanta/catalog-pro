jQuery(document).ready(function($) {

	$('#second-choice').select2({
				placeholder: setting_enquiry_filter_data.type_text
			});

	$('#first-choice').select2();
	$('#others-choice').select2();

	if($('#product-enquiry-position').val() ==1) {
		var parrent_ele = $('#custom-enquiry-position').parent().parent();
		parrent_ele.show();
	}
	else {
		var parrent_ele = $('#custom-enquiry-position').parent().parent();
		parrent_ele.hide();
	}

	if($('#is-replace-price-with-txt').is(':checked')) {
		var parrent_ele = $('#replace-text-in-price').parent().parent();
		$('#is-remove-price').attr('checked', false);
		parrent_ele.show();
	}
	else {
		var parrent_ele = $('#replace-text-in-price').parent().parent();
		parrent_ele.hide();
	}

	$('#product-enquiry-position').change(function() {
		if($(this).val() == 1) {
			var parrent_ele = $('#custom-enquiry-position').parent().parent();
			parrent_ele.show('slow');
		}
		else {
			var parrent_ele = $('#custom-enquiry-position').parent().parent();
			parrent_ele.hide('slow');
		}
	});
	$('#is-replace-price-with-txt').change(function() {
		if($(this).is(":checked")) {
			var parrent_ele = $('#replace-text-in-price').parent().parent();
			$('#is-remove-price').attr('checked', false);
			parrent_ele.show('slow');
		}
		else {
			var parrent_ele = $('#replace-text-in-price').parent().parent();
			parrent_ele.hide('slow');
		}
	});

	// Email Template Settings section //
	$('#woocommerce-catalog-eml-tpl .woocommerce-catalog-eml-tpl-cell').click(function(e) {
		e.preventDefault();
		$(this).addClass('selected').parents().siblings().children('.woocommerce-catalog-eml-tpl-cell').removeClass('selected');
		var tpl_id = $(this).attr('data-tpl');
		$('#selected_email_tpl').val(tpl_id);
	});
	$('#second-choice').hide();
	$("#first-choice").change(function() {
		var $dropdown = $(this);
		var key = $dropdown.val();
		var vals;
		switch(key) {
			case 'product_name':
			vals = setting_enquiry_filter_data.form_data_product;
			break;
			case 'customer_name':
			vals = setting_enquiry_filter_data.form_data_customer;
			break;
			case 'enquiry_number':
			vals = setting_enquiry_filter_data.enquiry_titles;
			break;
			case 'base':
			vals = ['Please choose from above'];
		}
		
		var $secondChoice = $("#second-choice");
		$secondChoice.empty();
		$.each(vals, function(index, value) {
			$secondChoice.append("<option value="+index+" >" + value + "</option>");
		});

	});



	$( ".catalog_start_date_order" ).datepicker( {
        dateFormat: 'yy-mm-dd',
        onClose: function ( selectedDate ) {
            $( ".catalog_end_date_order" ).datepicker( "option", "minDate", selectedDate );
        }
    } );
    $( ".catalog_end_date_order" ).datepicker( {
        dateFormat: 'yy-mm-dd',
        onClose: function ( selectedDate ) {
            $( ".catalog_start_date_order" ).datepicker( "option", "maxDate", selectedDate );
        }
    } );

    /************	Replace price with text	******************/
    $('.is-replace-price-with-txt').change(function() {
    	if($(this).is(":checked")) {
    		var parrent_ele = $('#replace-text-in-price').parent().parent();
    		$('.is-remove-price').attr('checked', false);
    		$('.is_enable_add_to_cart').attr('checked', false);
    		parrent_ele.show('slow');
    	}
    	else {
    		var parrent_ele = $('#replace-text-in-price').parent().parent();
    		parrent_ele.hide('slow');
    	}
    });

    if($('.is-replace-price-with-txt').is(':checked')) {
    	var parrent_ele = $('#replace-text-in-price').parent().parent();
    	$('.is-remove-price').attr('checked', false);
    	$('.is_enable_add_to_cart').attr('checked', false);
    	parrent_ele.show();
    }
    else {
    	var parrent_ele = $('#replace-text-in-price').parent().parent();
    	parrent_ele.hide();
    }

    /*********	Remove add to cart when remove price	**************/
    $('.is-remove-price').change(function() {
    	if($(this).is(":checked")) {
    		$('.is_enable_add_to_cart').attr('checked', false);
    	}
    });
    // Allow user to redirect custom pages
    $("#disable-cart-page-link").removeAttr("disabled"); 

});