<?php
class Woocommerce_Catalog_Enquiry_Pro {

    public $plugin_url;
    public $plugin_path;
    public $version;
    public $token;
    public $text_domain;
    public $shortcode;
    public $admin;
    public $frontend;
    public $template;
    public $ajax;
    private $file;  
    public $settings;
    public $catalog;
    public $license;
    public $email_tpl;
    public $library;

    public function __construct($file) {

        $this->file = $file;
        $this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
        $this->plugin_path = trailingslashit(dirname($file));
        $this->token = WOOCOMMERCE_CATALOG_ENQUIRY_PRO_PLUGIN_TOKEN;
        $this->text_domain = WOOCOMMERCE_CATALOG_ENQUIRY_PRO_TEXT_DOMAIN;
        $this->version = WOOCOMMERCE_CATALOG_ENQUIRY_PRO_PLUGIN_VERSION;
        
        $this->email_tpl = get_option('mvx_catalog_enquiry_pro_field_tab_settings');
        
        // Init Text Domain
        $this->load_plugin_textdomain();
        // init catalog cron job
        $this->init_catalog_cron_job();

        // DC License Activation
        $this->load_class('license');
        $this->license =  new Woocommerce_Catalog_Enquiry_Pro_License( $this->file, $this->plugin_path, WOOCOMMERCE_CATALOG_ENQUIRY_PRO_PLUGIN_PRODUCT_ID, $this->version, 'plugin', WOOCOMMERCE_CATALOG_ENQUIRY_PRO_PLUGIN_SERVER_URL, WOOCOMMERCE_CATALOG_ENQUIRY_PRO_PLUGIN_SOFTWARE_TITLE, $this->text_domain  );

        add_action('init', array(&$this, 'init'), 0);

        // Woocommerce Email structure
        add_filter('woocommerce_email_classes', array(&$this, 'woocommerce_catalog_enquiry_email_callback' ));

        add_filter( 'woocommerce_catalog_enquiry_free_active', '__return_false' );       
        // MVX 
        if (is_active_MVX()) {
            add_action('mvx_init', array(&$this, 'mvx_init'));
            add_action('wc_am_after_plugin_activation', array(&$this, 'activate_mvx_Woocommerce_Catalog_Enquiry'));
            add_action('wc_am_after_plugin_deactivation', array(&$this, 'deactivate_mvx_Woocommerce_Catalog_Enquiry'));
            
            // Capabilities
            add_filter('settings_capabilities_product_tab_options', array($this, 'set_catalog_enquiry_capabilities'));
            add_filter("settings_capabilities_product_tab_new_input", array($this, 'save_catalog_enquiry_capabilities'),10,2 );
        }

        // Enquiry button on customer my account page
        add_filter( 'woocommerce_account_menu_items',array($this, 'iconic_account_menu_items'), 40 );
        add_action( 'init',array($this, 'iconic_add_my_account_endpoint1') );       
        add_action( 'woocommerce_account_enquiry_endpoint', array($this, 'iconic_information_endpoint_content1' ));

        add_filter('mvx_catalog_add_query', '__return_true');
        add_filter('mvx_catalog_free_only_active', '__return_false');


        add_filter('mvx_catalog_endpoint_fields_before_value', array($this, 'mvx_catalog_endpoint_fields_before_value'));
        // rest api call
        add_action('init', array(&$this, 'catalog_pro_init'));
        

        add_filter('mvx_settings_fields_details', array($this, 'mvx_settings_fields_details_for_catalog_pro'));
    }

    public function catalog_pro_init() {
        add_action( 'rest_api_init', array( $this, 'catalog_peo_rest_routes_react_module' ) );
    }

    public function mvx_settings_fields_details_for_catalog_pro($settings_fileds) {
        $settings_fileds_report = [
            [
                'key'       => 'mvx_catalog_details_endpoint',
                'type'      => 'text',
                'label'     => __('Enquiry Details', 'mvx-pro'),
                'desc'      => __('Set endpoint for vendor catalog details page', 'mvx-pro'),
                'placeholder'   => __('catalog-details', 'mvx-pro'),
                'database_value' => '',
            ],
            [
                'key'       => 'mvx_catalog_settings_endpoint',
                'type'      => 'text',
                'label'     => __('Enquiry Settings', 'mvx-pro'),
                'desc'      => __('Set endpoint for vendor catalog settings page', 'mvx-pro'),
                'placeholder'   => __('catalog-settings', 'mvx-pro'),
                'database_value' => '',
            ],
        ];
        $settings_fileds['seller-dashbaord'] = array_merge($settings_fileds['seller-dashbaord'], $settings_fileds_report);
        return $settings_fileds;
    }

    public function catalog_peo_rest_routes_react_module() {

        // list of vendors on vendor tab section
        register_rest_route( 'mvx_catalog_pro/v1', '/fetch_enquiry_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_catalog_pro_fetch_enquiry_data' ),
            'permission_callback' => array( $this, 'catalog_pro_permission' )
        ] );

        register_rest_route( 'mvx_catalog_pro/v1', '/update_enquiry_data', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_catalog_pro_update_enquiry_data' ),
            'permission_callback' => array( $this, 'catalog_pro_permission' )
        ] );

        // fetch catalog post details
        register_rest_route( 'mvx_catalog_pro/v1', '/fetch_enquiry_post_details', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_catalog_pro_fetch_enquiry_post_details' ),
            'permission_callback' => array( $this, 'catalog_pro_permission' ),
            'args'     => [
                'current_id'     => get_current_user_id(),
            ],
        ] );

        // fetch catalog post details
        register_rest_route( 'mvx_catalog_pro/v1', '/update_msg_by_admin', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_catalog_pro_update_msg_by_admin' ),
            'permission_callback' => array( $this, 'catalog_pro_permission' ),
            'args'     => [
                'current_id'     => get_current_user_id(),
            ],
        ] );
    }

    public function mvx_catalog_pro_update_msg_by_admin($request) {
        global $wpdb;
        $data_value = $request && $request->get_param('data_value') ? $request->get_param('data_value') : '';
        $chat_message = $request && $request->get_param('text') ? $request->get_param('text') : '';
        $data_product = !empty($data_value) ? $data_value['product_id'] : 0;
        $data_enquiry = !empty($data_value) ? $data_value['enquiry_id'] : 0;
        $vendor_id = !empty($data_value) ? $data_value['to_user_id'] : 0;


        //$vendor_id =  get_current_user_id();
        //$data_product = isset($_POST['data_product']) ? absint($_POST['data_product']) : 0;
        //$data_enquiry = isset($_POST['data_enquiry']) ? absint($_POST['data_enquiry']) : 0;

        if (is_active_MVX()) {
            $vendor_details = get_mvx_product_vendors( $data_product ) ? get_mvx_product_vendors( $data_product ) : false;
        } else {
            $vendor_details = false;
        }

        /*$user = get_userdata( $vendor_id );
        $user_roles = $user->roles;*/
        //if ( in_array( 'dc_vendor', $user_roles, true ) || in_array( 'administrator', $user_roles, true ) ){
            //$enquiry_user_email = get_post_meta( $data_enquiry , '_enquiry_useremail', true );
            //$user_details = get_user_by( 'email' , $enquiry_user_email );
            $user_details_id = get_user_by( 'ID' , get_post_field ('post_author', $data_enquiry) );
            $to_user_id = $user_details_id ? $user_details_id->data->ID : 0;
        /*} else {
            if ( $vendor_details ){
                $to_user_id = $vendor_details->id;
            } else {
                $admin_email = get_option('admin_email');
                $User_details = get_user_by('email', $admin_email);
                $to_user_id = $User_details->data->ID;
            }
        }*/
        if ( $vendor_details ){
            $vendor_id =  $vendor_details->id;
        }

        $wpdb->query("insert into {$wpdb->prefix}catelog_cust_vendor_answers set to_user_id='{$to_user_id}', from_user_id = '{$vendor_id}',chat_message='{$chat_message}',product_id='{$data_product}',enquiry_id='{$data_enquiry}',status='unread' ");
        
        // send email code
        /*if ( in_array( 'dc_vendor', $user_roles, true ) || in_array( 'administrator', $user_roles, true ) ){
            if ( apply_filters( 'wc_catalog_enquiry_send_mail_customer', true ) ){
                do_action( 'wc_catalog_enquiry_send_mail', $data_enquiry, $vendor_id, $chat_message );
            } 
        }*/

        //print_r($data_value);die;
    }

    public function mvx_catalog_pro_fetch_enquiry_post_details($request) {
        
        global $wpdb;
        $lists = [];
        $one_dropdown_status = $request && $request->get_param('first_choice') ? $request->get_param('first_choice') : '';
        $two_dropdown_selection_choice = $request && $request->get_param('second_choice') ? $request->get_param('second_choice') : '';        
        $three_dropdown_previous_depend = $request && $request->get_param('others_choice') ? $request->get_param('others_choice') : '';
        $catalog_start_date_order = $request && $request->get_param('catalog_start_date_order') ? $request->get_param('catalog_start_date_order') : '';
        $catalog_end_date_order = $request && $request->get_param('catalog_end_date_order') ? $request->get_param('catalog_end_date_order') : '';
        

        $current_user_id = $request && $request->get_attributes('args')['args']['current_id'] ? $request->get_attributes('args')['args']['current_id'] : '';
        
        /*if ($three_dropdown_previous_depend) {
            print_r($three_dropdown_previous_depend);
            echo "  ";
            print_r($one_dropdown_status);
            echo "  ";
            print_r($two_dropdown_selection_choice);


            die;
        }*/
        
        $default = array(
          'posts_per_page'    => -1,
          'post_type'         => 'wcce_enquiry',
          'post_status' => array('publish', 'pending', 'completed', 'read', 'unread'),
          'meta_query' => $two_dropdown_selection_choice && $two_dropdown_selection_choice == 'product_name' && $three_dropdown_previous_depend ? array(
                array(
                        'key'   => '_enquiry_product',
                        'value' => $two_dropdown_selection_choice && $two_dropdown_selection_choice == 'product_name' ? ( $three_dropdown_previous_depend ? $three_dropdown_previous_depend : '' ) : '',
                    )
            ) : array()
           
        );
        if ( $two_dropdown_selection_choice && $two_dropdown_selection_choice == 'customer_name' && !empty($three_dropdown_previous_depend) ) {
            $author_query['author__in'] = $three_dropdown_previous_depend;
            $default = array_merge($default, $author_query);
        }

        // Date query
        if ($catalog_start_date_order ) {
           $start_date_next = date('Y-m-d G:i:s', strtotime($catalog_start_date_order));
        }

        if ($catalog_end_date_order ) {
           $end_date_next = date('Y-m-d G:i:s', strtotime($catalog_end_date_order . ' +1 day'));
        }

        if( $catalog_start_date_order && $catalog_end_date_order ) {

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
        if ($two_dropdown_selection_choice && $two_dropdown_selection_choice == 'enquiry_number' && !empty($three_dropdown_previous_depend)) {
            $post_ids = $three_dropdown_previous_depend;
        }

        // Find unread massage
        if ($one_dropdown_status && $one_dropdown_status == 'unread_name') {
            $post_ids_first = array();

            $post_ids_unread = array();
            $default_unread = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('unread'),
                );
            $unread_posts = get_posts( $default_unread );
            $post_ids_unread = wp_list_pluck( $unread_posts , 'ID' );

            $post_id_unread = $wpdb->get_results("SELECT enquiry_id FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (to_user_id = '". $current_user_id ."' AND status = 'unread' ) ");
            foreach ($post_id_unread as $key_unread => $value_unread) {
                $post_ids_first[] = $value_unread->enquiry_id;
            }

            $post_ids = array_merge( $post_ids_first, $post_ids_unread );
        }

        // Find read massage
        if ($one_dropdown_status && $one_dropdown_status == 'read_name') {
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
        if ($one_dropdown_status && $one_dropdown_status == 'completed_name') {
            $default = array(
                'posts_per_page'    => -1,
                'post_type'         => 'wcce_enquiry',
                'post_status' => array('completed'),
                );
            $completed_posts = get_posts( $default );
            $post_ids = wp_list_pluck( $completed_posts , 'ID' );
        }

        if ($post_ids) {
            foreach ($post_ids as $key => $value) {

                $product_id = get_post_meta( $value ,'_enquiry_product', true ) ? get_post_meta( $value ,'_enquiry_product', true ) : '';
                $product = wc_get_product( $product_id );
                if(!$product) continue;

                $last_massage = '';
                
                $user_details = get_user_by( 'ID' , get_post_field ('post_author', $value) );
                $to_user_id = $user_details ? $user_details->data->ID : 0;

                if (is_active_MVX()) {
                    $vendor_details = get_mvx_product_vendors( $product_id ) ? get_mvx_product_vendors( $product_id ) : false;
                } else {
                    $vendor_details = false;
                }

                if ( $vendor_details ) {
                    $last_massage = get_user_last_massage( $vendor_details->id , $to_user_id, $product_id);
                } else {
                    $last_massage = get_user_last_massage( $current_user_id , $to_user_id, $product_id);
                }

                $histrory_unread = get_customer_vendor_admin_conversation_details( $current_user_id , $to_user_id, $product_id );
                $count = 0;
                foreach ($histrory_unread as $key1 => $value1) {
                    if ( $value1->status == 'unread' && $value1->from_user_id != $current_user_id ){
                        $count++;
                    }
                }
                global $wpdb;
                $conversation_lists = [];
                //$conversation = get_customer_vendor_conversation_details( $current_user_id , $to_user_id, $product_id );

                $conversation = $wpdb->get_results("SELECT chat_message,from_user_id,to_user_id, timestamp FROM {$wpdb->prefix}catelog_cust_vendor_answers WHERE (from_user_id = '".$current_user_id."' AND to_user_id = '". $to_user_id ."' AND product_id = '". $product_id ."' ) OR (from_user_id = '".$to_user_id."' AND to_user_id = '". $current_user_id ."' AND product_id = '". $product_id ."')");
                if ($conversation) {
                    foreach ($conversation as $key => $value_c) {
                        $dateTime = new DateTime($value_c->timestamp); 
                        $date_format = $dateTime->format("F d");
                        $time_format = $dateTime->format("H:i A");

                        $conversation_lists[] = [
                            'date_format'   =>  $date_format,
                            'time_format'   =>  $time_format,
                            'value_c'         =>  $value_c
                        ];
                    }
                }

                $catalog_status = array(  
                    'read' => __('Read', 'woocommerce-catalog-enquiry-pro'),
                    'unread' => __('Unread', 'woocommerce-catalog-enquiry-pro'),
                    'completed' => __('Closed', 'woocommerce-catalog-enquiry-pro'),
                    'delete' => __('Delete', 'woocommerce-catalog-enquiry-pro'),
                );

                $user_name_non_log_in = get_post_meta( $value, '_enquiry_username', true );
                $user_email_non_log_in = get_post_meta( $value, '_enquiry_useremail', true );
                $user_details = get_user_by( 'ID' , get_post_field ('post_author', $value) );

                $users_name = $user_details ? $user_details->data->user_login : $user_name_non_log_in;
                $users_email = $user_details ? $user_details->user_email : $user_email_non_log_in;
                $user_name_and_email = apply_filters('catalog_chat_display_email_with_name', true) ? $users_name . '( ' . $users_email . ' ) ' : $users_name;
                $user_image = get_avatar( $user_details->data->ID , 60 );

                $lists[] = array(
                    'product_id'                    =>  $product_id,
                    'enquiry_user_email'            =>  get_post_meta( $value , '_enquiry_useremail', true ) ? get_post_meta( $value , '_enquiry_useremail', true ) : '',
                    'to_user_id'        =>  $to_user_id,
                    'enquiry_id'        =>  $value,
                    'count'             =>  $count,
                    'last_massage'      =>  $last_massage ? end($last_massage)->chat_message : '',
                    'product_name'      =>  $product ? $product->get_name() : '',
                    'product_image'     =>  $product ? $product->get_image() : '',
                    'conversation_lists'      =>  $conversation_lists,
                    'current_user_id'   =>  $current_user_id,
                    'enquiry_current_status'    =>  get_post_status( $value ) && get_post_status( $value ) == 'publish' ? __('Open', 'woocommerce-catalog-enquiry-pro') : get_post_status( $value ),
                    'catalog_status'            =>  $catalog_status,
                    'user_name_and_email'       =>  $user_name_and_email,
                    'user_image'                =>  $user_image,
                    'enquiry_product_permalink' => get_permalink( $product_id ),
                    'enquiry_product_title'     => get_the_title( $product_id ),
                    'quantity_number'           => get_post_meta( $value, '_enquiry_product_quantity', true ),
                    'enquiry_post_title'        => get_the_title($value),
                    'enquiry_post_date'         => get_the_date('M j, Y' , $value)
                );
            }
        }

        return rest_ensure_response( $lists );
    }

    public function mvx_catalog_pro_fetch_enquiry_data() {
        $mvx_vendor_registration_form_data = mvx_get_option('mvx_catalog_pro_enquiry_form_data') ? mvx_get_option('mvx_catalog_pro_enquiry_form_data') : [];
        return rest_ensure_response( $mvx_vendor_registration_form_data );
    }

    public function mvx_catalog_pro_update_enquiry_data($request) {
        $form_data = json_decode(stripslashes_deep($request->get_param( 'form_data' )), true);
        if (!empty($form_data) && is_array($form_data)) {
            foreach ($form_data as $key => $value) {
                $form_data[$key]['hidden'] = false;
            }
        }
        mvx_update_option('mvx_catalog_pro_enquiry_form_data', $form_data);
        die;
    }

    public function catalog_pro_permission() {
        return true;
    }

    public function mvx_catalog_endpoint_fields_before_value($args) {
        $live_preview = $args['live-preview'];
        unset($args['live-preview']);
        unset($args['upgrade']);
        $args['enquiry-form']['modulename'][7] = array(
            'key'       => 'custom_email_subject88',
            'label'     => __( 'Enquiry Form fields', 'woocommerce-catalog-enquiry' ),
            'type'      => 'custom_fileds',
            'database_value' => mvx_get_option('mvx_catalog_pro_enquiry_form_data') ? mvx_get_option('mvx_catalog_pro_enquiry_form_data') : [],
        );
        $args['enquiry-pro-field'] = array(
            'tablabel'        =>  __('Enquiry Email Template', 'woocommerce-catalog-enquiry'),
            'apiurl'          =>  'save_enquiry',
            'description'     =>  __('Enquiry Email Template', 'woocommerce-catalog-enquiry'),
            'icon'            =>  'icon-general-tab',
            'submenu'         =>  'settings',
            'modulename'      =>  [
                [
                    'key'       =>  'woocommerce_catalog_enquiry_general_settings',
                    'type'      =>  'blocktext',
                    'label'     =>  __( 'no_label', 'woocommerce-catalog-enquiry' ),
                    'blocktext'      =>  __( "Common Settings", 'woocommerce-catalog-enquiry' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'selected_email_tpl',
                    'type'      => 'radio_select',
                    'label'     => __( 'Store Header', 'multivendorx' ),
                    'desc'      => __( "Select store banner style", 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'selected_email_tpl',
                            'key' => 'template1',
                            'label' => __('Outer Space', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/default_wc_tpl.png',
                            'value' => 'template1'
                        ),
                        array(
                            'name'  => 'selected_email_tpl',
                            'key'   => 'template2',
                            'label' => __('Green Lagoon', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_1.png',
                            'value' => 'template2'
                        ),
                        array(
                            'name'  => 'selected_email_tpl',
                            'key'   => 'template3',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_2.png',
                            'value' => 'template3'
                        ),
                        array(
                            'name'  => 'selected_email_tpl',
                            'key'   => 'template4',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_3.png',
                            'value' => 'template4'
                        ),
                        array(
                            'name'  => 'selected_email_tpl',
                            'key'   => 'template5',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_4.png',
                            'value' => 'template5'
                        ),
                        array(
                            'name'  => 'selected_email_tpl',
                            'key'   => 'template6',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_5.png',
                            'value' => 'template6'
                        ),
                        array(
                            'name'  => 'selected_email_tpl',
                            'key'   => 'template7',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => $this->plugin_url . 'assets/images/email/templates/woocommerce_catalog_send_email_tpl_6.png',
                            'value' => 'template7'
                        ),
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'custom_email_subject',
                    'type'      => 'textarea',
                    'class'     =>  'mvx-setting-wpeditor-class',
                    'desc'      => __('Available tags |USER_NAME|,|USER_EMAIL|,|PRODUCT_NAME|,|PRODUCT_URL|,|PRODUCT_SKU|,|PRODUCT_TYPE| ****All the product related tags are not available for multiple enquiry.', 'woocommerce-catalog-enquiry'),
                    'label'     => __( 'Custom Email Subject', 'woocommerce-catalog-enquiry' ),
                    'database_value' => '',
                ],    
            ]
        );
        
        $general_extra_data = [
        [
            'key'    => 'is_enable_add_to_cart',
            'label'   => __( 'Enable Add to cart?', 'multivendorx' ),
            'class'     => 'mvx-toggle-checkbox',
            'type'    => 'checkbox',
            'options' => array(
                array(
                    'key'=> "is_enable_add_to_cart",
                    'label'=> __("Enable this if you want add to cart button  along with enquiry button throughout the shop.", 'multivendorx'),
                    'value'=> "is_enable_add_to_cart"
                )
            ),
            'database_value' => array(),
        ],
        [
            'key'    => 'is_enable_quantity_cart',
            'label'   => __( 'Allow Quantity in Enquiry cart?', 'multivendorx' ),
            'class'     => 'mvx-toggle-checkbox',
            'type'    => 'checkbox',
            'options' => array(
                array(
                    'key'=> "is_enable_quantity_cart",
                    'label'=> __("Enable this if you want to allow quantity in enquiry cart.", 'multivendorx'),
                    'value'=> "is_enable_quantity_cart"
                )
            ),
            'database_value' => array(),
        ],
        [
            'key'    => 'is_remove_price',
            'label'   => __( 'Remove Price? (This disables Add to cart feature)', 'multivendorx' ),
            'class'     => 'mvx-toggle-checkbox',
            'type'    => 'checkbox',
            'options' => array(
                array(
                    'key'=> "is_remove_price",
                    'label'=> __("Enable this checkbox to hide product price and disable purchase.", 'multivendorx'),
                    'value'=> "is_remove_price"
                )
            ),
            'database_value' => array(),
        ],
        [
            'key'    => 'is_replace_price_with_txt',
            'label'   => __( 'Replace Price with Text?', 'multivendorx' ),
            'class'     => 'mvx-toggle-checkbox',
            'type'    => 'checkbox',
            'options' => array(
                array(
                    'key'=> "is_replace_price_with_txt",
                    'label'=> __("Just Checked this checkbox to replace the price with text. ** Please uncheck Remove Price, if you already checked it.", 'multivendorx'),
                    'value'=> "is_replace_price_with_txt"
                )
            ),
            'database_value' => array(),
        ],
        [
            'key'       => 'replace_text_in_price',
            'type'      => 'number',
            'class'     =>  'mvx-setting-wpeditor-class',
            'desc'      => __('This text will be displayed at the place of the product.', 'woocommerce-catalog-enquiry'),
            'label'     => __( 'Alternative Text at Price', 'woocommerce-catalog-enquiry' ),
            'database_value' => '',
        ],
        [
            'key'       => 'product_enquiry_position',
            'type'      => 'select',
            'bydefault' =>  '0',
            'label'     => __( 'Enquiry Button Postion', 'multivendorx' ),
            'desc'      => __( 'Change Enquiry button position.', 'multivendorx' ),
            'options' => array(
                array(
                    'key'=> "0",
                    'label'=> __('Please Select', 'multivendorx'),
                    'value'=> '0',
                ),
                array(
                    'key'=> "5",
                    'label'=> __('After Product Title', 'multivendorx'),
                    'value'=> '5',
                ),
                array(
                    'key'=> "15",
                    'label'=> __('Before Product Exerpt', 'multivendorx'),
                    'value'=> '15',
                ),
                array(
                    'key'=> "20",
                    'label'=> __('After Product Exerpt', 'multivendorx'),
                    'value'=> '20',
                ),
                array(
                    'key'=> "50",
                    'label'=> __('After Product Summary', 'multivendorx'),
                    'value'=> '50',
                ),
                array(
                    'key'=> "1",
                    'label'=> __('Custom Position', 'multivendorx'),
                    'value'=> '1',
                ),
            ),
            'database_value' => '',
        ],
        [
            'key'       => 'custom_enquiry_position',
            'type'      => 'number',
            'class'     =>  'mvx-setting-wpeditor-class',
            'desc'      => __('Enter custom priority value for Ex. 10.', 'woocommerce-catalog-enquiry'),
            'label'     => __( 'Custom Priority for Enquiry Button', 'woocommerce-catalog-enquiry' ),
            'database_value' => '',
        ] ];

        array_splice($args['general']['modulename'], 9, 0, $general_extra_data);

        $display_option_extra_data = [
            [
                'key'    => 'is_enable_multiple_product_enquiry',
                'label'   => __( 'Enable Multiple Product Enquiry', 'multivendorx' ),
                'class'     => 'mvx-toggle-checkbox',
                'type'    => 'checkbox',
                'options' => array(
                    array(
                        'key'=> "is_enable_multiple_product_enquiry",
                        'label'=> __( "Enable this checkbox to allow multiple product enquiry via enquiry cart. Also multiple enquiry product displays on the cart ", 'woocommerce-catalog-enquiry-pro' ) . __("<a href=".admin_url('widgets.php')." terget='_blank'>". __('widget', 'woocommerce-catalog-enquiry-pro') ."</a>", 'woocommerce-catalog-enquiry-pro'),
                        'value'=> "is_enable_multiple_product_enquiry"
                    )
                ),
                'database_value' => array(),
            ]
        ];
        array_splice($args['general']['modulename'], 18, 0, $display_option_extra_data);

        $enquiry_email_extra_data = [
            [
                'key'    => 'is_customer_receive_mail',
                'label'   => __( 'Send Copy To Customer', 'multivendorx' ),
                'class'     => 'mvx-toggle-checkbox',
                'type'    => 'checkbox',
                'options' => array(
                    array(
                        'key'=> "is_customer_receive_mail",
                        'label'=> __( "Enable this to send the enquiry mail copy to customer.", 'woocommerce-catalog-enquiry-pro'),
                        'value'=> "is_customer_receive_mail"
                    )
                ),
                'database_value' => array(),
            ],
            [
                'key'    => 'admin_receive_vendor_enquiry',
                'label'   => __( 'Enable vendor Enquiry Email Copy To Admin', 'multivendorx' ),
                'class'     => 'mvx-toggle-checkbox',
                'type'    => 'checkbox',
                'options' => array(
                    array(
                        'key'=> "admin_receive_vendor_enquiry",
                        'label'=> __( "If enabled, admin will receive a vendor enquiry mail copy.", 'woocommerce-catalog-enquiry-pro'),
                        'value'=> "admin_receive_vendor_enquiry"
                    )
                ),
                'database_value' => array(),
            ]
        ];
        array_splice($args['general']['modulename'], 23, 0, $enquiry_email_extra_data);

        $button_extra_data = [
            [
                'key'    => 'can_vendor_customize_btn_style',
                'label'   => __( 'Allow Vendor To Customize Button', 'multivendorx' ),
                'class'     => 'mvx-toggle-checkbox',
                'type'    => 'checkbox',
                'options' => array(
                    array(
                        'key'=> "can_vendor_customize_btn_style",
                        'label'=> __( "If enabled, Vendor can customize button style.", 'woocommerce-catalog-enquiry-pro'),
                        'value'=> "can_vendor_customize_btn_style"
                    )
                ),
                'database_value' => array(),
            ],
            [
                'key'    => 'hide_pro_added_enq_cart_msg',
                'label'   => __( 'Hide Enquiry Cart message', 'multivendorx' ),
                'class'     => 'mvx-toggle-checkbox',
                'type'    => 'checkbox',
                'options' => array(
                    array(
                        'key'=> "hide_pro_added_enq_cart_msg",
                        'label'=> __( "Hide cart message after product added to enquiry cart.", 'woocommerce-catalog-enquiry-pro'),
                        'value'=> "hide_pro_added_enq_cart_msg"
                    )
                ),
                'database_value' => array(),
            ],
        ];

        array_splice($args['button-appearance']['modulename'], 3, 0, $button_extra_data);

        $button_additional_data = [
            [
                'key'       => 'separator6_content',
                'type'      => 'section',
                'label'     => "",
            ],
            [
                'key'       =>  'woocommerce_catalog_multiple_button_enquiry',
                'type'      =>  'blocktext',
                'label'     =>  'no_label',
                'blocktext'      =>  __( "Enquiry Cart - Multiple Product Enquiry Button Customizer", 'woocommerce-catalog-enquiry' ),
                'database_value' => '',
            ],
            [
                'key'       => 'enquiry_cart_button_text',
                'type'      => 'number',
                'class'     =>  'mvx-setting-wpeditor-class',
                'label'     => __( 'Multiple Enquiry Cart Button Text', 'woocommerce-catalog-enquiry' ),
                'database_value' => '',
            ],
            [
                'key'       => 'view_enquiry_cart_button_text',
                'type'      => 'number',
                'class'     =>  'mvx-setting-wpeditor-class',
                'label'     => __( 'Multiple View Enquiry Cart Message Text', 'woocommerce-catalog-enquiry' ),
                'database_value' => '',
            ]
        ];

        array_splice($args['button-appearance']['modulename'], 7, 0, $button_additional_data);
        $args['live-preview'] = $live_preview;
        return $args;
    }
    
    /**
     * initilize plugin on WP init
     */

    function iconic_account_menu_items( $items ) {
        unset( $items['customer-logout'] );
        $items[ 'enquiry' ] = __( 'Enquiry', 'woocommerce-catalog-enquiry-pro' );
        $items[ 'customer-logout' ] = __( 'Log out', 'woocommerce' );
        return $items;
    }

    function iconic_add_my_account_endpoint1() {
        add_rewrite_endpoint( 'enquiry', EP_ALL );
    }

    public function iconic_information_endpoint_content1() {
        global $Woocommerce_Catalog_Enquiry_Pro;
        $Woocommerce_Catalog_Enquiry_Pro->nocache();
        $suffix = defined( 'WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG' ) && WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style('mvx_catalog_frontend_css',  $Woocommerce_Catalog_Enquiry_Pro->plugin_url.'assets/frontend/css/mvx-catalog-frontend' . $suffix . '.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);

        // Find each customer enquiry product and title
        $product_titles = array();
        $enquiry_titles = array();
        foreach (get_customer_enquiry_details(get_current_user_id()) as $key_title => $value_title) {
            $product_id = get_post_meta( $value_title, '_enquiry_product', true );
            $product_titles[$product_id] = get_the_title($product_id);
            $enquiry_titles[$value_title] = get_the_title($value_title);
        }

        $form_data_product = array_unique($product_titles);
        $enquiry_titles = array_unique($enquiry_titles);

        /**
        ** Customer first enquiry details
        **/
        $customer_product_enquiry = get_customer_enquiry_details(get_current_user_id(), '', $_POST);
        $enquiry_ids = array();
        $enquiry_product = array();
        $first_enquiry_details = '';
        foreach ($customer_product_enquiry as $key_enquiry => $value_enquiry) {
            $product_id = get_post_meta( $value_enquiry ,'_enquiry_product', true );
            $product = wc_get_product( $product_id );
            if (!$product) continue;
            $enquiry_ids[] = $value_enquiry;
            $enquiry_product[] = $product_id;
        }
        if (is_array($enquiry_ids) && !empty($enquiry_ids)) {
            $user_details = get_user_by( 'ID' , get_post_field ('post_author', $enquiry_ids[0]) );
            $to_user_id = $user_details ? $user_details->data->ID : 0;

            $first_enquiry_details = array($enquiry_ids[0], $enquiry_product[0], $to_user_id);
        }

        wp_enqueue_script('mvx_catalog_frontend_js', $Woocommerce_Catalog_Enquiry_Pro->plugin_url.'assets/frontend/js/mvx-catalog-frontend' . $suffix . '.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
            wp_localize_script(
                'mvx_catalog_frontend_js', 
                'mvx_catalog', 
            apply_filters( 'wc_enquiry_customer_chat_script', array(
                'ajaxurl'           => admin_url('admin-ajax.php'), 
                'reply_sent'        => __('Reply sent successfully!','woocommerce-catalog-enquiry-pro'),    
                'something_wrong'       => __('Somethings wrong with your mail!','woocommerce-catalog-enquiry-pro'),
                'empty_text'       => __('You have to write something','woocommerce-catalog-enquiry-pro'),   
                'send_button' => __('Send','woocommerce-catalog-enquiry-pro'),
                'wait_msg' => __('Please wait...','woocommerce-catalog-enquiry-pro'),
                'scroll_limit' => 5000,
                'form_data_product' => $form_data_product,
                'enquiry_titles' => $enquiry_titles,
                'first_enquiry_details' => $first_enquiry_details,
                'type_text' => __('Start Typing','woocommerce-catalog-enquiry-pro'),
                
            )));
        $args   = array(
            'current_user_id'   => get_current_user_id(),
            'customer_details_header'   => ''
        );
        $args['args'] = $args;

        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-customer-reply-template.php',$args);
    }

    function init() {

        // Init custom post and pages
        $this->init_enquiry_cart_page();

        $this->create_table_chat_msg();

        // Intialize MVX Catalog Widgets
        $this->init_custom_widgets();

        // Init library
        $this->load_class('library');
        $this->library = new Woocommerce_Catalog_Enquiry_Pro_Library();

        // Init ajax
        $this->load_class('ajax');
        $this->ajax = new  Woocommerce_Catalog_Enquiry_Pro_Ajax();

        if (is_admin()) {
            $this->load_class('admin');
            $this->admin = new Woocommerce_Catalog_Enquiry_Pro_Admin();
        }

        if (!is_admin() || defined('DOING_AJAX')) {
            $this->load_class('frontend');
            $this->frontend = new Woocommerce_Catalog_Enquiry_Pro_Frontend();
        }

        // Init shortcode
        $this->load_class( 'shortcode' );
        $this->shortcode = new Woocommerce_Catalog_Enquiry_Pro_Shortcode();

        // init templates
        $this->load_class('template');
        $this->template = new Woocommerce_Catalog_Enquiry_Pro_Template();

        // catalog session
        $this->register_session_for_woocommerce_catalog_catalog();

        do_action('woocommerce_catalog_init');

    }
    
    /**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'woocommerce-catalog-enquiry-pro');
        load_textdomain('woocommerce-catalog-enquiry-pro', WP_LANG_DIR . '/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-' . $locale . '.mo');
        load_plugin_textdomain('woocommerce-catalog-enquiry-pro', false, plugin_basename(dirname(dirname(__FILE__))) . '/languages');
    }

    public function init_catalog_cron_job() {
        add_filter('cron_schedules', array($this, 'add_mvx_corn_schedule_catalog'));
        $this->load_class('cron-job');
        $this->cron_job = new Woocommerce_Catalog_Enquiry_Pro_Cron_Job();
    }

    /**
     * Add MVX weekly and monthly corn schedule
     *
     * @access public
     * @param schedules array
     * @return schedules array
     */
    function add_mvx_corn_schedule_catalog($schedules) {
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __('Every 7 Days', $this->text_domain)
        );
        $schedules['monthly'] = array(
            'interval' => 2592000,
            'display' => __('Every 1 Month', $this->text_domain)
        );
        $schedules['fortnightly'] = array(
            'interval' => 1296000,
            'display' => __('Every 15 Days', $this->text_domain)
        );
        $schedules['every_5minute'] = array(
                'interval' => 5*60, // in seconds
                'display'  => __( 'Every 5 minute', $this->text_domain )
        );
        
        return $schedules;
    }

    public function load_class($class_name = '') {
        if ('' != $class_name && '' != $this->token) {
            require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
        } // End If Statement
    }// End load_class()


    /*** Create table for chat message ****/
    function create_table_chat_msg() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "catelog_cust_vendor_answers` (
        `chat_message_id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `to_user_id` int(11) NOT NULL ,
        `from_user_id` int(11) NOT NULL ,
        `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `chat_message` text NOT NULL,
        `product_id` int(11) NOT NULL ,
        `enquiry_id` int(1) NOT NULL,
        `status` text NOT NULL,
        PRIMARY KEY  (chat_message_id)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $wpdb->query($sql);
    }


    function init_enquiry_cart_page() {
        global $wpdb,$Woocommerce_Catalog_Enquiry_Pro;
        $option_value = get_option( 'woocommerce_catalog_enq_cart_page_id' );
        if ( $option_value > 0 && get_post( $option_value ) )
            return;

        $page_found = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'woocommerce_catalog_enquiry_cart' LIMIT 1;");
        if ( $page_found ) :
            if ( ! $option_value )
                update_option( 'woocommerce_catalog_enq_cart_page_id', $page_found );
            return;
        endif;

        $page_data = array(
            'post_status'       => 'publish',
            'post_type'         => 'page',
            'post_author'       => 1,
            'post_name'         => esc_sql( _x( 'woocommerce_catalog_enquiry_cart', 'page_slug', 'woocommerce-catalog-enquiry-pro' ) ),
            'post_title'        => __( 'Enquiry Cart', 'woocommerce-catalog-enquiry-pro' ),
            'post_content'      => '[woocommerce_catalog_enquiry_cart]',
            'post_parent'       => 0,
            'comment_status'    => 'closed'
        );
        $page_id = wp_insert_post( $page_data );

        update_option( 'woocommerce_catalog_enq_cart_page_id', $page_id );
    }

    /**
     * Init MVX vendor widgets.
     *
     * @access public
     * @return void
     */
    function init_custom_widgets() {
        $this->load_class('widget');
        new Woocommerce_Catalog_Enquiry_Pro_Widget();
    }

    
    /** Cache Helpers *********************************************************/

    function mvx_init() {
        add_filter( 'mvx_endpoints_query_vars', array($this, 'add_mvx_catalog_menu_page_endpoint'),10);
        // migrate data
        $this->do_migrate_vendor_catalog_settings();
    }

    public function add_mvx_catalog_menu_page_endpoint($endpoint) {
        $endpoint['catalog-details'] = array(
            'label' => __('Enquiry Details', 'woocommerce-catalog-enquiry-pro'),
            'endpoint' => get_mvx_vendor_settings('mvx_catalog_details_endpoint', 'seller_dashbaord', 'catalog-details')
        );
        $endpoint['catalog-settings'] = array(
            'label' => __('Enquiry Settings', 'woocommerce-catalog-enquiry-pro'),
            'endpoint' => get_mvx_vendor_settings('mvx_catalog_settings_endpoint', 'seller_dashbaord', 'catalog-settings')
        );
        if (!get_option('mvx_catalog_endpoints')) {
                flush_rewrite_rules();
                update_option('mvx_catalog_endpoints', 1);
        }

        return $endpoint;
    }

    /**
       * Install upon activation.
       *
       * @access public
       * @return void
       */
    static function activate_mvx_Woocommerce_Catalog_Enquiry() {
        if (is_active_MVX()) {
            // capabilities
            $capabilities_settings = get_option('mvx_capabilities_product_settings_name');
            if (is_array($capabilities_settings)) {
                    $capabilities_settings['can_manage_catalog_settings'] = 'Enable';
                    $capabilities_settings['can_manage_catalog_details'] = 'Enable';
            }
            update_option('mvx_capabilities_product_settings_name', $capabilities_settings);
        }
    }

    function set_catalog_enquiry_capabilities($settings_tab_options) {
        global $Woocommerce_Catalog_Enquiry_Pro;
        $settings_tab_options["sections"]["mvx_woocommerce_catalog_enquiry"] = array( 
                "title" =>  __('Woocommerce Catalog Enquiry', 'woocommerce-catalog-enquiry-pro'),
                "ref" => &$this,
                "fields" => array( 
                    "can_manage_catalog_settings" => array('title' => __('Can Manage Catalog Settings', 'woocommerce-catalog-enquiry-pro'), 'type' => 'checkbox', 'id' => 'can_manage_catalog_settings', 'label_for' => 'can_manage_catalog_settings', 'desc' => __('Allow vendors to manage their own catalog enquiry settings.', 'woocommerce-catalog-enquiry-pro'), 'name' => 'can_manage_catalog_settings', 'value' => 'Enable'),
                    "can_manage_catalog_details" => array('title' => __('Can Manage Catalog Details', 'woocommerce-catalog-enquiry-pro'), 'type' => 'checkbox', 'id' => 'can_manage_catalog_details', 'label_for' => 'can_manage_catalog_details', 'name' => 'can_manage_catalog_details', 'desc' => __('Allow vendors to manage catalog enquiry details.', 'woocommerce-catalog-enquiry-pro'), 'value' => 'Enable'), // Checkbox
                )
        );
        return $settings_tab_options;
    }

    function save_catalog_enquiry_capabilities($new_input, $input) {
        global $Woocommerce_Catalog_Enquiry_Pro;
        
        if (isset($input['can_manage_catalog_settings']))
            $new_input['can_manage_catalog_settings'] = sanitize_text_field($input['can_manage_catalog_settings']);
        if (isset($input['can_manage_catalog_details']))
            $new_input['can_manage_catalog_details'] = sanitize_text_field($input['can_manage_catalog_details']);
        return $new_input;
    }

    /**
    * Migrate vendor catalog capabilities settings if mvx version less than 2.6+
    */ 
    function do_migrate_vendor_catalog_settings() {
        global $MVX;

        if (get_mvx_vendor_settings('can_manage_catalog_settings', 'capabilities') && get_mvx_vendor_settings('can_manage_catalog_settings', 'capabilities') == 'Enable') {
            update_mvx_vendor_settings('can_manage_catalog_settings', 'Enable', 'capabilities','product');
        }
        delete_mvx_vendor_settings('can_manage_catalog_settings', 'capabilities');
        if (get_mvx_vendor_settings('can_manage_catalog_details', 'capabilities') && get_mvx_vendor_settings('can_manage_catalog_details', 'capabilities') == 'Enable') {
            update_mvx_vendor_settings('can_manage_catalog_details', 'Enable', 'capabilities','product');
        }
        delete_mvx_vendor_settings('can_manage_catalog_details', 'capabilities');
        
    }
      
    /**
     * UnInstall upon deactivation.
     *
     * @access public
     * @return void
     */
    static function deactivate_mvx_Woocommerce_Catalog_Enquiry() {
        // delete catalog endpoints
        if (get_option('mvx_catalog_endpoints')) {
                delete_option('mvx_catalog_endpoints');
        }
    }

    /**
     * Add WCCE Email Class
     *
     */ 
    function woocommerce_catalog_enquiry_email_callback( $emails ) {
        require_once( 'emails/class-woocommerce-catalog-enquiry-pro-send-email.php' );
        $emails['Woocommerce_Catalog_Enquiry_Pro_Send_Email'] = new Woocommerce_Catalog_Enquiry_Pro_Send_Email();
        require_once( 'emails/class-woocommerce-catalog-enquiry-pro-reply-email.php' );
        $emails['Woocommerce_Catalog_Enquiry_Pro_Reply_Email'] = new Woocommerce_Catalog_Enquiry_Pro_Reply_Email();

        return $emails;
    }

    function register_session_for_woocommerce_catalog_catalog() {
        if ( !is_admin() ) {
            if (!session_id()) {
                session_start();
            } 
        }
    }
    

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     *
     * @access public
     * @return void
     */
    function nocache() {
        if (!defined('DONOTCACHEPAGE'))
            define("DONOTCACHEPAGE", "true");
        // WP Super Cache constant
    }

}