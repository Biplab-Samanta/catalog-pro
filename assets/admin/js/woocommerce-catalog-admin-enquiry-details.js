function woocommerce_catalog_enquiry_open_chat(e) {
	jQuery.blockUI({ message: '<h1>'+enquiry_admin.wait_msg+'</h1>' });
	jQuery.ajax({
		type : 'post',
		url : enquiry_admin.ajaxurl,
		data : {
			action : 'infor_enquiry_chat_histry',
			data_product : e.getAttribute('data-option'),
			data_enquiry : e.id,
			data_cov : e.getAttribute('data-conv')
		},
		
	}).done(function( data ) {
		jQuery.unblockUI();
		jQuery(".chat-list").removeClass("active");
		jQuery(".chat-list_"+e.id+"").addClass("active");
		jQuery(".mesgs").html(data['display_chat_histry']);
		jQuery(".hide_unread"+e.id+"").hide();
		jQuery('.msg-history').scrollTop(enquiry_admin.scroll_limit);
	});
}

/*
***	select first option
*/
if (enquiry_admin.first_enquiry_details) {
	jQuery.blockUI({ message: '<h1>'+enquiry_admin.wait_msg+'</h1>' });
	jQuery.ajax({
		type : 'post',
		url : enquiry_admin.ajaxurl,
		data : {
			action : 'infor_enquiry_chat_histry',
			data_product : enquiry_admin.first_enquiry_details[1],
			data_enquiry : enquiry_admin.first_enquiry_details[0],
			data_cov : enquiry_admin.first_enquiry_details[2]
		},

	}).done(function( data ) {
		jQuery.unblockUI();
		jQuery(".chat-list").removeClass("active");
		jQuery(".chat-list_"+enquiry_admin.first_enquiry_details[0]+"").addClass("active");
		jQuery(".mesgs").html(data['display_chat_histry']);
		jQuery(".hide_unread"+enquiry_admin.first_enquiry_details[0]+"").hide();
		jQuery('.msg-history').scrollTop(enquiry_admin.scroll_limit);
	});
}

function woocommerce_catalog_enquiry_send_reply(f) {
	var inputVal = document.getElementById("write_msg").value;       
	if(inputVal != ''){
		jQuery.blockUI({ message: '<h1>'+enquiry_admin.wait_msg+'</h1>' });
		jQuery.ajax({
			type : 'post',
			url : enquiry_admin.ajaxurl,
			data : {
				action : 'infor_enquiry_reply_from_vendor_action',
				inputVal : inputVal,
				data_product : f.getAttribute('data-product1'),
				data_enquiry : f.id
			},
			success : function( data ) {
				jQuery.unblockUI();
				jQuery(".msg-history").append( '<div class="outgoing-msg"><div class="sent-msg"><p>'+inputVal+'</p><span class="time-date"></span> </div></div>' );
				jQuery('.msg-history').scrollTop(enquiry_admin.scroll_limit);
				jQuery("#write_msg").val("");
			}
		});
	} else {
		alert( mvx_catalog.empty_text );     
	}
}

function catalog_status_changes_dropdown_open( fsfsaf ) {
	jQuery(".status-changes-area").slideToggle();
}

function catalog_status_changes_dropdown_close( fsfsaf ) {
	jQuery(".status-changes-area").slideToggle();
}

function status_changes_from_dropdown( changes_data ) {
	var radioValue = jQuery("input[name='enquiry_status']:checked").val();

	jQuery.blockUI({ message: '<h1>'+enquiry_admin.wait_msg+'</h1>' });
	jQuery.ajax({
		type : 'post',
		url : enquiry_admin.ajaxurl,
		data : {
			action : 'enquiry_status_changed',
			enquiry_id : changes_data.getAttribute('data-enqid'),
			radio_value : radioValue
		},
		success : function( data ) {
			jQuery.unblockUI();
			jQuery(".enquiry-status-name").html(radioValue);
			if (radioValue == 'delete') {
				jQuery(".messaging").load(location.href + " .messaging");				
			}
		}
	});

}