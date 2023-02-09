//*********************************** For MVX ************************************** //
jQuery( function( $ ) { 
  
  $('#second-choice').select2({
        placeholder: mvx_catalog.type_text
      });

  $('#first-choice').select2();
  $('#others-choice').select2();

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


  /********************** Vendor Catalog Enquiry general **********************/
  if($('.vendor-catalog-enquiry-settings #is-replace-price-with-txt').is(':checked')) {
    $('#is-remove-price').attr('checked', false);
  } else {
    $('#alternate-price').hide();
  }

  $('.vendor-catalog-enquiry-settings #is-replace-price-with-txt').change(function() {
    if($(this).is(":checked")) {
      $('#is-remove-price').attr('checked', false);
      $('#alternate-price').show('slow');
    }
    else {
      $('#alternate-price').hide('slow');
    }
  });

  $('#is-remove-price').change(function() { $('#is-replace-price-with-txt').attr('checked', false);$('#alternate-price').hide(); });

  if($('.vendor-catalog-enquiry-settings #is_page_redirect').is(':checked')) {
    $('#redirect-other-page').show();
  }else{
    $('#redirect-other-page').hide();
  }
  $('.vendor-catalog-enquiry-settings #is_page_redirect').change(function() {
    if($(this).is(":checked")) {
      $('#redirect-other-page').show('slow');
    }
    else {
      $('#redirect-other-page').hide('slow');
    }
  });
  if($('.vendor-catalog-enquiry-settings #is-override-form-heading').is(':checked')) {
    $('#custom-form-heading').show();
  }else{
    $('#custom-form-heading').hide();
  }
  $('.vendor-catalog-enquiry-settings #is-override-form-heading').change(function() {
    if($(this).is(":checked")) {
      $('#custom-form-heading').show('slow');
    }
    else {
      $('#custom-form-heading').hide('slow');
    }
  });

  /********************** Vendor Catalog Exclusion **********************/
  $('#woocommerce-product-vendor-list').select2();  
  $('#woocommerce-user-vendor-list').select2(); 
  $('#woocommerce-category-vendor-list').select2(); 

  /********************** Vendor Enquiry Email **********************/
  $('.settings #woocommerce-catalog-eml-tpl .woocommerce-catalog-eml-tpl-cell').click(function(e) {
    e.preventDefault();
    $(this).addClass('selected').parents().siblings().children('.woocommerce-catalog-eml-tpl-cell').removeClass('selected');
    var tpl_id = $(this).attr('data-tpl');
    $('#selected_email_tpl').val(tpl_id);
  });

  /********************** Vendor Catalog Enquiry button **********************/

  $('#enq_button #is_button').change(function() {
    if($(this).is(":checked")) {
      $('#mkUrBtn').show('slow');
    }
    else {
      $('#mkUrBtn').hide('slow');
    }
  });

});

// Open chat
function woocommerce_catalog_enquiry_open_chat(e) {
  jQuery.blockUI({ message: '<h1>'+mvx_catalog.wait_msg+'</h1>' });
  jQuery.ajax({
    type : 'post',
    url : mvx_catalog.ajaxurl,
    data : {
      action : 'infor_enquiry_chat_histry',
      data_product : e.getAttribute('data-option'),
      data_enquiry : e.id,
      data_cov : e.getAttribute('data-conv')
    },

  }).done(function( data ) {
      jQuery.unblockUI();
      //jQuery(".mesgs").html("<div class='chat-list chat-list-msg' style='border: 0'><div class='chat-people'><div class='chat-img'> "+data['user_image']+"</div><div class='chat-ib'><h5>"+data['user_name']+"</h5></div>"+data['product_permalink']+"</div></div><div class='msg-history'>"+data['conversation']+"</div><div class='type-msg'><div class='input-msg-write'><div class='attachment'><span><i class='fa fa-paperclip' aria-hidden='true'></i></span><span><i class='fa fa-smile-o' aria-hidden='true'></i></span></div><input type='text' id='write_msg' placeholder='Type a message' /><button type='button' id ='"+e.id+"' data-product1 = '"+e.getAttribute('data-option')+"' onclick=woocommerce_catalog_enquiry_send_reply(this)>SEND</button></div></div>  ");
      jQuery(".chat-list").removeClass("active-catalog");
      jQuery(".chat-list_"+e.id+"").addClass("active-catalog");
      jQuery(".mesgs").html(data['display_chat_histry']);
      jQuery(".hide_unread"+e.id+"").hide();
      jQuery('.msg-history').scrollTop(mvx_catalog.scroll_limit);
  });
}



/*
*** select first option
*/
if (mvx_catalog.first_enquiry_details) {
  
  jQuery.ajax({
    type : 'post',
    url : mvx_catalog.ajaxurl,
    data : {
      action : 'infor_enquiry_chat_histry',
      data_product : mvx_catalog.first_enquiry_details[1],
      data_enquiry : mvx_catalog.first_enquiry_details[0],
      data_cov : mvx_catalog.first_enquiry_details[2]
    },
    
    }).done(function( data ) {
      jQuery(".chat-list").removeClass("active-catalog");
      jQuery(".chat-list_"+mvx_catalog.first_enquiry_details[0]+"").addClass("active-catalog");
      jQuery(".mesgs").html(data['display_chat_histry']);
      jQuery(".hide_unread"+mvx_catalog.first_enquiry_details[0]+"").hide();
      jQuery('.msg-history').scrollTop(mvx_catalog.scroll_limit);
  });
}
// Reply Chat
function woocommerce_catalog_enquiry_send_reply(f) {
  var inputVal = document.getElementById("write_msg").value;  
  if(inputVal != ''){
    jQuery.blockUI({ message: '<h1>'+mvx_catalog.wait_msg+'</h1>' });
    jQuery.ajax({
      type : 'post',
      url : mvx_catalog.ajaxurl,
      data : {
        action : 'infor_enquiry_reply_from_vendor_action',
        inputVal : inputVal,
        data_product : f.getAttribute('data-product1'),
        data_enquiry : f.id
      },

    }).done(function( data ) {
        jQuery.unblockUI();
        jQuery(".msg-history").append( '<div class="outgoing-msg"><div class="sent-msg"><p>'+inputVal+'</p><span class="time-date"> 11:01 AM    |    June 9</span> </div></div>' );
        jQuery('.msg-history').scrollTop(mvx_catalog.scroll_limit);
        jQuery("#write_msg").val("");
    });

  } else {
    alert( mvx_catalog.empty_text );     
  }
}


jQuery("#first-choice").change(function() {
    var dropdown = jQuery(this);
    var key = dropdown.val();

    var vals;
    switch(key) {
      case 'product_name':
      vals = mvx_catalog.form_data_product;
      break;
      case 'customer_name':
      vals = mvx_catalog.form_data_customer;
      break;
      case 'enquiry_number':
      vals = mvx_catalog.enquiry_titles;
      break;
      case 'base':
      vals = ['Please choose from above'];
    }

    var secondChoice = jQuery("#second-choice");
    secondChoice.empty();
    jQuery.each(vals, function(index, value) {
      secondChoice.append("<option value="+index+" >" + value + "</option>");
    });

  });
  
  
function catalog_status_changes_dropdown_open( test_status ) {
  jQuery(".status-changes-area").slideToggle();
}

function catalog_status_changes_dropdown_close( test_status ) {
  jQuery(".status-changes-area").slideToggle();
}

function status_changes_from_dropdown( changes_data ) {
  var radioValue = jQuery("input[name='enquiry_status']:checked").val();
  jQuery.blockUI({ message: '<h1>'+mvx_catalog.wait_msg+'</h1>' });
  jQuery.ajax({
    type : 'post',
    url : mvx_catalog.ajaxurl,
    data : {
      action : 'enquiry_status_changed',
      enquiry_id : changes_data.getAttribute('data-enqid'),
      radio_value : radioValue
    },

  }).done(function( data ) {

    jQuery.unblockUI();
    jQuery(".enquiry-status-name").html(radioValue);
    
  });

}
