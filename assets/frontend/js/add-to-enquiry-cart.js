jQuery( function( $ ) { 
	
    $(document).on( 'click' ,'.wcce-add-enquiry-cart-button', function(e){
        e.preventDefault();
        var $this = $(this),
            $this_wrap = $this.parents('.wcce-enquiry-cart'),
            add_to_enquiry_info = '';
            quantity = $('.quantity .qty').val();
            if (quantity == null) {
                quantity = 1;
            }

        add_to_enquiry_info = 'action=woocommerce_catalog_add_to_enquiry_action&product_id='+$this.data('product_id')+'&quantity='+quantity+'&wp_nonce='+$this.data('wp_nonce');
        /* console.log(add_to_enquiry_info); */
        $.ajax({
            type   : 'POST',
            url    : enquiry_cart.ajaxurl,
            dataType: 'json',
            data   : add_to_enquiry_info,
            beforeSend: function(){
                $this.siblings( '.ajax-loading' ).css( 'visibility', 'visible' );
            },
            complete: function(){
                $this.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
                $( '.hide_enquiry_cart_widget_if_empty' ).closest( '.widget_woocommerce_catalog_enquiry_cart' ).show();
            },

            success: function (response) { //console.log(response);
                if( response.result == 'true' || response.result == 'exists'){
                    $this.parent().hide().removeClass('show').addClass('addedd');
                    if(response.show_message === true){
                        $this_wrap.append( '<div class="woocommerce_catalog_enquiry_add_item_response-'+ $this.data('product_id') +' woocommerce_catalog_enquiry_add_item_response_message">' + response.message + '</div>');
                    }
                    $this_wrap.append( '<div class="woocommerce_catalog_enquiry_add_item_view_cart_message_list-'+$this.data('product_id')+' woocommerce_catalog_enquiry_add_item_view_cart_message"><a class="added_to_cart wc-forward '+response.btn_style+'" href="'+response.enquiry_cart_url+'">' + response.label_view_cart + '</a></div>');

                }else if( response.result == 'false' ){
                    $this_wrap.append( '<div class="woocommerce_catalog_enquiry_add_item_response-'+$product_id_item.val()+'">' + response.message + '</div>');
                }

                /* update enquiry mini cart */
                $('.widget_woocommerce_catalog_enquiry_content').html('');
                $('.widget_woocommerce_catalog_enquiry_content').html(response.enquiry_mini_cart);
            }
        });
    });

    /*Remove an item from enquiry cart list*/
    $('.wcce-enquiry-cart-item-remove').on( 'click', function(e){

        e.preventDefault();

        var $this = $(this),
            key = $this.data('remove_item'),
            product_id = $this.data('product_id'),
            form = $('#wcce-enquiry-cart-form'),
            remove_info = '';

        remove_info = 'action=woocommerce_catalog_remove_from_enquiry_action&key='+key+'&wp_nonce='+$this.data('wp_nonce')+'&product_id='+product_id;

        $.ajax({
            type   : 'POST',
            url    : enquiry_cart.ajaxurl,
            dataType: 'json',
            data   : remove_info,
            beforeSend: function(){
                $this.addClass('ajaxloader');
            },
            complete: function(){
                $this.removeClass('ajaxloader');
            },

            success: function (response) {
                if( response.status === true){
                    $("#product-data-for-enquiry").val(JSON.stringify(response.cart_data));
                    $("[data-remove_item='"+key+"']").parents('.cart_item').remove();
                    if( $('.cart_item').length === 0 ){
                        $('#wcce-enquiry-cart-form').remove();
                        $('.wcce-cart-wrapper').html(enquiry_cart.no_more_product);
                    }
                }
            }
        });
    });

    /* remove an item from mini enquiry cart */
    $(document).on( 'click' ,'.mini_enquiry_cart_item .remove', function(e){
        e.preventDefault();
        var $this = $(this),
            key = $this.data('remove_item'),
            product_id = $this.data('product_id'),
            remove_info = '';
        remove_info = 'action=woocommerce_catalog_remove_from_enquiry_action&key='+key+'&product_id='+product_id;
        $.ajax({
            type   : 'POST',
            url    : enquiry_cart.ajaxurl,
            dataType: 'json',
            data   : remove_info,
            beforeSend: function(){
            },
            complete: function(){
            },
            success: function (response) {
                if ( response.status) {
                    $("[data-remove_item='"+key+"']").parent('.mini_enquiry_cart_item').remove();
                    if( $('.mini_enquiry_cart_item').length === 0 ){
                        $('ul.enquiry_cart_list').html('<li>'+enquiry_cart.no_more_product+'</li>');
                    }
                }
            }
        });
    });

    /* Enquiry Cart Hiding */

    if ( enquiry_cart.is_empty_enquiry ==1 ) { 
        $( '.hide_enquiry_cart_widget_if_empty' ).closest( '.widget_woocommerce_catalog_enquiry_cart' ).hide();
    } else { 
        $( '.hide_enquiry_cart_widget_if_empty' ).closest( '.widget_woocommerce_catalog_enquiry_cart' ).show();
    }

    /*Remove an item from enquiry cart list*/
    $('.woocommerce_catalog_enq_cart .enquiry-actions .enq_cart_update_btn').prop('disabled', true);
    $('.woocommerce_catalog_enq_cart .quantity .qty').on( 'change', function(e){
        $('.woocommerce_catalog_enq_cart .enquiry-actions .enq_cart_update_btn').prop('disabled', false);
    });
    
    /* Update Enquiry Cart */
    $( document ).on('submit', '#wcce-enquiry-cart-form', function(e){
        e.preventDefault();
        var $form = $( e.currentTarget );
        
        var update_info = $form.serialize()+'&action=woocommerce_catalog_update_enquiry_cart_action';
        $.ajax( {
            type:     'POST',
            url:      enquiry_cart.ajaxurl,
            data:     update_info,
            dataType: 'json',
            success:  function( response ) {
                if(response.status == 1){
                    $('#woocommerce-catalo-enquiry-msg').prepend('');
                    $('#woocommerce-catalo-enquiry-msg').prepend('<div class="woocommerce-message">'+response.msg+'</div>');	
                    location.reload();
                }else{
                    if(response.msg != ''){
                        $('#woocommerce-catalo-enquiry-msg').prepend('');
                        $('#woocommerce-catalo-enquiry-msg').prepend('<div class="woocommerce-error">'+response.msg+'</div>');
                    }
                }
            },
            complete: function() {
            }
        } );
    });
});