<?php

if(!function_exists('woocommerce_catalog_enquiry_alert_notice')) {
    function woocommerce_catalog_enquiry_alert_notice() {
    ?>
    <div id="message" class="error settings-error notice is-dismissible">
      <p><?php printf( __( '%sWoocommerce Catalog Enquiry is inactive.%s The %sWooCommerce plugin%s must be active for the Woocommerce Catalog Enquiry to work. Please %sinstall & activate WooCommerce%s', WOOCOMMERCE_CATALOG_ENQUIRY_PRO_TEXT_DOMAIN ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
      <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
        <?php
    }
}

// Woocommerce catalog enquiry not active massage
if (!function_exists('woocommerce_catalog_enquiry_not_active_alert_notice')) {
    function woocommerce_catalog_enquiry_not_active_alert_notice() {
        ?>
        <div id="message" class="error settings-error notice is-dismissible">
          <p><?php printf( __( '%sWoocommerce Catalog Enquiry%s is not Active. Please Active Woocommerce Catalog Enquiry first to activate Woocommerce Catalog Enquiry Pro.', WOOCOMMERCE_CATALOG_ENQUIRY_PRO_TEXT_DOMAIN ), '<strong>', '</strong>' ); ?></p>
          <button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
        <?php
    }
}

// Check MVX active
if(!function_exists('is_active_MVX')) {
    function is_active_MVX() {
      if(class_exists('MVX'))
        return true;
      else
        return false;
    }
}

if (!function_exists('woocommerce_catalog_enquiry_pro_migration_1_to_2')) {
    function woocommerce_catalog_enquiry_pro_migration_1_to_2() {
        if( !get_option( 'woocommerce_catalog_pro_migration_completed' ) ) :

        $woocommerce_catalog_pro_email_template = get_option( 'dc_mvx_Woocommerce_Catalog_Enquiry_email_tpl_settings_name', true );
        if ( !empty( $woocommerce_catalog_pro_email_template ) ) {
            update_option( 'woocommerce_catalog_enquiry_email_template_settings', $woocommerce_catalog_pro_email_template );
        }

        $enquiry_pro_old_buttons = get_option( 'dc_mvx_Woocommerce_Catalog_Enquiry_button_settings_name', true );
        if ( isset( $enquiry_pro_old_buttons['is_button'] ) && isset($enquiry_pro_old_buttons['custom_enquiry_buttons_css']) ) {
                unset( $enquiry_pro_old_buttons['is_button'] );
                unset( $enquiry_pro_old_buttons['custom_enquiry_buttons_css'] );

            $enquery_new_button_appearence = get_option( 'woocommerce_catalog_enquiry_button_appearence_settings', true );
            $marge_button = array_merge($enquery_new_button_appearence, $enquiry_pro_old_buttons);

            update_option( 'woocommerce_catalog_enquiry_button_appearence_settings', $marge_button );
        } 
        //New general data
        $enquiry_pro_new_general = get_option( 'woocommerce_catalog_enquiry_general_settings', true );
        // Old data
        $woocommerce_catalog_old_options = get_option('dc_mvx_Woocommerce_Catalog_Enquiry_general_settings_name', true );
        $enquiry_general_pro = array('is_enable_add_to_cart', 'is_enable_quantity_cart', 'is_remove_price', 'is_replace_price_with_txt', 'product_enquiry_position', 'is_enable_multiple_product_enquiry');
        $pro_catalog_general_data = array();
        if( is_array($woocommerce_catalog_old_options) && !empty( $woocommerce_catalog_old_options ) ) {
            foreach ($woocommerce_catalog_old_options as $key => $value) {
                if ( in_array($key, $enquiry_general_pro) ) {
                    $pro_catalog_general_data[$key] = $value;
                }
            }
        }
        $enquiry_pro_new_general = is_array($enquiry_pro_new_general) ? $enquiry_pro_new_general : array();
        $pro_general_data = array_merge( $enquiry_pro_new_general, $pro_catalog_general_data );
        update_option( 'woocommerce_catalog_enquiry_general_settings', $pro_general_data );
        update_option( 'woocommerce_catalog_pro_migration_completed', 'migrated' );

        // Form setting data migrated
        $store_from_details = array();
        $count = 0;
        if ( !empty( get_option( 'woocommerce_catalog_enquiry_from_settings' ) ) ) {
            foreach (get_option( 'woocommerce_catalog_enquiry_from_settings' ) as $key_from => $value_from) {

                if (!is_array($value_from)) continue;
                
                if (isset($value_from['is_enable']) && $value_from['is_enable'] == 'Enable') {
                    $store_from_details[] = array(
                        'id' => $count,
                        'type' => 'textarea',
                        'label' => $value_from['label'],
                        'hidden' => 1,
                        'partial' => 'textarea.html',
                        'placeholder' => '',
                        'required' => '',
                        'cssClass' => ''
                        );
                    $count++;
                }
            }
            update_option( 'wcce_enquiry_form_data', $store_from_details );   
        }

        endif;
    }
}

// Get vendor enquiry details
if(!function_exists('get_vendor_enquiry_details')) {
    function get_vendor_enquiry_details($vendor_id, $args = array(), $post_data = array()) {         
        global $wpdb;
        $default = array(
            'posts_per_page'    => -1,
            'post_type'         => 'wcce_enquiry',
            'post_status' => array('publish', 'pending', 'completed', 'read', 'unread'),
            'author__in'   => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'customer_name' && !empty($post_data['second_choice']) ? $post_data['second_choice'] : '',
            'meta_query' => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'product_name' && !empty($post_data['second_choice']) ? array(
                array(
                    'key'   => '_enquiry_product',
                    'value' => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'product_name' ? ( isset( $post_data['second_choice'] ) ? $post_data['second_choice'] : '' ) : '',
                    )
                ) : array()
            );
        $args = wp_parse_args($args, $default);
        $enquiry_posts = get_posts( $args );
        $vendor_product_enquiry = array();
        $admin_product_enquiry = array();
        
        if ( count($enquiry_posts) > 0 ) {
            foreach($enquiry_posts as $enquiry) { 
                $admin_product_enquiry[] = $enquiry->ID;
                $product_data = get_post_meta( $enquiry->ID, '_enquiry_product', true );
                $enquiry_type = get_post_meta( $enquiry->ID, '_enquiry_action_type', true);
                $product_author = array();
                    
                $product = wc_get_product( $product_data );
                if ($product && $product->get_type() == 'variation') {
                    $parent_id = $product->get_parent_id();
                    $_product = wc_get_product( $parent_id );
                    if(is_active_MVX()){
                        $product_author = get_mvx_product_vendors($_product->get_id());
                    } else {
                        $post = get_post($_product->get_id());
                        $product_author = $post;
                    }
                } elseif ($product){
                    if(is_active_MVX()){
                        $product_author = get_mvx_product_vendors($product->get_id());
                    }else{
                        $post = get_post($product->get_id());
                        $product_author = $post;
                    }
                }
                if(is_active_MVX()){
                    if($product_author && $product_author->id == $vendor_id)
                        $vendor_product_enquiry[] = $enquiry->ID;
                } else {
                    if($product_author && $product_author->post_author == $vendor_id)
                        $vendor_product_enquiry[] = $enquiry->ID;
                }
            }
        }

        // Date query
        if (isset( $post_data['catalog_start_date_order'] )) {
           $start_date_next = date('Y-m-d G:i:s', strtotime($post_data['catalog_start_date_order']));
        }

        if (isset( $post_data['catalog_end_date_order'] )) {
           $end_date_next = date('Y-m-d G:i:s', strtotime($post_data['catalog_end_date_order'] . ' +1 day'));
        }

        if( isset($post_data['date_action']) && isset( $post_data['catalog_start_date_order'] ) && isset( $post_data['catalog_end_date_order'] ) ) {

            $date_query['date_query'] =  array(
                array(
                    'after'     => $start_date_next,
                    'before'    => $end_date_next,
                    'inclusive' => true,
                ),
            );
            $default = array_merge($default, $date_query);
            $enquiry_posts = get_posts( $default );
            $post_ids = wp_list_pluck( $enquiry_posts , 'ID' );
            $vendor_product_enquiry = array_intersect( $post_ids, $vendor_product_enquiry );
        }

        // Find enquiry number
        if (isset($post_data['first_choice']) && $post_data['first_choice'] == 'enquiry_number' && !empty($post_data['second_choice'])) {
            $post_ids = $post_data['second_choice'];
            $vendor_product_enquiry = array_intersect( $post_ids, $vendor_product_enquiry );
        }

        // Find read massage
        if (isset($post_data['others_choice']) && $post_data['others_choice'] == 'read_name') {


            $post_ids_read = array();
            $default_read = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('read'),
                );
            $unread_posts = get_posts( $default_read );
            $post_ids = wp_list_pluck( $unread_posts , 'ID' );

            $vendor_product_enquiry = array_intersect( $post_ids, $vendor_product_enquiry );
        }

        // Find unread massage
        if (isset($post_data['others_choice']) && $post_data['others_choice'] == 'unread_name') {

            $post_ids_first = array();
            $post_ids_unread = array();
            $default_unread = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('unread'),
                );
            $unread_posts = get_posts( $default_unread );
            $post_ids_unread = wp_list_pluck( $unread_posts , 'ID' );

            $post_id_unread = $wpdb->get_results("SELECT enquiry_id FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (to_user_id = '". get_current_user_id() ."' AND status = 'unread' ) ");
            foreach ($post_id_unread as $key_unread => $value_unread) {
                $post_ids_first[] = $value_unread->enquiry_id;
            }

            $post_ids = array_merge( $post_ids_first, $post_ids_unread );

            $vendor_product_enquiry = array_intersect( $post_ids, $vendor_product_enquiry );
        }

        // Find completed enquiry
        if (isset($post_data['others_choice']) && $post_data['others_choice'] == 'completed_name') {
            $default = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('completed'),
                );
            $completed_posts = get_posts( $default );
            $post_ids = wp_list_pluck( $completed_posts , 'ID' );
            $vendor_product_enquiry = array_intersect( $post_ids, $vendor_product_enquiry );
        }

        $current_user = wp_get_current_user();
        if(in_array('administrator', $current_user->roles))
            return $admin_product_enquiry;
        else
            return array_unique($vendor_product_enquiry);
    }
}

if(!function_exists('get_userList_for_vendor')) {
    function get_userList_for_vendor() {  
        $user_query = new WP_User_Query( array( 'role__not_in' => 'Administrator' ) );
        $users = $user_query->get_results();
        $all_users = array();
        foreach($users as $user) {                  
            $all_users[$user->data->ID] = $user->data->display_name;              
        }
        return $all_users;
    }
}
  
  
if(!function_exists('get_productList_for_vendor')) { 
    function get_productList_for_vendor() {
        $user_id = get_current_user_id();
        $vendor = get_mvx_vendor($user_id);
        $all_products = array();
        $args = array( 'posts_per_page' => -1, 'post_type' => 'product', 'orderby' => 'title', 'order' => 'ASC' );
        $myposts = get_posts( $args );
        foreach ( $myposts as $post ) : setup_postdata( $post ); 
            $product_author = get_mvx_product_vendors($post->ID);
            if($product_author->id == $vendor->id)
                $all_products[$post->ID] = $post->post_title;     
        endforeach; 
        wp_reset_postdata();
        return $all_products;
    }
}
  

if(!function_exists('get_productCatagoryList_for_vendor')) { 
    function get_productCatagoryList_for_vendor() { 
        $all_product_cat = array();
        $args = array( 'orderby' => 'name', 'order' => 'ASC' );
        $terms = get_terms( 'product_cat', $args );
        foreach ( $terms as $term) {
            $all_product_cat[$term->term_id] = $term->name;
        }
        return $all_product_cat;
    }
}

if(!function_exists('get_all_pages_for_vendor')) { 
    function get_all_pages_for_vendor() {
        global $Woocommerce_Catalog_Enquiry_Pro;
        $pages = array();
        $args = array( 'posts_per_page' => -1, 'post_type' => 'page', 'orderby' => 'title', 'order' => 'ASC' );
        $myposts = get_posts( $args );
        foreach ( $myposts as $post ) : setup_postdata( $post );    
        $pages[$post->ID] = $post->post_title;       
        endforeach; 
        wp_reset_postdata();
        return $pages;
    }
}

if(!function_exists('get_mvx_vendor_data')) { 
    function get_mvx_vendor_data($key) { 
        $user_id = get_current_user_id();
        $vendor = get_mvx_vendor($user_id);
        $mvx_vendor_catalog_settings = get_mvx_catalog_settings($vendor->id);
        return isset($mvx_vendor_catalog_settings[$key]) ? $mvx_vendor_catalog_settings[$key] : '';
    }
}

if(!function_exists('get_mvx_catalog_settings')) { 
    function get_mvx_catalog_settings($vendor_id) {
        if(!empty($vendor_id))
            return get_user_meta($vendor_id, '_mvx_vendor_catalog_settings',true);
    }
}

if ( ! function_exists( 'is_enquiry_cart' ) ) {
    /**
    * is_cart - Returns true when viewing the enquiry cart page.
    * @return bool
    */
    function is_enquiry_cart() {
        global $wp_query;
        return is_page( 110 ) || wc_post_content_has_shortcode( 'woocommerce_catalog_enquiry_cart' );
    }
}

if(!function_exists('is_mvx_vendor_product')) {
    function is_mvx_vendor_product($product_id) {
        $user = wp_get_current_user();
        if(is_active_MVX()){
            $vendor = get_mvx_vendor($user->ID); 
            $product = wc_get_product($product_id);
            $product_author = array();
            if($product && $product->get_type() == 'variation'){
                $parent_id = $product->get_parent_id();
                $_product = wc_get_product( $parent_id );
                $product_author = get_mvx_product_vendors($_product->get_id());
            }elseif($product){
                $product_author = get_mvx_product_vendors($product->get_id());
            } 
            if($product_author && $vendor && $product_author->id == $vendor->id)
                return true;
            else
                return false;
        } else {
            return false;
        }
    }
}

// Get Catalog settings
if(!function_exists('get_woocommerce_catalog_catalog_settings')) {
    function get_woocommerce_catalog_catalog_settings($vendor_id = '') {
        global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry;
        $general = (array)$Woocommerce_Catalog_Enquiry->options_general_settings;
        $form = (array)$Woocommerce_Catalog_Enquiry->options_form_settings;
        $exclusion = (array)$Woocommerce_Catalog_Enquiry->options_exclusion_settings;
        $button = (array)$Woocommerce_Catalog_Enquiry->options_button_appearence_settings;
        
        $email_tpl = (array)$Woocommerce_Catalog_Enquiry_Pro->email_tpl;
        $admin_settings = array_merge($general,$form,$exclusion,$button,$email_tpl);
        $vendor_settings = array();
        if($vendor_id){
            $settings = get_mvx_catalog_settings($vendor_id);
            if($settings){
                foreach ($settings as $key => $value) {
                    if($value){
                        $vendor_settings[$key] = $value;
                    }else{
                        if(isset($admin_settings[$key]))
                            $vendor_settings[$key] = $admin_settings[$key];
                        else
                            $vendor_settings[$key] = $value;
                    }
                }
            }
        }
        return array_merge((array)$admin_settings,(array)$vendor_settings);
    }
}


// Get Catalog notice
if(!function_exists('woocommerce_catalog_get_notice')) {
    function woocommerce_catalog_get_notice( $message, $type = 'success') {
        if($message){
            return '<div class="alert alert-'.$type.'">'.$message.'</div>';
        }
    }
}

// Get customer enquiry details
if(!function_exists('get_customer_enquiry_details')) {
    function get_customer_enquiry_details( $customer_id, $args = array(), $post_data = array() ) {
        global $wpdb;
        $default = array(
          'posts_per_page'    => -1,
          'post_type'         => 'wcce_enquiry',
          'author' => $customer_id,
          'post_status' => array('publish', 'pending', 'completed', 'read', 'unread'),
          'meta_query' => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'product_name' ? array(
                array(
                    'key'   => '_enquiry_product',
                    'value' => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'product_name' ? ( isset( $post_data['second_choice'] ) ? $post_data['second_choice'] : '' ) : '',
                    )
                ) : array() 
        );
        $args = wp_parse_args($args, $default);
        $enquiry_posts = get_posts( $args );
        $post_ids = wp_list_pluck( $enquiry_posts , 'ID' );

        return $post_ids;
    }
}

// Get customer enquiry details
if(!function_exists('get_all_enquiry_details')) {
    function get_all_enquiry_details( $post_data ) {
        global $wpdb;

        $default = array(
          'posts_per_page'    => -1,
          'post_type'         => 'wcce_enquiry',
          'post_status' => array('publish', 'pending', 'completed', 'read', 'unread'),
          'meta_query' => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'product_name' && $post_data['second_choice'] ? array(
                array(
                        'key'   => '_enquiry_product',
                        'value' => isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'product_name' ? ( isset( $post_data['second_choice'] ) ? $post_data['second_choice'] : '' ) : '',
                    )
            ) : array()
           
        );
        if ( isset( $post_data['first_choice'] ) && $post_data['first_choice'] == 'customer_name' && !empty($post_data['second_choice']) ) {
            $author_query['author__in'] = $post_data['second_choice'];
            $default = array_merge($default, $author_query);
        }

        // Date query
        if (isset( $post_data['catalog_start_date_order'] )) {
           $start_date_next = date('Y-m-d G:i:s', strtotime($post_data['catalog_start_date_order']));
        }

        if (isset( $post_data['catalog_end_date_order'] )) {
           $end_date_next = date('Y-m-d G:i:s', strtotime($post_data['catalog_end_date_order'] . ' +1 day'));
        }

        if( isset($post_data['date_action']) && isset( $post_data['catalog_start_date_order'] ) && isset( $post_data['catalog_end_date_order'] ) ) {

            $date_query['date_query'] =  array(
                array(
                    'after'     => $start_date_next,
                    'before'    => $end_date_next,
                    'inclusive' => true,
                ),
            );
            $default = array_merge($default, $date_query);
        }

        $enquiry_posts = get_posts( $default );
        $post_ids = wp_list_pluck( $enquiry_posts , 'ID' );

        // Find enquiry number
        if (isset($post_data['first_choice']) && $post_data['first_choice'] == 'enquiry_number' && !empty($post_data['second_choice'])) {
            $post_ids = $post_data['second_choice'];
        }

        // Find unread massage
        if (isset($post_data['others_choice']) && $post_data['others_choice'] == 'unread_name') {
            $post_ids_first = array();

            $post_ids_unread = array();
            $default_unread = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('unread'),
                );
            $unread_posts = get_posts( $default_unread );
            $post_ids_unread = wp_list_pluck( $unread_posts , 'ID' );

            $post_id_unread = $wpdb->get_results("SELECT enquiry_id FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (to_user_id = '". get_current_user_id() ."' AND status = 'unread' ) ");
            foreach ($post_id_unread as $key_unread => $value_unread) {
                $post_ids_first[] = $value_unread->enquiry_id;
            }

            $post_ids = array_merge( $post_ids_first, $post_ids_unread );
        }

        // Find read massage
        if (isset($post_data['others_choice']) && $post_data['others_choice'] == 'read_name') {
            $post_ids_read = array();
            $default_read = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('read'),
                );
            $unread_posts = get_posts( $default_read );
            $post_ids = wp_list_pluck( $unread_posts , 'ID' );

        }
        // Find completed enquiry
        if (isset($post_data['others_choice']) && $post_data['others_choice'] == 'completed_name') {
            $default = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('completed'),
                );
            $completed_posts = get_posts( $default );
            $post_ids = wp_list_pluck( $completed_posts , 'ID' );
        }

        return $post_ids;
    }
}

if(!function_exists('get_catalog_enquiry_post_titles')) {
    function get_catalog_enquiry_post_titles() {

        $enquiry_posts = get_posts( 
            array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('publish', 'pending', 'completed', 'read', 'unread'),
                )
            );
        $enquiry_titles = array();
        foreach ($enquiry_posts as $key => $value) {
            $enquiry_titles[$value->ID] = $value->post_title;
        }
        return $enquiry_titles;
    }
}

// Get vendor user conversation details
if(!function_exists('get_customer_vendor_conversation_details')) {
    function get_customer_vendor_conversation_details( $from_user_id, $to_user_id, $product_id ) {
        global $wpdb;
        $conversation_details = '';
        $post_id = $wpdb->get_results("SELECT chat_message,from_user_id,to_user_id, timestamp FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (from_user_id = '".$from_user_id."' AND to_user_id = '". $to_user_id ."' AND product_id = '". $product_id ."' ) OR (from_user_id = '".$to_user_id."' AND to_user_id = '". $from_user_id ."' AND product_id = '". $product_id ."')");
        if( $post_id ){

            foreach ($post_id as $key => $value) {
                $dateTime = new DateTime($value->timestamp); 
                $date_format = $dateTime->format("F d");
                $time_format = $dateTime->format("H:i A");

                if( get_current_user_id() == $value->from_user_id ){
                    $conversation_details .=   '<div class="outgoing-msg">
                                <div class="sent-msg">
                                <p>'.$value->chat_message.'</p>
                                <span class="time-date"> '.  $date_format .'    |    '. $time_format.'</span> </div>
                            </div>';
                } else { 
                    $conversation_details .= '<div class="incoming-msg">
                        <div class="received-msg">
                            <div class="received-withd-msg">
                                <p> '.$value->chat_message.' </p>
                                    <span class="time-date">'.  $date_format .'    |    '. $time_format.'</span>
                            </div>
                        </div>
                    </div>';
                }
            }
        } else {
            $no_coversation_text = __('No conversation found !!', 'woocommerce-catalog-enquiry-pro');
            $conversation_details = '<div class="noConvrstnCls">'. $no_coversation_text .'</div>';
        }
    return $conversation_details;
    }
}

if(!function_exists('get_customer_vendor_admin_conversation_details')) {
    function get_customer_vendor_admin_conversation_details( $from_user_id, $to_user_id, $product_id ) {
        global $wpdb;
        
       $post_id = $wpdb->get_results("SELECT chat_message_id,status,chat_message,from_user_id,to_user_id,enquiry_id, timestamp FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (from_user_id = '".$from_user_id."' AND to_user_id = '". $to_user_id ."' AND product_id = '". $product_id ."' ) OR (from_user_id = '".$to_user_id."' AND to_user_id = '". $from_user_id ."' AND product_id = '". $product_id ."')");
       return $post_id;
    }
}

if(!function_exists('get_user_last_massage')) {
    function get_user_last_massage( $from_user_id, $to_user_id, $product_id ) {
        global $wpdb;
        $post_id = $wpdb->get_results("SELECT chat_message,from_user_id,to_user_id, timestamp FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (from_user_id = '".$from_user_id."' AND to_user_id = '". $to_user_id ."' AND product_id = '". $product_id ."' ) OR (from_user_id = '".$to_user_id."' AND to_user_id = '". $from_user_id ."' AND product_id = '". $product_id ."')");
       return $post_id;
    }
}

// find all wp users
if (!function_exists('woocommerce_catalog_wp_users')) {
  function woocommerce_catalog_wp_users(){
    $users = get_users();
    $all_users = array();
    foreach($users as $user) {                  
      $all_users[$user->data->ID] = $user->data->display_name;
    }
    return $all_users;
  }
}

if (!function_exists('mvx_catalog_pro_convert_select_structure')) {
    function mvx_catalog_pro_convert_select_structure($data_fileds = array(), $csv = false, $object = false) {
        $is_csv = $csv ? 'key' : 'value';
        $datafileds_initialize_array = [];
        if ($data_fileds) {
            foreach($data_fileds as $fileds_key => $fileds_value) {
                if ($object) {
                    $datafileds_initialize_array[] = array(
                        'value' => $fileds_value->ID,
                        'label' => $fileds_value->post_title
                    );
                } else {
                    $datafileds_initialize_array[] = array(
                        $is_csv => $csv ? $fileds_value : $fileds_key,
                        'label' => $fileds_value
                    );
                }
            }
        }
        return $datafileds_initialize_array;
    }
}