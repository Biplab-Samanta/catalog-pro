<?php
class Woocommerce_Catalog_Enquiry_Pro_Ajax {
    public $error_mail_report;
    public $send_mail;
    public function __construct() {
        // Admin action
        add_action( 'wp_ajax_woocommerce_catalog_enquiry_reply_admin_action', array(&$this, 'woocommerce_catalog_enquiry_reply_admin_action') );
        add_action( 'wp_ajax_woocommerce_catalog_enquiry_post_status_action', array(&$this, 'woocommerce_catalog_enquiry_post_status_action') );
        add_action( 'wp_ajax_wcce_enquiry_form_settings_data', array(&$this, 'wcce_enquiry_form_settings_data_action') );
        add_action( 'wp_ajax_woocommerce_catalog_json_get_products', array(&$this, 'woocommerce_catalog_json_get_products') );
        add_action( 'wp_ajax_woocommerce_catalog_json_get_users', array(&$this, 'woocommerce_catalog_json_get_users') );
        add_action( 'wp_ajax_woocommerce_catalog_json_get_product_cats', array(&$this, 'woocommerce_catalog_json_get_product_cats') );
        // Frontend action
        add_action( 'wp_ajax_send_enquiry_mail', array(&$this, 'send_product_enqury_mail') );
        add_action( 'wp_ajax_nopriv_send_enquiry_mail', array( &$this, 'send_product_enqury_mail' ) );

        add_action( 'wp_ajax_add_variation_for_enquiry_mail', array( $this, 'add_variation_for_enquiry_mail'));
        add_action( 'wp_ajax_nopriv_add_variation_for_enquiry_mail', array( $this, 'add_variation_for_enquiry_mail'));
        // Enquiry Cart action
        add_action( 'wp_ajax_woocommerce_catalog_add_to_enquiry_action', array(&$this, 'woocommerce_catalog_add_to_enquiry_action_callback') );
        add_action( 'wp_ajax_nopriv_woocommerce_catalog_add_to_enquiry_action', array( &$this, 'woocommerce_catalog_add_to_enquiry_action_callback' ) );
        add_action( 'wp_ajax_woocommerce_catalog_update_enquiry_cart_action', array(&$this, 'woocommerce_catalog_update_enquiry_cart_action_callback') );
        add_action( 'wp_ajax_nopriv_woocommerce_catalog_update_enquiry_cart_action', array( &$this, 'woocommerce_catalog_update_enquiry_cart_action_callback' ) );
        add_action( 'wp_ajax_woocommerce_catalog_remove_from_enquiry_action', array(&$this, 'woocommerce_catalog_remove_from_enquiry_action_callback') );
        add_action( 'wp_ajax_nopriv_woocommerce_catalog_remove_from_enquiry_action', array( &$this, 'woocommerce_catalog_remove_from_enquiry_action_callback' ) );
        add_action( 'wp_ajax_woocommerce_catalog_vendor_enquiry_details_list', array(&$this, 'woocommerce_catalog_vendor_enquiry_details_list_callback') );
        add_action('wp_mail_failed', array( $this, 'woocommerce_catalog_catalog_enquiry_error_mail_report'));
        // responce for chat massage
        add_action( 'wp_ajax_infor_enquiry_reply_from_vendor_action', array( $this, 'infor_enquiry_reply_from_vendor_action' ) );
        add_action( 'wp_ajax_infor_enquiry_chat_histry', array( $this, 'infor_enquiry_chat_histry' ) );
        add_action( 'wp_ajax_enquiry_status_changed', array( $this, 'enquiry_status_changed' ) );
    }

    public function infor_enquiry_chat_histry() {
        global $MVX ,$wpdb;
        $data_product = isset( $_POST['data_product'] ) ? absint( $_POST['data_product'] ) : 0;
        $data_enquiry = isset( $_POST['data_enquiry'] ) ? absint( $_POST['data_enquiry'] ) : 0;
        $to_user_id_old = isset( $_POST['data_cov'] ) ? absint( $_POST['data_cov'] ) : 0;
        // Enquiry post details
        $enquiry_post_title = get_the_title($data_enquiry);
        $enquiry_post_date = get_the_date('M j, Y' ,$data_enquiry);
        // Enquiry product details
        $enquiry_product_id = get_post_meta( $data_enquiry, '_enquiry_product', true );
        $enquiry_product_title = get_the_title( $enquiry_product_id );
        $enquiry_product_permalink = get_permalink( $enquiry_product_id );
        // Complete button
        $status_changes_dropdown = '';
        $user = get_userdata( get_current_user_id() );
        $user_roles = $user->roles;
        $customer_status = '';

        $catalog_status = array(  
            'read' => __('Read', 'woocommerce-catalog-enquiry-pro'),
            'unread' => __('unread', 'woocommerce-catalog-enquiry-pro'),
            'completed' => __('Closed', 'woocommerce-catalog-enquiry-pro'),
            'delete' => __('Delete', 'woocommerce-catalog-enquiry-pro'),
            );

        if ( in_array( 'dc_vendor', $user_roles, true ) || in_array( 'administrator', $user_roles, true ) ){
            $status_changes_dropdown = '
            <span class="dashicons dashicons-arrow-down-alt2"></span>
            ';          
            $customer_status = 'onclick = catalog_status_changes_dropdown_open(this)>';      
        }

        if ( in_array( 'dc_vendor', $user_roles, true ) ){
            unset($catalog_status['delete']);
        }

        $catalog_all_status = '';
        foreach ($catalog_status as $name => $label) { 
            $catalog_all_status .=  '<h5><label><input type="radio" name="enquiry_status" id="enquiry_status' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '" ' . checked( get_post_status( $data_enquiry ), $name, false ) . '  /><span for="enquiry_status' . esc_attr( $name ) . '" class="selectit">' . esc_html( $label ) . '</span></label></h5>';
        }

        $product_link = '<div class="enquiry-details-chat">    
        <p class="productDetailCls">'.__( 'Enquiry : ', 'woocommerce-catalog-enquiry-pro' ).'
            <span>'.$enquiry_post_title.'</span></p>
            <p class="productDetailCls">'.__( 'Date : ', 'woocommerce-catalog-enquiry-pro' ).'
                <span>'.$enquiry_post_date.'</span></p>

            <div>
        </div>';
        
        if (is_active_MVX()){
            $vendor_details = get_mvx_product_vendors( $data_product ) ? get_mvx_product_vendors( $data_product ) : false;
        } else {
            $vendor_details = false;
        }        
        if ( $vendor_details ){
            $to_user_id = $vendor_details->id;
            $users_name = $vendor_details->user_data->data->user_login;
            $user_image =  get_avatar( $to_user_id , 60 );
        } else {
            $admin_email  = get_option('admin_email');
            $User_details = get_user_by('email', $admin_email);
            $to_user_id   = $User_details->data->ID;
            $user_details = get_user_by("ID", $to_user_id);
            $users_name   = $user_details->data->user_login;
            $user_image   = get_avatar( $to_user_id , 60 );
        }

        $current_id =  get_current_user_id();
        $user = get_userdata( $current_id );
        $user_roles = $user->roles;

        if ( in_array( 'dc_vendor', $user_roles, true ) || in_array( 'administrator', $user_roles, true ) ){

            if ( $vendor_details ){
                $conversation = get_customer_vendor_conversation_details( $vendor_details->id , $to_user_id_old, $data_product );
            } else {
                $conversation = get_customer_vendor_conversation_details( get_current_user_id() , $to_user_id_old, $data_product );
            }

            $user_name_non_log_in = get_post_meta( $data_enquiry, '_enquiry_username', true );
            $user_email_non_log_in = get_post_meta( $data_enquiry, '_enquiry_useremail', true );
            $user_details = get_user_by( 'ID' , get_post_field ('post_author', $data_enquiry) );

            $users_name = $user_details ? $user_details->data->user_login : $user_name_non_log_in;
            $users_email = $user_details ? $user_details->user_email : $user_email_non_log_in;
            $user_name_and_email = apply_filters('catalog_chat_display_email_with_name', true) ? $users_name . '( ' . $users_email . ' ) ' : $users_name;
            $user_image = get_avatar( $user_details->data->ID , 60 );
        } else {
            $conversation = get_customer_vendor_conversation_details( get_current_user_id() , $to_user_id_old, $data_product );
        }

        $histrory_unread = get_customer_vendor_admin_conversation_details( get_current_user_id() , $to_user_id_old, $data_product );
        foreach ($histrory_unread as $key => $value_unread) {
           if ( $value_unread->to_user_id == get_current_user_id() ){
               
               $wpdb->query("UPDATE `{$wpdb->prefix}catelog_cust_vendor_answers` SET status = ' read' WHERE enquiry_id =" . $value_unread->enquiry_id . " AND chat_message_id = " . $value_unread->chat_message_id ); 
           }
        }

        $quantity_number = get_post_meta( $data_enquiry, '_enquiry_product_quantity', true );
        $quantity = __( 'Quantity : ', 'woocommerce-catalog-enquiry-pro' ) . $quantity_number;

        $send_button = apply_filters( 'woocommer_catalog_send_button_test_frontend', __( 'SEND', 'woocommerce-catalog-enquiry-pro' ) );
        $type_massage = apply_filters( 'woocommer_catalog_type_text_frontend', __( 'Type a message..', 'woocommerce-catalog-enquiry-pro' ) );
        $enquiry_current_status = get_post_status( $data_enquiry ) && get_post_status( $data_enquiry ) == 'publish' ? __('Open', 'woocommerce-catalog-enquiry-pro') : get_post_status( $data_enquiry );
        $display_chat_histry = '<div class="chat-list chat-list-msg" style="border: 0">
            <div class="chat-people">
            <div class="customerTitleArea">
            <div>
               <div class="statusDrpDown">
                <label>
                    '. __( 'Status : ', 'woocommerce-catalog-enquiry-pro' ). '
                </label>
            <div class="cat-visiblity" '.$customer_status.'
            <span class= "enquiry-status-name">
                '. $enquiry_current_status .'
            </span>
            '.$status_changes_dropdown.' 

            </div></div>
        <div class="status-changes-area">
            '.$catalog_all_status.'

            <div class="statusButnArea">
                <button type="button" onclick = status_changes_from_dropdown(this) data-enqid = "'.$data_enquiry.'" class="btn">'. __("Ok", "woocommerce-catalog-enquiry-pro").'</button>
                <button type="button" onclick = catalog_status_changes_dropdown_close(this)>'. __("Cancel", "woocommerce-catalog-enquiry-pro").'</button>
            </div>
            </div>
            </div>
                <div class="imgNameCls">
                    <div class="chat-img">'.$user_image.'</div>
                
                    <div class="chat-ib">'.$user_name_and_email.'</div>
                </div>
                
                </div>
                <div class="productEnquiryArea"> 
                <p class="productLink">Product:<a href="'.$enquiry_product_permalink.'" target="_blank">'.$enquiry_product_title.'</a><span class="woo-catalog-quantity-class">&nbsp ( '.$quantity.' ) </span></p>
               
                '.$product_link.' </div>
            
            </div>
        </div>
        </div>
        <div class="msg-history">'.$conversation.'</div>
        <div class="type-msg">
            <div class="input-msg-write">
                <textarea id="write_msg" name="write_msg" rows="2" cols="60" placeholder="'.$type_massage.'"></textarea>
                <button type="button" id = "'.$data_enquiry.'" data-product1 = "'.$data_product.'" onclick = woocommerce_catalog_enquiry_send_reply(this)>'.$send_button.'</button>
            </div>
        </div>';
            
        $data_details =  array( 'display_chat_histry' => $display_chat_histry );
        wp_send_json( $data_details );
    }

    public function infor_enquiry_reply_from_vendor_action() {
        global $wpdb;
        $vendor_id =  get_current_user_id();
        $chat_message = isset($_POST['inputVal']) ? wc_clean($_POST['inputVal']) : '';
        $data_product = isset($_POST['data_product']) ? absint($_POST['data_product']) : 0;
        $data_enquiry = isset($_POST['data_enquiry']) ? absint($_POST['data_enquiry']) : 0;
        if (is_active_MVX()){
            $vendor_details = get_mvx_product_vendors( $data_product ) ? get_mvx_product_vendors( $data_product ) : false;
        } else {
            $vendor_details = false;
        }  
        $user = get_userdata( $vendor_id );
        $user_roles = $user->roles;
        if ( in_array( 'dc_vendor', $user_roles, true ) || in_array( 'administrator', $user_roles, true ) ){
            $enquiry_user_email = get_post_meta( $data_enquiry , '_enquiry_useremail', true );
            //$user_details = get_user_by( 'email' , $enquiry_user_email );
            $user_details_id = get_user_by( 'ID' , get_post_field ('post_author', $data_enquiry) );
            $to_user_id = $user_details_id ? $user_details_id->data->ID : 0;
        } else {
            if ( $vendor_details ){
                $to_user_id = $vendor_details->id;
            } else {
                $admin_email = get_option('admin_email');
                $User_details = get_user_by('email', $admin_email);
                $to_user_id = $User_details->data->ID;
            }
        }

        if ( in_array( 'administrator', $user_roles, true ) && $vendor_details ){
            $vendor_id =  $vendor_details->id;
        }

        $wpdb->query("insert into {$wpdb->prefix}catelog_cust_vendor_answers set to_user_id='{$to_user_id}', from_user_id = '{$vendor_id}',chat_message='{$chat_message}',product_id='{$data_product}',enquiry_id='{$data_enquiry}',status='unread' ");
        
        // send email code
        if ( in_array( 'dc_vendor', $user_roles, true ) || in_array( 'administrator', $user_roles, true ) ){
            if ( apply_filters( 'wc_catalog_enquiry_send_mail_customer', true ) ){
                do_action( 'wc_catalog_enquiry_send_mail', $data_enquiry, $vendor_id, $chat_message );
            } 
        }
    }

    public function enquiry_status_changed() {
       global $wpdb;
        $enquiry_id = isset( $_POST['enquiry_id'] ) ? absint($_POST['enquiry_id']): 0 ;
        $radio_value = isset( $_POST['radio_value'] ) ? wc_clean( $_POST['radio_value'] ) : '';
        if ($radio_value == 'completed') {
            wp_update_post( array(
                'ID'          => $enquiry_id,
                'post_status' => sanitize_text_field('completed'),
            ) );
        } elseif ($radio_value == 'delete') {
            wp_delete_post($enquiry_id);
        } elseif ($radio_value == 'unread') {
            wp_update_post( array(
                'ID'          => $enquiry_id,
                'post_status' => sanitize_text_field('unread'),
            ) );
        } elseif ($radio_value == 'read') {
            wp_update_post( array(
                'ID'          => $enquiry_id,
                'post_status' => sanitize_text_field('read'),
            ) );
        }
    }

    // Admin action
    public function woocommerce_catalog_json_get_products(){
    	$return = array();
    	$search_results = new WP_Query( array( 
    		's'=> isset($_GET['q']) ? $_GET['q'] : '', 
                    'post_type' => 'product',
    		'post_status' => 'publish', 
    		'ignore_sticky_posts' => 1,
    		'posts_per_page' => 20 
    	) );
    	if ( $search_results->have_posts() ) :
                while( $search_results->have_posts() ) : $search_results->the_post();	
                    // shorten the title a little
                    $title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
                    $return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title )
                endwhile;
    	endif;
            wp_send_json($return);
    	die;
    }
    
    public function woocommerce_catalog_json_get_users(){
    	$return = array();
            $keyword = isset($_GET['q']) ? $_GET['q'] : '';
            $search = '*' .$keyword.'*';
    	$users = get_users( array( 'search' => $search, 'search_columns' => array('display_name'), 'fields' => array( 'ID', 'display_name' ) ) );
            if ( $users ) :
                foreach ($users as $user) {
                    $return[] = array( $user->ID, $user->display_name );       
                }
    	endif;
            wp_send_json($return);
    	die;
    }
    
    public function woocommerce_catalog_json_get_product_cats(){
    	$return = array();
            $keyword = isset($_GET['q']) ? $_GET['q'] : '';
    	$args = array('search'=> $keyword, 'orderby' => 'name', 'order' => 'ASC' );
      	$terms = get_terms( 'product_cat', $args );
    	if ( $terms ) :
                foreach ($terms as $term) {
                    $return[] = array( $term->term_id, $term->name );       
                }
    	endif;
            wp_send_json($return);
    	die;
    }
    
    public function woocommerce_catalog_enquiry_post_status_action() {
        $user = wp_get_current_user();
        $enq_id = (int)$_POST['enq_id'];
        $enq_type = get_post_meta( $enq_id, "_enquiry_action_type", true );
        $flag = 0;
        if (!empty($enq_id)){
            if (is_active_MVX()){
                if (in_array('administrator', $user->roles)){ 	
                    if (!empty(sanitize_text_field($_POST['enq_status'])) && sanitize_text_field($_POST['enq_status']) != 'delete'){
                        wp_update_post( array(
                            'ID'          => $enq_id,
                            'post_status' => sanitize_text_field($_POST['enq_status']),
                        ) );
                        $flag = 1;
                    } else {
                        wp_delete_post($enq_id);
                        $flag = 2;
                    }
                }elseif (is_user_mvx_vendor($user->ID)){
                    if (!empty(sanitize_text_field($_POST['enq_status']))){
                        if (sanitize_text_field($_POST['enq_status']) != 'delete'){
                            if ($enq_type == 'single'){
                                wp_update_post( array('ID' => $enq_id, 'post_status' => sanitize_text_field($_POST['enq_status'])) );
                            } else {
                                update_post_meta( $enq_id, "_enquiry_status_vendor_$user->ID", $_POST['enq_status'] );
                            }
                            $flag = 1;
                        } else {
                            if ($enq_type == 'single'){
                                wp_update_post( array('ID' => $enq_id, 'post_status' => sanitize_text_field($_POST['enq_status'])) );
                            } else {
                                update_post_meta( $enq_id, "_enquiry_status_vendor_$user->ID", $_POST['enq_status'] );
                            }
                            $flag = 2;
                        }
                    }
                }
            } else {
                if (!empty(sanitize_text_field($_POST['enq_status'])) && sanitize_text_field($_POST['enq_status']) != 'delete'){
                    wp_update_post( array(
                        'ID'          => $enq_id,
                        'post_status' => sanitize_text_field($_POST['enq_status']),
                    ) );
                    $flag = 1;
                } else {
                    wp_delete_post($enq_id);
                    $flag = 2;
                }
            }

        } else {
            $flag = 0;
        }
        if (!is_admin()){
            if ($flag == 1){
                wc_add_notice(__('Enquiry status changed successfully', 'woocommerce-catalog-enquiry-pro'), 'success');
            }elseif ($flag == 2){
                wc_add_notice(__('Enquiry deleted successfully', 'woocommerce-catalog-enquiry-pro'), 'success');
            } else {
                wc_add_notice(__('Somethings wrong!', 'woocommerce-catalog-enquiry-pro'), 'error');
            }
        }
        echo $flag;
        die;
    }

    public function wcce_enquiry_form_settings_data_action(){
        $form_data = json_decode(stripslashes_deep($_REQUEST['form_data']),true);
        if (!empty($form_data) && is_array($form_data)){
            foreach ($form_data as $key => $value){
                $form_data[$key]['hidden'] = true;
            }
        }

        update_option('wcce_enquiry_form_data', $form_data);
        die;
    }
    
    public function woocommerce_catalog_catalog_enquiry_error_mail_report($wp_error){
        if ( true === WP_DEBUG ) {
            error_log(print_r($wp_error, true));
        }
        if (is_object( $wp_error ) ) {
            if (isset($wp_error->errors['wp_mail_failed']) || isset($wp_error->error_data['wp_mail_failed'])){
                if (isset($wp_error->error_data['wp_mail_failed']['phpmailer_exception_code'])){
                    $this->error_mail_report = 'Mailer Error: '.$wp_error->error_data['wp_mail_failed']['phpmailer_exception_code'];
                }
                if (isset($wp_error->errors['wp_mail_failed'][0])){
                    $this->error_mail_report .= ', '.$wp_error->errors['wp_mail_failed'][0];
                }
            }
        }
    }

    public function woocommerce_catalog_enquiry_reply_admin_action(){
        if (isset($_POST['enq_id']) && !empty((int)$_POST['enq_id'])){
            $enquiry = get_post((int)$_POST['enq_id']);
            $user_name = get_post_meta( $enquiry->ID , '_enquiry_username', true );
            $user_mail = get_post_meta( $enquiry->ID , '_enquiry_useremail', true );
            $enquiry_action_type = get_post_meta( $enquiry->ID , '_enquiry_action_type', true );
            $body = sanitize_textarea_field($_POST['message']);
            $vendor_name = __('Admin', 'woocommerce-catalog-enquiry-pro');
            $user_id = get_current_user_id();
            if ($enquiry_action_type == 'multiple'){
                $subject_mail = __('Multiple Enquiry Response by |ADMIN|','woocommerce-catalog-enquiry-pro');
                if (is_active_MVX()){
                        $vendor = get_mvx_vendor($user_id);
                        if ($vendor){
                            $subject_mail = str_replace( '|ADMIN|', $vendor->page_title, $subject_mail );
                            $vendor_name = $vendor->page_title;
                        }
                        else{
                            $subject_mail = str_replace( '|ADMIN|', 'Admin', $subject_mail );
                            $vendor_name = 'Admin';
                        }
                } else {
                    $vendor_data = get_userdata($user_id);
                    $subject_mail = str_replace( '|ADMIN|', $vendor_data->display_name, $subject_mail );
                    $vendor_name = $vendor_data->display_name;
                }
                $product_data = get_post_meta( $enquiry->ID , '_enquiry_product', true );
                $product_names = array();
                $product_urls = array();
                if (is_active_MVX()){
                    foreach ($product_data as $key => $pro) {
                        if (is_object($pro))
                            $product = wc_get_product( $pro->product_id );
                        else
                            $product = wc_get_product( $pro['product_id'] );
                        if ($product && is_mvx_vendor_product($product->get_id())){
                            $product_names[] = $product->get_title();
                            $product_urls[] = $product->get_permalink();
                        }
                    }
                } else {

                }

                $body = str_replace( '|USER_NAME|', $user_name, $body );
                $body = str_replace( '|USER_EMAIL|', $user_mail, $body );
                $body = str_replace( '|PRODUCT_NAME|', implode(", ",$product_names), $body );
                $body = str_replace( '|PRODUCT_URL|', implode(", ",$product_urls), $body );

            } else {
                if (is_active_MVX()){
                    $vendor = get_mvx_vendor($user_id);
                    if ($vendor)
                        $vendor_name = $vendor->page_title;
                    else
                        $vendor_name = 'Admin';
                } else {
                    $vendor_data = get_userdata($user_id);
                    $vendor_name = $vendor_data->display_name;
                }
                $product_id = get_post_meta( $enquiry->ID , '_enquiry_product', true );
                $product = wc_get_product( $product_id );
                
                $body = str_replace( '|USER_NAME|', $user_name, $body );
                $body = str_replace( '|USER_EMAIL|', $user_mail, $body );
                if ($product){
                    $subject_mail = __('Enquiry Response for ','woocommerce-catalog-enquiry-pro'). $product->get_formatted_name();
                    $body = str_replace( '|PRODUCT_NAME|', $product->get_title(), $body );
                    $body = str_replace( '|PRODUCT_URL|', $product->get_permalink(), $body );
                } else {
                    $subject_mail = __('Enquiry Response','woocommerce-catalog-enquiry-pro');
                    $body = str_replace( '|PRODUCT_NAME|', '', $body );
                    $body = str_replace( '|PRODUCT_URL|', '', $body );
                }
            }

            $body = stripslashes($body);

            $enquiry_data = apply_filters( 'woocommerce_catalog_before_enquiry_reply_email_data', array(
                'enquiry_id' => $enquiry->ID,
                'user_name' => $user_name,
                'vendor_name' => $vendor_name,
                'user_email' => $user_mail,
                'subject_mail' => sanitize_text_field($subject_mail),
                'enquiry_action_type' => $enquiry_action_type,
                'body_mail' => $body,
                ));
            //print_r($enquiry_data);die;
            $reply_email = WC()->mailer()->emails['Woocommerce_Catalog_Enquiry_Pro_Reply_Email'];
            if ($reply_email->trigger( $enquiry_data )) {
                $admin_reply = array(
                    'reply_time' =>date("Y-m-d h:i:sa"),
                    'reply_subject' =>sanitize_text_field($subject_mail),
                    'reply_body' =>$body,
                    'reply_by' =>$user_id,
                );
                $admin_replies = array();
                $previous_replies = get_post_meta( $enquiry->ID, '_enquiry_replies', true );
                if ( empty($previous_replies) ) {
                    $admin_replies[] = $admin_reply;
                    update_post_meta( $enquiry->ID, '_enquiry_replies', $admin_replies );
                } else {
                    $previous_replies[] = $admin_reply;
                    update_post_meta( $enquiry->ID, '_enquiry_replies', $previous_replies );
                }
                wc_add_notice(__('Enquiry reply sent successfully', 'woocommerce-catalog-enquiry-pro'), 'success');
                echo 1;
            } else {
                wc_add_notice(__('Somethings wrong with your mail!', 'woocommerce-catalog-enquiry-pro'), 'error');
                echo 0;
            }
        }
        die;
    }

    public function add_variation_for_enquiry_mail() {
        $product_id = (int)$_POST['product_id'];
        if ($product_id){
            if (isset($_SESSION['variation_list']))
                unset($_SESSION['variation_list']);

            $variation_data = $_POST['variation_data'];
            $_SESSION['variation_list'] = $variation_data;
        }	
        die;
    }

    public function send_product_enqury_mail() {
        global $Woocommerce_Catalog_Enquiry_Pro, $woocommerce, $product, $Woocommerce_Catalog_Enquiry_Pro_Cart, $Woocommerce_Catalog_Enquiry;
        $flag = 0;

        $settings = get_woocommerce_catalog_catalog_settings();
        $upload_dir = wp_upload_dir();
        $catalog_enquiry = $upload_dir['basedir'].'/catalog_enquiry';
        if ( ! file_exists( $catalog_enquiry ) ) {
            wp_mkdir_p( $catalog_enquiry );
        }
        $uploaded_file_link = array();
        $attachments = array();
        $product_data = '';
        $product_data = isset($_POST['product_data_for_enquiry']) ? $_POST['product_data_for_enquiry'] : '';
        if (is_object(json_decode(stripslashes($product_data)))){
            $product_data = json_decode(stripslashes($product_data));
        }
        $enquiry_action_type = isset($_POST['product_enquiry_action']) ? $_POST['product_enquiry_action'] : '';
        $user_name = isset($_POST['enq_user_name']) ? $_POST['enq_user_name'] : '';
        $user_email = isset($_POST['enq_user_email']) ? $_POST['enq_user_email'] : '';
        $woocommerce_catalog_enquiry_fields = isset($_POST['woocommerce_catalog_enquiry_fields']) ? $_POST['woocommerce_catalog_enquiry_fields'] : '';
        $quantity = 1;
        if ($enquiry_action_type == 'single' && isset($_POST['quantity']) && $_POST['quantity'] != 'undefined')
            $quantity = (int)$_POST['quantity'];

        // if form has google recaptcha
        if (isset($_POST['woocommerce_catalog_enquiry_grecaptcha_secret']) && isset($_POST['g-recaptcha-response'])) {
            $recaptcha = '';
            $data;
            header('Content-Type: application/json');
            error_reporting(E_ALL ^ E_NOTICE);
            if (isset($_POST['g-recaptcha-response'])) {
                $recaptcha = $_POST['g-recaptcha-response'];
            }
            if (!$recaptcha){
                $flag='norecaptcha';
                wp_send_json(array('value' => $flag));
                die;
            }
            // calling google recaptcha api.
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$_POST['woocommerce_catalog_enquiry_grecaptcha_secret']."&response=".$recaptcha."&remoteip=".$_SERVER['REMOTE_ADDR']);

            // validating result.
            if ($response.success==false) {
                $flag='spam';
                wp_send_json(array('value' => $flag));
                die;
            }
        }

        if (isset($_FILES['woocommerce_catalog_enquiry_fields'])) {
            $attacment_files = $_FILES['woocommerce_catalog_enquiry_fields'];
            $files = array();
            $count = 0;
            if (!empty($attacment_files) && is_array($attacment_files)) {
                foreach ($attacment_files['name'] as $key => $attacment) {
                    foreach ($attacment as $key_attacment => $value_attacment) {
                        $files[$count]['name'] = $value_attacment;
                        $files[$count]['type'] = $attacment_files['type'][$key][$key_attacment];
                        $files[$count]['tmp_name'] = $attacment_files['tmp_name'][$key][$key_attacment];
                        $files[$count]['error'] = $attacment_files['error'][$key][$key_attacment];
                        $files[$count]['size'] = $attacment_files['size'][$key][$key_attacment];
                        $files[$count]['field_key'] = $key;
                        $count++;
                    }
                }
            }
            foreach ($files as $file) {
                $file_name = mt_rand().'.'.explode(".",basename($file['name']))[1];
                $target_file = $catalog_enquiry.'/'.$file_name;
                if (move_uploaded_file($file['tmp_name'], $target_file)){
                    $attachments[] = $target_file; 
                    $uploaded_file_link[] = $upload_dir['baseurl'].'/catalog_enquiry/'.$file_name;
                }
            }
        }

        // MVX
        $is_vendor_product = $send_vendor_email = false;
        $multi_vendor_enquiry_products = array();
        $this->send_mail = false;
        $other_info_product = "";
        $product_variations = array();
        if ($enquiry_action_type == 'single')
            $product_variations = isset($_SESSION['variation_list']) ? $_SESSION['variation_list'] : array();
            
        /********************Send Enquiry Email********************/
        $send_email = WC()->mailer()->emails['Woocommerce_Catalog_Enquiry_Pro_Send_Email'];
        $enquiry_data = apply_filters( 'woocommerce_catalog_before_enquiry_send_email_data', array(
                'user_name' => sanitize_text_field($user_name),
                'user_email' => sanitize_email($user_email),
                'user_enquiry_fields' => $woocommerce_catalog_enquiry_fields,
                'user_attachments_link' => !empty($uploaded_file_link) ? esc_url($uploaded_file_link) : '',
                'user_attachment' => $attachments,
                'product_data' => $product_data,
                'product_quantity' => $quantity,
                'product_variations' => $product_variations,
                'product_otherInfo' => $other_info_product,
                'enquiry_action_type' => sanitize_text_field($enquiry_action_type),
                ));
        /***********Admin & Customer Enquery Copy************/
        if (isset($settings['is_other_admin_mail']) && mvx_catalog_get_settings_value($settings['is_other_admin_mail'], 'checkbox') == 'Enable') {
            $email_admin = '';
        } else {
            $email_admin = get_option( 'admin_email' );
        }
        if (isset($settings['other_emails'])) {
            $email_admin .= ','.$settings['other_emails'];				
        }
        if (isset($settings['is_customer_receive_mail']) && mvx_catalog_get_settings_value($settings['is_customer_receive_mail'], 'checkbox') == 'Enable') {
            $email_admin .= ','.$user_email;			
        }
        
        $this->send_mail = $send_email->trigger( $email_admin, $product_data, $enquiry_data );
        /***********Multiple Enquery************/
        /***********Multiple Enquery for MVX************/
        if ($enquiry_action_type == 'multiple'){			
            if (is_active_MVX()){ 						
                if (is_object($product_data)){ 
                    foreach($product_data as $key =>$pro){ 
                        $post_author = array();
                        $product = wc_get_product( $pro->product_id );
                        if ($product && $product->get_type() == 'variation'){
                            $parent_id = $product->get_parent_id();
                            $_product = wc_get_product( $parent_id );
                            $post_author = get_mvx_product_vendors($_product->get_id());
                        }elseif ($product){
                            $post_author = get_mvx_product_vendors($product->get_id());
                        }
                        if ($post_author){
                            $is_vendor_product = is_user_mvx_vendor($post_author->id);
                            if ($is_vendor_product && array_key_exists($post_author->id, $multi_vendor_enquiry_products))
                                $multi_vendor_enquiry_products[$post_author->id][$key] = $pro;
                            else
                                $multi_vendor_enquiry_products[$post_author->id][$key] = $pro;
                        }
                    }
                }
            }

            /***********Vendor Enquery Copy************/
            if (!empty($multi_vendor_enquiry_products)){
                foreach ($multi_vendor_enquiry_products as $author_id => $products) {
                    $email_admin = '';
                    $user_info = get_userdata($author_id);
                    $vendor_email = $user_info->user_email;
                    if (is_active_MVX()){
                        if (is_user_mvx_vendor($author_id)){
                            $settings = get_mvx_catalog_settings($author_id);
                            if (isset($settings['is_other_admin_mail']) && mvx_catalog_get_settings_value($settings['is_other_admin_mail'], 'checkbox') == 'Enable') {
                                if (isset($settings['other_emails'])) {
                                    $email_admin = $settings['other_emails'];				
                                } else {
                                    $email_admin = $vendor_email;
                                }
                            }else {
                                $email_admin = $vendor_email;
                            }
                        
                            $admin_general_settings = (array) get_option( 'woocommerce_catalog_enquiry_general_settings' );
                            if (!empty(!$admin_general_settings) && isset($admin_general_settings['admin_receive_vendor_enquiry']) && $admin_general_settings['admin_receive_vendor_enquiry'] == 'Enable') {
                                if (isset($settings['is_other_admin_mail']) && mvx_catalog_get_settings_value($settings['is_other_admin_mail'], 'checkbox') == 'Enable') {
                                    if (isset($settings['other_admin_mail'])) {
                                        $email_admin .= ','.$settings['other_admin_mail'];
                                    }
                                    else {
                                        $email_admin .= ','.get_option( 'admin_email' );
                                    }
                                } else {
                                    $email_admin .= ','.get_option( 'admin_email' );
                                }
                                if (isset($settings['other_emails'])) {
                                    $email_admin .= ','.$settings['other_emails'];				
                                }
                            }

                            $vendor_details = get_mvx_vendor($author_id);
                            if ($vendor_details && strpos($email_admin, $vendor_details->user_data->data->user_email) !== false) {
                                $send_vendor_email = true;
                            }

                            $product_data = (object)$products;

                            $enquiry_data = apply_filters( 'woocommerce_catalog_before_enquiry_send_vendor_email_data', array(
                                    'user_name' => sanitize_text_field($user_name),
                                    'user_email' => sanitize_email($user_email),
                                    'user_enquiry_fields' => $woocommerce_catalog_enquiry_fields,
                                    'user_attachments_link' => !empty($uploaded_file_link) ? esc_url($uploaded_file_link) : '',
                                    'user_attachment' => $attachments,
                                    'product_data' => $product_data,
                                    'product_author' => $author_id,
                                    'product_variations' => $product_variations,
                                    'product_otherInfo' => $other_info_product,
                                    'enquiry_action_type' => sanitize_text_field($enquiry_action_type),
                                    'send_vendor_email' =>  $send_vendor_email
                                    ),$author_id);

                            $this->send_mail = $send_email->trigger( $email_admin, $product_data, $enquiry_data );
                        }
                    }
                }
            }
        } else {		
            /***********Single Enquery************/
            /***********Single Enquery for MVX************/
            $product = wc_get_product( $product_data );
            $author_id = '';
            if (is_active_MVX()){
                if ($product && $product->get_type() == 'variation'){
                    $parent_id = $product->get_parent_id();
                    $_product = wc_get_product( $parent_id );
                    $post_author = get_mvx_product_vendors($_product->get_id());
                    if ($post_author){
                        $author_id = $post_author->id;
                    } else {
                        $author_id = get_post($_product->get_id())->post_author;
                    }
                } else {
                    $post_author = get_mvx_product_vendors($product->get_id());
                    if ($post_author){
                        $author_id = $post_author->id;
                    } else {
                        $author_id = get_post($product->get_id())->post_author;
                    }
                }
                
                $user_info = isset($author_id) ? get_userdata($author_id) : array();
                $vendor_email = isset($user_info) ? $user_info->user_email : '';
                
                if (is_user_mvx_vendor($author_id)){
                    $settings = get_mvx_catalog_settings($author_id);
                    if (isset($settings['is_other_admin_mail']) && mvx_catalog_get_settings_value($settings['is_other_admin_mail'], 'checkbox') == 'Enable') {
                        if (isset($settings['other_emails'])) {
                            $email_admin = $settings['other_emails'];				
                        } else {
                            $email_admin = $vendor_email;
                        }
                    } else {
                        $email_admin = $vendor_email;
                    }
                    
                    $admin_general_settings = (array) get_option( 'woocommerce_catalog_enquiry_general_settings' );
                    if (!empty($admin_general_settings) && isset($settings['admin_receive_vendor_enquiry']) && $settings['admin_receive_vendor_enquiry'] == 'Enable') {
                        if (isset($settings['is_other_admin_mail']) && mvx_catalog_get_settings_value($settings['is_other_admin_mail'], 'checkbox') == 'Enable') {
                            if (isset($settings['other_admin_mail'])) {
                                $email_admin .= ','.$settings['other_admin_mail'];
                            } else {
                                $email_admin .= ','.get_option( 'admin_email' );
                            }
                        } else {
                            $email_admin .= ','.get_option( 'admin_email' );
                        }
                        if (isset($settings['other_emails'])) {
                            $email_admin .= ','.$settings['other_emails'];				
                        }
                    }


                    $vendor_details = get_mvx_vendor($author_id);
                    if ($vendor_details && strpos($email_admin, $vendor_details->user_data->data->user_email) !== false) {
                        $send_vendor_email = true;
                    }

                    $enquiry_data = apply_filters( 'woocommerce_catalog_before_enquiry_send_vendor_email_data', array(
                        'user_name' => sanitize_text_field($user_name),
                        'user_email' => sanitize_email($user_email),
                        'user_enquiry_fields' => $woocommerce_catalog_enquiry_fields,
                        'user_attachments_link' => !empty($uploaded_file_link) ? esc_url($uploaded_file_link) : '',
                        'user_attachment' => !empty($attachments) ? $attachments : '',
                        'product_data' => $product_data,
                        'product_author' => $author_id,
                        'product_quantity' => $quantity,
                        'product_variations' => $product_variations,
                        'product_otherInfo' => $other_info_product,
                        'enquiry_action_type' => sanitize_text_field($enquiry_action_type),
                        'send_vendor_email' =>  $send_vendor_email
                        ),$author_id);

                    $this->send_mail = $send_email->trigger( $email_admin, $product_data, $enquiry_data );
                    
                }
            } 	
        }

        //if ($this->send_mail) {
            $flag = 1;
            if (isset($_SESSION['variation_list']))
                unset($_SESSION['variation_list']);
            global $wpdb;
            // Store Enquiry details Setup
                if ($enquiry_action_type == 'multiple'){
                    $products_data = $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data();
                    foreach ($products_data as $key => $value) {
                        
                        $enq_title = sanitize_text_field(apply_filters( 'woocommerce_catalog_enquiry_prefix', '#')) . get_current_user_id() . $value['product_id'];
                        
                        $data_product = $value['product_id'];
                        $vendor_id = get_current_user_id();
                        
                        $enquiry_post = array(
                            'ID' => '',
                            'post_title' => $enq_title,
                            'post_name'  => sanitize_title($enq_title),
                            'post_type' => 'wcce_enquiry',
                            'post_status' => 'publish'
                            );
                        $enq_id = wp_insert_post( $enquiry_post );

                        update_post_meta( $enq_id, '_enquiry_product', $value['product_id'] );
                        update_post_meta( $enq_id, '_enquiry_action_type', $enquiry_action_type);
                        update_post_meta( $enq_id, '_enquiry_username', $user_name );
                        update_post_meta( $enq_id, '_enquiry_useremail', $user_email );
                        $multiple_quantity = isset($value['quantity']) ? $value['quantity'] : 1;
                        update_post_meta( $enq_id, '_enquiry_product_quantity', $multiple_quantity );
                        if ($woocommerce_catalog_enquiry_fields) update_post_meta( $enq_id, '_user_enquiry_fields', $woocommerce_catalog_enquiry_fields );
                        if ($uploaded_file_link) update_post_meta( $enq_id, '_enquiry_filelink', $uploaded_file_link );

                    }
                   
                    $Woocommerce_Catalog_Enquiry_Pro_Cart->unset_session();
                    // display chat at database
                    // to user id
                    if (is_active_MVX()){
                        $vendor_details = get_mvx_product_vendors( $data_product ) ? get_mvx_product_vendors( $data_product ) : false;
                    } else {
                        $vendor_details = false;
                    }  
                    if ( $vendor_details ){
                        $to_user_id = $vendor_details->id;
                    } else {
                        $admin_email  = get_option('admin_email');
                        $User_details = get_user_by('email', $admin_email);
                        $to_user_id   = $User_details->data->ID;
                    }

                    $chat_message = '';
                    foreach($woocommerce_catalog_enquiry_fields as $key => $field){ 
                        if ($field['type'] != 'file'){
                           $chat_message.= '<strong>'.$field['label'].':</strong><br>'.$field['value'].'<br>';
                        }
                    }
                    $wpdb->query("insert into {$wpdb->prefix}catelog_cust_vendor_answers set to_user_id='{$to_user_id}', from_user_id = '{$vendor_id}',chat_message='{$chat_message}',product_id='{$data_product}',enquiry_id='{$enq_id}',status='unread' ");

                } else {

                    $data_product = $product_data;
                    $vendor_id = get_current_user_id();

                    $enq_title = sanitize_text_field(apply_filters( 'woocommerce_catalog_enquiry_prefix', '#')) . get_current_user_id() . $product_data;
                    $enquiry_post = array(
                        'ID' => '',
                        'post_title' => $enq_title,
                        'post_name'  => sanitize_title($enq_title),
                        'post_type' => 'wcce_enquiry',
                        'post_status' => 'publish'
                        );
                    $enq_id = wp_insert_post( $enquiry_post );
                    update_post_meta( $enq_id, '_enquiry_product', $product_data );
                    update_post_meta( $enq_id, '_enquiry_product_quantity', $quantity );
                    update_post_meta( $enq_id, '_enquiry_action_type', $enquiry_action_type);
                    update_post_meta( $enq_id, '_enquiry_username', $user_name );
                    update_post_meta( $enq_id, '_enquiry_useremail', $user_email );
                    if ($woocommerce_catalog_enquiry_fields) update_post_meta( $enq_id, '_user_enquiry_fields', $woocommerce_catalog_enquiry_fields );
                    if ($uploaded_file_link) update_post_meta( $enq_id, '_enquiry_filelink', $uploaded_file_link );
                    if ($other_info_product) update_post_meta( $enq_id, '_enquiry_product_other_info', $other_info_product );
                    if ($product_variations) update_post_meta( $enq_id, '_enquiry_product_variations', $product_variations );
                    // display chat at database

                    // to user id
                    if (is_active_MVX()){
                        $vendor_details = get_mvx_product_vendors( $data_product ) ? get_mvx_product_vendors( $data_product ) : false;
                    } else {
                        $vendor_details = false;
                    }  
                    if ( $vendor_details ){
                        $to_user_id = $vendor_details->id;
                    } else {
                        $admin_email  = get_option('admin_email');
                        $User_details = get_user_by('email', $admin_email);
                        $to_user_id   = $User_details->data->ID;
                    }

                    $chat_message = '';
                    foreach($woocommerce_catalog_enquiry_fields as $key => $field){ 
                        if ($field['type'] != 'file'){
                           $chat_message.= '<strong>'.$field['label'].':</strong><br>'.$field['value'].'<br>';
                        }
                    }
                    $wpdb->query("insert into {$wpdb->prefix}catelog_cust_vendor_answers set to_user_id='{$to_user_id}', from_user_id = '{$vendor_id}',chat_message='{$chat_message}',product_id='{$data_product}',enquiry_id='{$enq_id}',status='unread' ");
                }

            do_action( 'woocommerce_catalog_sent_product_enquiry' );
        /*} else {
             $flag = 0;
        }	*/
        $redirect_link = isset($settings['redirect_page_id']) ? get_permalink(mvx_catalog_get_settings_value($settings['redirect_page_id'], 'select')) : '';
        wp_send_json(array('value' => $flag, 'error_report' => $this->error_mail_report, 'for' => $enquiry_action_type, 'settings' => $settings, 'redirect_link' => $redirect_link ));
        die;	  
    }

    public function woocommerce_catalog_add_to_enquiry_action_callback() {
	global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry_Pro_Cart;
	$settings = get_woocommerce_catalog_catalog_settings();
        $return  = 'false';
        $message = '';
        $product_variation = array();
        $enquiry_data = array();
        $errors = array();
        $product_id = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;

        $product = wc_get_product( $product_id );
        $post = get_post( $product->get_id() );

        if ($product->get_type() == 'variation'){
            if (isset($_SESSION['variation_list']))
                $_POST['variation'] = $_SESSION['variation_list'];
            else
                $_POST['variation'] = array();
        } else {
            $_POST['variation'] = array();
        }

        // MVX
        $is_vendor_product = false;
        $vendor_css_class = '';
        if (is_active_MVX()){
            if (is_object($post)){
                $product_author = get_mvx_product_vendors($post->ID);
                $is_vendor_product = is_user_mvx_vendor($product_author->id);
            }
        }
        if ($is_vendor_product){
            $product_author = get_mvx_product_vendors($post->ID);
            $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
            $vendor_css_class = 'vendor_'.$product_author->id.'_custom_enquiry_buttons_css';
        }

        $enquiry_data = apply_filters( 'woocommerce_catalog_add_to_enquiry_cart_item_data', $_POST,$product_id);

        if ( $product_id == false ) {
            $errors[] = __( 'Error occurred while adding product to Enquiry Cart.', 'woocommerce-catalog-enquiry-pro' );
        }
        else {
            $return = $Woocommerce_Catalog_Enquiry_Pro_Cart->add_enquiry( $enquiry_data );
        }

        if ( $return == 'true' ) {
            $message = apply_filters( 'woocommerce_catalog_product_added_to_enquiry_cart_message', __( 'Product added to Enquiry Cart!', 'woocommerce-catalog-enquiry-pro' ) );
        }
        elseif ( $return == 'exists' ) {
            $message = apply_filters( 'woocommerce_catalog_product_already_in_enquiry_cart_message', __( 'Product already in Enquiry Cart.', 'woocommerce-catalog-enquiry-pro' ) );
        }
        elseif ( count( $errors ) > 0 ) {
            $message = apply_filters( 'woocommerce_catalog_error_adding_to_enquiry_cart_message', $this->get_errors($errors) );
        }

        $view_enq_cart_btn_text = __('View Enquiry Cart','woocommerce-catalog-enquiry-pro');
        if (isset($settings['view_enquiry_cart_button_text']) && !empty($settings['view_enquiry_cart_button_text']))
            $view_enq_cart_btn_text = $settings['view_enquiry_cart_button_text'];
        $btn_style = '';
        if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {
            $btn_style = ' custom_enquiry_buttons_css'; 
            if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable') $btn_style = ' '.$vendor_css_class;
        }
        $mini_enquiry_cart = '';
        ob_start();
        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-mini-cart.php',array());
        $mini_enquiry_cart = ob_get_clean();

        wp_send_json(
            array(
                'result'       => $return,
                'show_message' => apply_filters('woocommerce_catalog_show_added_to_enquiry_cart_message', true),
                'message'      => $message,
                'btn_style'      => $btn_style,
                'label_view_cart' => $view_enq_cart_btn_text,
                'enquiry_cart_url' => $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_cart_page_url(),
                'enquiry_mini_cart' => $mini_enquiry_cart,
            )
        );
    }
    
    public function woocommerce_catalog_update_enquiry_cart_action_callback() {
        global $Woocommerce_Catalog_Enquiry_Pro_Cart;
        $enquiry_cart_updated = false;
        $update_msg = '';
        $enquiry_cart_update_quantity  = isset( $_POST['wcce-enquiry-cart-quantity'] ) ? $_POST['wcce-enquiry-cart-quantity'] : '';
        if ( ! $Woocommerce_Catalog_Enquiry_Pro_Cart->is_empty_enquiry() && is_array( $enquiry_cart_update_quantity ) ) {
            foreach ( $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data() as $key => $values ) {
                $_product = wc_get_product(  isset( $values['variation_id'] ) ? $values['variation_id'] : $values['product_id'] );
                // Skip product if no updated quantity was posted
                if ( ! isset( $enquiry_cart_update_quantity[ $key ] ) || ! isset( $enquiry_cart_update_quantity[ $key ]['qty'] ) ) {
                    continue;
                }
                // Sanitize
		$quantity = apply_filters( 'woocommerce_catalog_enquiry_cart_quantity_amount_cart_item', wc_stock_amount( preg_replace( "/[^0-9\.]/", '', $enquiry_cart_update_quantity[ $key ]['qty'] ) ), $key );
                if ( '' === $quantity || $quantity == $values['quantity'] ) {
                    continue;
                }
                // Update cart validation
		$passed_validation = apply_filters( 'woocommerce_catalog_enquiry_cart_update_cart_validation', true, $key, $values, $quantity );

                // is_sold_individually
                if ( $_product->is_sold_individually() && $quantity > 1 ) {
                        $update_msg = sprintf( __( 'You can only have 1 %s in your cart.', 'woocommerce-catalog-enquiry-pro' ), $_product->get_name() );
                        $passed_validation = false;
                }

                if ( $passed_validation ) {
                    $Woocommerce_Catalog_Enquiry_Pro_Cart->update_enquiry( $key, 'quantity', $quantity );
                    $update_msg = apply_filters('woocommerce_catalog_enquiry_cart_updated_success_msg', __( 'Enquiry cart updated!', 'woocommerce-catalog-enquiry-pro'));
                    $enquiry_cart_updated = true;
                }
            }
        }
        wp_send_json(array('status' => $enquiry_cart_updated, 'msg' => $update_msg));
        die;
    }

    public function woocommerce_catalog_remove_from_enquiry_action_callback() {
    	global $woocommerce, $Woocommerce_Catalog_Enquiry_Pro_Cart;
        $product_id = ( isset( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) ? (int) $_POST['product_id'] : false;
        $is_valid   = $product_id && isset( $_POST['key'] );
        $status = false;
        if ( $is_valid ) {
            $status = $Woocommerce_Catalog_Enquiry_Pro_Cart->remove_enquiry( $_POST['key'] );
        }
        else {
            $status = false;
        }
        wp_send_json(array('status' => $status, 'cart_data' => $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data()));
        die();
    }

}
