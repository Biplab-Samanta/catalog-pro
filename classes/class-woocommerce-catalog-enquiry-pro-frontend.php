<?php
class Woocommerce_Catalog_Enquiry_Pro_Frontend {
    
    public $available_for;
    private $enquiry_form_field_types;

    public function __construct() {
        global $Woocommerce_Catalog_Enquiry_Pro, $post;
        $settings = get_woocommerce_catalog_catalog_settings();
        //enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array($this, 'frontend_styles'));

        add_filter( 'woocommerce_catalog_enquiry_localize_script_data', array($this, 'woocommerce_catalog_enquiry_localize_script_data' ));
        
        add_filter( 'woocommerce_redirect_to_home_url', array($this, 'catalog_enquiry_custom_link_for_disable_cart' ));

        if (isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable") {
            // MVX
            if (is_active_MVX()) {
                add_filter( 'mvx_vendor_dashboard_nav', array($this, 'add_mvx_catalog_menu_in_vendor_dash'),10);
                add_action('mvx_vendor_dashboard_catalog-details_endpoint', array($this, 'mvx_vendor_dashboard_catalog_details_endpoint'));
                add_action('mvx_vendor_dashboard_catalog-settings_endpoint', array($this, 'mvx_vendor_dashboard_catalog_settings_endpoint'));
                add_action('mvx_before_vendor_dashboard', array($this, 'save_mvx_vendor_catalog_data'));
                //User Avatar override
                add_filter( 'get_avatar', array( &$this, 'woocommerce_catalog_user_avatar_override' ), 10, 6 );
            }
        }
        // add enquiry success message wrapper
        add_action('woocommerce_before_main_content', array($this, 'woocommerce_catalog_enquiry_success_wrapper'));
        add_action('woocommerce_catalog_before_enquiry_cart', array($this, 'woocommerce_catalog_enquiry_success_wrapper'));
        $current_user = wp_get_current_user();  
        $user_id = $current_user->ID;       
        $this->available_for = '';
        // Exclution as per username
        if ( isset($settings['woocommerce_user_list']) && mvx_catalog_get_settings_value($settings['woocommerce_user_list'], 'multiselect') && is_array(mvx_catalog_get_settings_value($settings['woocommerce_user_list'], 'multiselect')) ) {
            if (in_array($current_user->ID, mvx_catalog_get_settings_value($settings['woocommerce_user_list'], 'multiselect'))) {
                $this->available_for = $current_user->ID;                           
            }                                   
        }

        // Exclution for user role
        if (isset($settings['woocommerce_userroles_list']) && mvx_catalog_get_settings_value($settings['woocommerce_userroles_list'], 'multiselect')) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_userroles_list'], 'multiselect'))) {
                $user_role_list = array();
                
                foreach (mvx_catalog_get_settings_value($settings['woocommerce_userroles_list'], 'multiselect') as $user_list_key) {
                    $user_role_list[] = array_key_exists( $user_list_key, array_keys( wp_roles()->roles ) ) ? array_keys( wp_roles()->roles )[$user_list_key] : '';
                }

                if ( !empty( $current_user->roles ) && in_array($current_user->roles[0], $user_role_list)) {
                    $this->available_for = $current_user->ID;
                }
            }
        }

        $for_user_type = isset($settings['for_user_type']) ? mvx_catalog_get_settings_value($settings['for_user_type'], 'select') : '';
        if ($for_user_type == 0 || $for_user_type == 3 || $for_user_type == '' ) {
            $this->init_catalog();  
        } else if ($for_user_type == 1) {
            if ($current_user->ID == 0) {
                $this->init_catalog();  
            }
        } else if ($for_user_type == 2) {
            if ($current_user->ID != 0) {
                $this->init_catalog();  
            }           
        }
        if (isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable") {                      
            if (isset($settings['button_type']) && (mvx_catalog_get_settings_value($settings['button_type'], 'select') == 2 || mvx_catalog_get_settings_value($settings['button_type'], 'select') == 3)) {
                add_filter('the_permalink', array($this, 'change_permalink'),10);
            }
        }

        add_action( 'wc_catalog_enquiry_send_mail', array($this,'wc_catalog_enquiry_send_mail') , 10 , 3 ); 
    }
    public function wc_catalog_enquiry_send_mail( $data_enquiry,$vendor_id,$chat_message ) {
        $user_name_non_log_in = get_post_meta( $data_enquiry, '_enquiry_username', true );
        $user_email_non_log_in = get_post_meta( $data_enquiry, '_enquiry_useremail', true );
        $user_details = get_user_by( 'ID' , get_post_field ('post_author', $data_enquiry) );

        $user_name = $user_details ? $user_details->data->user_login : $user_name_non_log_in;
        $user_mail = $user_details ? $user_details->user_email : $user_email_non_log_in;
        $enquiry_action_type = get_post_meta( $data_enquiry , '_enquiry_action_type', true );
        $user_details = get_user_by( 'id' , $vendor_id );
        $from_id = $user_details->data->user_login;

        $subject_mail = __('Enquiry Response','woocommerce-catalog-enquiry-pro');
        
        $enquiry_data = apply_filters( 'woocommerce_catalog_before_enquiry_reply_email_data', array(
                'enquiry_id' => $data_enquiry,
                'user_name' => $user_name,
                'vendor_name' => $from_id,
                'user_email' => $user_mail,
                'subject_mail' => sanitize_text_field($subject_mail),
                'enquiry_action_type' => $enquiry_action_type,
                'body_mail' => 'Reply Massage :'. $chat_message . '.',
                ));
        $reply_email = WC()->mailer()->emails['Woocommerce_Catalog_Enquiry_Pro_Reply_Email'];
        $reply_email->trigger( $enquiry_data );
    }
    
    public function change_permalink() {
        global $product, $Woocommerce_Catalog_Enquiry_Pro, $post;
        $settings = get_woocommerce_catalog_catalog_settings();         
        if (!$product) {
            return get_permalink($post->ID);
        } else {
            if (isset($settings['button_type']) && mvx_catalog_get_settings_value($settings['button_type'], 'select') == 2) {
                $link = isset($settings['button_type']) ? $settings['button_link'] : '';
                return $link;
            } else if (isset($settings['button_type']) && mvx_catalog_get_settings_value($settings['button_type'], 'select') == 3 ) {
                $link = get_post_field("woo_catalog_enquiry_product_link",$post->ID);
                return $link;               
            } else {
                return get_permalink($post->ID);                
            }
        }
    }
    
    public function init_catalog() {
        global $Woocommerce_Catalog_Enquiry_Pro;
        $settings = get_woocommerce_catalog_catalog_settings(); 
        if (isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable" && ($this->available_for == '' ||  $this->available_for == 0)) {    
            add_action('wp_head',array($this,'remove_add_to_cart_button')); 
            add_action('wcce_enquiry_form_fields', array(&$this, 'wcce_enquiry_form_fields_callback'));
            if (isset($settings['is_enable_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_enquiry'], 'checkbox') == "Enable" ) {
                $priority = 100;
                if (isset($settings['product_enquiry_position']) && $settings['product_enquiry_position'] != 0 && $settings['product_enquiry_position'] != 1 ) {
                    $priority = $settings['product_enquiry_position'];
                } else if (isset($settings['product_enquiry_position']) && mvx_catalog_get_settings_value($settings['product_enquiry_position'], 'select') == 1 ) {
                    if (isset($settings['custom_enquiry_position']) && $settings['custom_enquiry_position'] != '') {
                        $priority = $settings['custom_enquiry_position'];
                    } else {
                        $priority = 100;
                    }
                }
                if (isset($settings['is_disable_popup']) && mvx_catalog_get_settings_value($settings['is_disable_popup'], 'checkbox') == "Enable" ) {
                    add_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry_without_popup'),$priority);
                } else {
                    add_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry'), $priority);   
                }
            }                       
            if (isset($settings['is_remove_price']) && mvx_catalog_get_settings_value($settings['is_remove_price'], 'checkbox') == "Enable") {
                add_action('init',array($this,'remove_price_from_product_list_loop'),10);
                add_action('woocommerce_single_product_summary',array($this,'remove_price_from_product_list_single'),5);    
            }
            // Alternative text @ price place
            if (isset($settings['is_replace_price_with_txt']) && mvx_catalog_get_settings_value($settings['is_replace_price_with_txt'], 'checkbox') == "Enable") {
                if (isset($settings['replace_text_in_price']) && !empty($settings['replace_text_in_price']))    {
                    add_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );
                    add_filter( 'woocommerce_cart_item_price', array($this, 'alternative_text_at_price_hook_callback') );
                }
            }
            add_action('woocommerce_after_shop_loop_item_title' , array ($this, 'price_for_selected_product'),5);
            add_action('woocommerce_after_shop_loop_item' , array ($this, 'add_to_cart_button_for_selected_product'),5);
            add_action('woocommerce_before_shop_loop_item', array ($this, 'change_permalink_url_for_selected_product'),5);
            add_action( 'woocommerce_single_product_summary', array($this, 'catalog_woocommerce_template_single'), 5 );
            // mini enquiry cart
            add_action( 'woocommerce_catalog_mini_enquiry_cart_buttons', array($this, 'woocommerce_catalog_mini_enquiry_cart_view_button'), 10 );
            // Quantity in enquiry cart
            if (isset($settings['is_enable_quantity_cart']) && mvx_catalog_get_settings_value($settings['is_enable_quantity_cart'], 'checkbox') == "Enable") {
                add_filter( 'woocommerce_catalog_enquiry_cart_show_quantity', '__return_true' ); 
            }
            // Enquiry cart message
            if (isset($settings['hide_pro_added_enq_cart_msg']) && mvx_catalog_get_settings_value($settings['hide_pro_added_enq_cart_msg'], 'checkbox') == "Enable") {
                add_filter( 'woocommerce_catalog_show_added_to_enquiry_cart_message', '__return_false' ); 
            }
        }       
    }

    public function woocommerce_catalog_mini_enquiry_cart_view_button() {
        global $Woocommerce_Catalog_Enquiry_Pro, $woocommerce, $Woocommerce_Catalog_Enquiry_Pro_Cart;
        $settings = get_woocommerce_catalog_catalog_settings();
        $view_enq_cart_btn_text = __('View Enquiry Cart','woocommerce-catalog-enquiry-pro');
        if (isset($settings['view_enquiry_cart_button_text']) && !empty($settings['view_enquiry_cart_button_text']))
            $view_enq_cart_btn_text = $settings['view_enquiry_cart_button_text'];
        $btn_style = '';
        if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {
            $btn_style = ' custom_enquiry_buttons_css_new'; 
        }
        echo '<a href="' . esc_url( $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_cart_page_url() ) . '" class="'.$btn_style.' button wc-forward">' . $view_enq_cart_btn_text . '</a>';
    }

    public function change_permalink_url_for_selected_product() {
        global $Woocommerce_Catalog_Enquiry_Pro, $post, $product, $Woocommerce_Catalog_Enquiry;
        $settings = get_woocommerce_catalog_catalog_settings();
        $product_for = $category_for = '';
        if ( isset($settings['woocommerce_product_list']) && mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect')) && isset($post->ID)) {
                if (in_array($post->ID,mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect'))) {
                    $product_for = $post->ID;                           
                } else {
                    $product_for = '';
                }
            }                       
        }

        if (isset($settings['woocommerce_category_list']) && mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect')) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) {
                if (isset($product)) {
                    $term_list = wp_get_post_terms($post->ID, 'product_cat', array('fields' => 'ids'));

                    if (count(array_intersect($term_list, mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) > 0) {
                        $category_for = $post->ID;
                    } else {
                        $category_for = '';
                    }
                } else {
                    $category_for = '';
                }
            } else {
                $category_for = '';
            }
        } else {
            $category_for = '';
        }                   
        
        if ($product_for == $post->ID || $category_for == $post->ID) {
            remove_filter('the_permalink', array($this, 'change_permalink'),10);
            remove_filter('woocommerce_loop_add_to_cart_link', array($Woocommerce_Catalog_Enquiry->frontend, 'woocommerce_loop_add_to_cart_link'), 99, 3);            
        } else {
            if ($post->post_type == 'product') {
                if($settings['button_type']){
                    add_filter('woocommerce_loop_add_to_cart_link', array($Woocommerce_Catalog_Enquiry->frontend, 'woocommerce_loop_add_to_cart_link'), 99, 3);
                }
                add_filter('the_permalink', array($this, 'change_permalink'),10);
            }
        }
    }
    
    public function catalog_woocommerce_template_single() { 
        global $Woocommerce_Catalog_Enquiry_Pro, $post, $product;
        $settings = get_woocommerce_catalog_catalog_settings();
        $priority = 100;
        if (isset($settings['product_enquiry_position']) && $settings['product_enquiry_position'] != 0 && $settings['product_enquiry_position'] != 1 ) {
            $priority = $settings['product_enquiry_position'];
        } else if ( isset($settings['product_enquiry_position']) && mvx_catalog_get_settings_value($settings['product_enquiry_position'], 'select') == 1 ) {
            if (isset($settings['custom_enquiry_position']) && $settings['custom_enquiry_position'] != '') {
                $priority = $settings['custom_enquiry_position'];
            } else {
                $priority = 100;
            }
        }
        // MVX
        $is_vendor_product = false;
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                }
            }
        }
        
        $product_for = '';
        
        if ( isset($settings['woocommerce_product_list']) && mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect')) && isset($post->ID)) {
                if (in_array($post->ID,mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect'))) {
                    $product_for = $post->ID;                           
                } else { $product_for = ''; }                   
            } else { $product_for = ''; }               
        } else { $product_for = ''; }           

        $category_for = '';
            
        if ( isset($settings['woocommerce_category_list']) && mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) {                 
                if (isset($product)) {
                    $term_list = wp_get_post_terms($post->ID,'product_cat',array('fields'=>'ids'));
                    if (count(array_intersect($term_list, mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) > 0) {
                        $category_for = $post->ID;
                    } else {$category_for = ''; }
                } else {  $category_for = ''; }                                     
            } else { $category_for = ''; }              
        } else { 
            $category_for = ''; 
        }   

        if ($product_for == $post->ID ||  $category_for == $post->ID) {     
            remove_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry'),$priority);
            remove_action('woocommerce_single_product_summary', array($this,'add_form_for_enquiry_without_popup'),$priority);
            add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
            add_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );           
            add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 ); 
            remove_action( 'woocommerce_single_product_summary', array($this,'add_variation_product'),29 );
            remove_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );
        } else {
            //for mvx vendor
            if (isset($settings['is_remove_price']) && mvx_catalog_get_settings_value($settings['is_remove_price'], 'checkbox') == "Enable") {              
                remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 ); 
                remove_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );
                remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 ); 
            } elseif (isset($settings['is_replace_price_with_txt']) && mvx_catalog_get_settings_value($settings['is_replace_price_with_txt'], 'checkbox') == "Enable") {
                if (isset($settings['replace_text_in_price']) && !empty($settings['replace_text_in_price']))    {
                    add_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );
                    add_filter( 'woocommerce_cart_item_price', array($this, 'alternative_text_at_price_hook_callback') );
                    remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 ); 
                }
            }
        }
    }
    
    
    public function add_form_for_enquiry_without_popup() {      
        global $Woocommerce_Catalog_Enquiry_Pro, $post, $product, $Woocommerce_Catalog_Enquiry_Pro_Cart;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        $vendor_css_class = '';
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                    $vendor_css_class = 'vendor_'.$product_author->id.'_custom_enquiry_buttons_css';
                }
            }
        }
        
        $button_text = __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
        if (isset($settings['is_button'])) {
            $button_text = isset($settings['enquiry_button_text']) ? $settings['enquiry_button_text'] : __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
            if (empty($button_text))
                    $button_text = __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
        }
        if (isset($settings['is_enable_out_of_stock']) && mvx_catalog_get_settings_value($settings['is_enable_out_of_stock'], 'checkbox') == "Enable" && $product->is_in_stock()) {return;}
        ?> 
        <style>
        <?php 
        if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable')
            echo str_replace("custom_enquiry_buttons_css",$vendor_css_class,$settings['custom_enquiry_buttons_css']);
        else
            echo $settings['custom_enquiry_buttons_css'];
        ?>
        </style>   
        <div id="woocommerce-catalog-pro" name="woo_catalog" >  
        <?php if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {?>
            <button class="woocommerce-catalog-send-enquiry <?php if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable') echo $vendor_css_class; else echo 'custom_enquiry_buttons_css_new'; ?>" href="#responsive"><?php echo $button_text;?></button>
        <?php } else {?>
            <button class="woocommerce-catalog-send-enquiry demo button btn btn-primary btn-large" style="margin-top:15px;" href="#responsive"><?php echo $button_text; ?></button>
        <?php } 
        ?>
        <?php 
        // Multiple Enquiry Cart Button
        if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
            $enq_cart_btn_text = __('Add to Enquiry Cart','woocommerce-catalog-enquiry-pro');
            if (isset($settings['enquiry_cart_button_text']) && !empty($settings['enquiry_cart_button_text']))
                $enq_cart_btn_text = $settings['enquiry_cart_button_text'];
            $view_enq_cart_btn_text = __('View Enquiry Cart','woocommerce-catalog-enquiry-pro');
            if (isset($settings['view_enquiry_cart_button_text']) && !empty($settings['view_enquiry_cart_button_text']))
                $view_enq_cart_btn_text = $settings['view_enquiry_cart_button_text'];
            $btn_style = '';
            if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {
                $btn_style = ' custom_enquiry_buttons_css'; 
                if ($is_vendor_product) $btn_style = ' '.$vendor_css_class;
            }
            $args         = array(
                'class'             => 'wcce-add-enquiry-cart-button button ',
                'btn_style'         => $btn_style,
                'wpnonce'           => wp_create_nonce( 'add-enquiry-cart-' . $product->get_id() ),
                'product_id'        => $product->get_id(),
                'label'             => apply_filters( 'woocommerce_catalog_enquiry_add_to_enquiry_cart_label' , $enq_cart_btn_text ),
                'label_view_cart'   => apply_filters( 'woocommerce_catalog_enquiry_product_added_view_list' , $view_enq_cart_btn_text ),
                'enquiry_cart_url'  => $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_cart_page_url(),
                'exists'            => $Woocommerce_Catalog_Enquiry_Pro_Cart->exists_enquiry( $product->get_id() )
            );
            $args['args'] = $args;
            $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-cart-button-template.php',$args);
        }
        // Enquiry Form
        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-form-template.php');
        ?>              
    </div>  
    <?php
    }
    
    public function add_form_for_enquiry() {        
        global $Woocommerce_Catalog_Enquiry_Pro, $post, $product, $Woocommerce_Catalog_Enquiry_Pro_Cart;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        $vendor_css_class = '';
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                }
            }
        }
        
        $button_text = __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
        if (isset($settings['is_button'])) {
            $button_text = isset($settings['enquiry_button_text']) ? $settings['enquiry_button_text'] : __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
            if (empty($button_text))
                $button_text = __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
        }
        if (isset($settings['is_enable_out_of_stock']) && mvx_catalog_get_settings_value($settings['is_enable_out_of_stock'], 'checkbox') == "Enable" && $product->is_in_stock()) {return;}
        ?>
        <style>
        <?php 
        if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable')
            echo str_replace("custom_enquiry_buttons_css",$vendor_css_class,$settings['custom_enquiry_buttons_css']);
        else
            echo isset($settings['custom_enquiry_buttons_css']) ? $settings['custom_enquiry_buttons_css'] : '';
        ?>
        </style>
        <div id="woocommerce-catalog-pro" name="woo_catalog" >
        <?php if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {?>
            <button class="woocommerce-catalog-send-enquiry <?php if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable') echo $vendor_css_class; else echo 'custom_enquiry_buttons_css_new'; ?>" href="#responsive"><?php echo $button_text;?></button>
        <?php } else {?>
            <button class="woocommerce-catalog-send-enquiry demo button btn btn-primary btn-large" style="margin-top:15px;" href="#responsive"><?php echo $button_text; ?></button>
        <?php }
        // Multiple Enquiry Cart Button
        if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
            $enq_cart_btn_text = __('Add to Enquiry Cart','woocommerce-catalog-enquiry-pro');
            if (isset($settings['enquiry_cart_button_text']) && !empty($settings['enquiry_cart_button_text']))
                $enq_cart_btn_text = $settings['enquiry_cart_button_text'];
            $view_enq_cart_btn_text = __('View Enquiry Cart','woocommerce-catalog-enquiry-pro');
            if (isset($settings['view_enquiry_cart_button_text']) && !empty($settings['view_enquiry_cart_button_text']))
                $view_enq_cart_btn_text = $settings['view_enquiry_cart_button_text'];
            $btn_style = '';
            if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {
                $btn_style = ' custom_enquiry_buttons_css_new'; 
                if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable') $btn_style = ' '.$vendor_css_class;
            }
            $args         = array(
                'class'             => 'wcce-add-enquiry-cart-button button ',
                'btn_style'             => $btn_style,
                'wpnonce'           => wp_create_nonce( 'add-enquiry-cart-' . $product->get_id() ),
                'product_id'        => $product->get_id(),
                'label'             => apply_filters( 'woocommerce_catalog_enquiry_add_to_enquiry_cart_label' , $enq_cart_btn_text ),
                'label_view_cart'   => apply_filters( 'woocommerce_catalog_enquiry_product_added_view_list' , $view_enq_cart_btn_text ),
                'enquiry_cart_url'  => $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_cart_page_url(),
                'exists'            => $Woocommerce_Catalog_Enquiry_Pro_Cart->exists_enquiry( $product->get_id() )
            );
            $args['args'] = $args;
            $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-cart-button-template.php',$args);
        }
        // Enquiry Form
        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-form-template.php');
        ?>
    </div>      
    <?php   
    }

    function wcce_enquiry_form_fields_callback() {
        global $Woocommerce_Catalog_Enquiry_Pro;
        $wcce_enquiry_form_data = mvx_get_option('mvx_catalog_pro_enquiry_form_data') ? mvx_get_option('mvx_catalog_pro_enquiry_form_data') : [];
        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-form-fileds.php', array('wcce_enquiry_form_data' => $wcce_enquiry_form_data));
    }
    
    public function price_for_selected_product() { 
        global $Woocommerce_Catalog_Enquiry_Pro, $post, $product;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                }
            }
        }
        
        $product_for = '';
        if ( isset($settings['woocommerce_product_list']) && mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect')) && isset($post->ID)) {
                if (in_array($post->ID,mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect'))) {
                    $product_for = $post->ID;                           
                } else {
                    $product_for = '';
                }
            }                       
        } 
                    
        $category_for = '';
        if ( isset($settings['woocommerce_category_list']) && mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) {                 
                if (isset($product)) {
                    $term_list = wp_get_post_terms($post->ID,'product_cat',array('fields'=>'ids'));
                    if (count(array_intersect($term_list, mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) > 0) {
                        $category_for = $post->ID;
                    } else { $category_for = ''; }
                } else {  $category_for = ''; }                                     
            } else { $category_for = ''; }              
        } else { $category_for = ''; }          

        if ($product_for == $post->ID || $category_for == $post->ID) {
            add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
            remove_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );         
        } else { 
            if (isset($settings['is_remove_price']) && mvx_catalog_get_settings_value($settings['is_remove_price'], 'checkbox') == "Enable") {              
                remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
                remove_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );
            } elseif (isset($settings['is_replace_price_with_txt']) && mvx_catalog_get_settings_value($settings['is_replace_price_with_txt'], 'checkbox') == "Enable") {
                if (isset($settings['replace_text_in_price']) && !empty($settings['replace_text_in_price']))    {
                    add_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') );
                    add_filter( 'woocommerce_cart_item_price', array($this, 'alternative_text_at_price_hook_callback') );
                }
            } else {
                add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
                remove_filter( 'woocommerce_get_price_html', array($this, 'alternative_text_at_price_hook_callback') ); 
            }
        }   
    }
    
    
    public function add_to_cart_button_for_selected_product() {
        global $Woocommerce_Catalog_Enquiry_Pro, $post, $product;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                }
            }
        }
        
        $product_for = '';

        if ( isset($settings['woocommerce_product_list']) && mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect')) && isset($post->ID)) {
                if (in_array($post->ID,mvx_catalog_get_settings_value($settings['woocommerce_product_list'], 'multiselect'))) {
                    $product_for = $post->ID;                           
                }
                else {
                    $product_for = '';
                }
            }                       
        }                   
        
        $category_for = '';
        if ( isset($settings['woocommerce_category_list']) && mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect') ) {
            if (is_array(mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) {                 
                if (isset($product)) {
                    $term_list = wp_get_post_terms($post->ID,'product_cat',array('fields'=>'ids'));
                    if (count(array_intersect($term_list, mvx_catalog_get_settings_value($settings['woocommerce_category_list'], 'multiselect'))) > 0) {
                        $category_for = $post->ID;
                    } else {$category_for = ''; }
                } else {  $category_for = ''; }                                     
            } else { $category_for = ''; }              
        } else { $category_for = ''; }          

        if ($product_for == $post->ID || $category_for == $post->ID) {
            if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
                remove_action('woocommerce_after_shop_loop_item',array($this, 'add_to_enquiry_cart_in_shop_loop'), 11 );
                add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );  
            }
            else {
                add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );              
            }
        } else {
            if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
                add_action('woocommerce_after_shop_loop_item',array($this, 'add_to_enquiry_cart_in_shop_loop'), 11 );
                // Enable Add to Cart with Catalog mode
                if (isset($settings['is_enable_add_to_cart']) && mvx_catalog_get_settings_value($settings['is_enable_add_to_cart'], 'checkbox') == "Enable")
                    add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );  
                else
                    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );   
            }
            else {
                // Enable Add to Cart with Catalog mode
                if (isset($settings['is_enable_add_to_cart']) && mvx_catalog_get_settings_value($settings['is_enable_add_to_cart'], 'checkbox') == "Enable")
                    add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );  
                else
                    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );   
            }
        }
    }
    
    
    public function remove_add_to_cart_button() { 
        global $Woocommerce_Catalog_Enquiry_Pro, $post;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                }
            }
        }
            
        if (isset($settings['is_custom_button']) && mvx_catalog_get_settings_value($settings['is_custom_button'], 'checkbox') == "Enable") {
        }
        else {
            // Enable Add to Cart with Catalog mode
            if (isset($settings['is_enable_add_to_cart']) && mvx_catalog_get_settings_value($settings['is_enable_add_to_cart'], 'checkbox') == "Enable") {
                add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
            } else {
                remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
            }
        }
        // Multiple Enquiry Cart Button
        if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
            // Enable Add to Cart with Catalog mode
            if (isset($settings['is_enable_add_to_cart']) && mvx_catalog_get_settings_value($settings['is_enable_add_to_cart'], 'checkbox') == "Enable") {
                add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                add_action('woocommerce_after_shop_loop_item',array($this, 'add_to_enquiry_cart_in_shop_loop'), 11 );   
            }
            else{
                remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
                add_action('woocommerce_after_shop_loop_item',array($this, 'add_to_enquiry_cart_in_shop_loop'), 11 );
            }
        }
        // Enable Add to Cart with Catalog mode
        if (isset($settings['is_enable_add_to_cart']) && mvx_catalog_get_settings_value($settings['is_enable_add_to_cart'], 'checkbox') == "Enable") {
            add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            remove_action( 'woocommerce_single_product_summary', array($this,'add_variation_product'),29);
        }
        else{
            remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
            add_action( 'woocommerce_single_product_summary', array($this,'add_variation_product'),29);
        }
        
    }

    public function add_to_enquiry_cart_in_shop_loop() {
        global $Woocommerce_Catalog_Enquiry_Pro, $product, $Woocommerce_Catalog_Enquiry_Pro_Cart, $post;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        $vendor_css_class = '';
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                    $vendor_css_class = 'vendor_'.$product_author->id.'_custom_enquiry_buttons_css';
                }
            }
        }
        
        // Multiple Enquiry Cart Button
        if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
        ?>
            <style>
            <?php 
            if ($is_vendor_product && isset($settings['can_vendor_customize_btn_style']) && mvx_catalog_get_settings_value($settings['can_vendor_customize_btn_style'], 'checkbox') == 'Enable')
                    echo str_replace("custom_enquiry_buttons_css", $vendor_css_class, $settings['custom_enquiry_buttons_css']);
            else
                    echo $settings['custom_enquiry_buttons_css'];
            ?>
            </style>
            <?php   
            $enq_cart_btn_text = __('Add to Enquiry Cart','woocommerce-catalog-enquiry-pro');
            if (isset($settings['enquiry_cart_button_text']) && !empty($settings['enquiry_cart_button_text']))
                $enq_cart_btn_text = $settings['enquiry_cart_button_text'];
            $view_enq_cart_btn_text = __('View Enquiry Cart','woocommerce-catalog-enquiry-pro');
            if (isset($settings['view_enquiry_cart_button_text']) && !empty($settings['view_enquiry_cart_button_text']))
                $view_enq_cart_btn_text = $settings['view_enquiry_cart_button_text'];
            $btn_style = '';
            if (isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) {
                $btn_style = ' custom_enquiry_buttons_css_new'; 
                if ($is_vendor_product) $btn_style = ' '.$vendor_css_class;
            }
            $args   = array(
                'class'             => 'wcce-add-enquiry-cart-button button ',
                'btn_style'     => $btn_style,
                'wpnonce'           => wp_create_nonce( 'add-enquiry-cart-' . $product->get_id() ),
                'product_id'        => $product->get_id(),
                'label'             => apply_filters( 'woocommerce_catalog_enquiry_add_to_enquiry_cart_label' , $enq_cart_btn_text ),
                'label_view_cart'   => apply_filters( 'woocommerce_catalog_enquiry_product_added_view_list' , $view_enq_cart_btn_text ),
                'enquiry_cart_url'  => $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_cart_page_url(),
                'exists'            => $Woocommerce_Catalog_Enquiry_Pro_Cart->exists_enquiry( $product->get_id() )
            );
            if (isset($settings['is_enable_out_of_stock']) && mvx_catalog_get_settings_value($settings['is_enable_out_of_stock'], 'checkbox') == "Enable" && $product->is_in_stock()) {return;}
            $args['args'] = $args;
            $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-cart-button-template.php',$args);
        }
    }
    /** Pro **/
    public function woocommerce_catalog_enquiry_success_wrapper() {
        echo '<div id="woocommerce-catalo-enquiry-msg" class="woocommerce-catalo-enquiry-msg"></div>';
    }


    public function add_variation_product() {
        global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry, $post, $product;     
        if ( $product->is_type( 'variable' ) ) {
            $variable_product = new WC_Product_Variable($product);
            // Enqueue variation scripts
            wp_enqueue_script( 'wc-add-to-cart-variation' );
            $available_variations = $variable_product->get_available_variations();      
            //attributes
            include_once ($Woocommerce_Catalog_Enquiry->plugin_path . 'templates/woocommerce-catalog-enquiry-variable-product.php');
        } elseif ($product->is_type( 'simple' )) {
            echo wc_get_stock_html( $product );
        }   
    }
    
    public function remove_price_from_product_list_loop() {             
        remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );       
    }
    
    public function remove_price_from_product_list_single() {
        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 ); 
    }


    public function alternative_text_at_price_hook_callback($price) {
        global $Woocommerce_Catalog_Enquiry_Pro,$post;
        $settings = get_woocommerce_catalog_catalog_settings();
        // MVX
        $is_vendor_product = false;
        if (is_active_MVX()) {
            if (is_object($post)) {
                $product_author = get_mvx_product_vendors($post->ID);
                if ($product_author) {
                    $is_vendor_product = is_user_mvx_vendor($product_author->id);
                    $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
                }
            }
        }
        
        if (isset($settings['replace_text_in_price']) && !empty($settings['replace_text_in_price']))    {
            return $settings['replace_text_in_price'];
        } else {
            return $price;
        }
    }

    //***************** MVX functionalities ********************//

    public function add_mvx_catalog_menu_in_vendor_dash($menu) {
        global $MVX, $Woocommerce_Catalog_Enquiry_Pro;
        $pages = get_option('mvx_pages_settings_name');
        $vendor_cap = get_option("mvx_capabilities_product_settings_name");
        $has_details_access = isset($vendor_cap['can_manage_catalog_details']) ? $vendor_cap['can_manage_catalog_details'] : '';
        $has_settings_access = isset($vendor_cap['can_manage_catalog_settings']) ? $vendor_cap['can_manage_catalog_settings'] : '';
        $global_access = true; if (empty($has_details_access) && empty($has_settings_access)) $global_access = false;
        $settings_access = true; if (empty($has_settings_access)) $settings_access = false;
        $details_access = true; if (empty($has_details_access)) $details_access = false;
        $menu['catalog-enquiry'] = array(
            'label' => __('Catalog Enquiry', 'woocommerce-catalog-enquiry-pro'),
            'url' => '#',
            'capability' => true,
            'position' => 25,
            'submenu' => array(
                'catalog-details' => array(
                    'label' => __('Enquiry Details', 'woocommerce-catalog-enquiry-pro'),
                    'url' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_catalog_details_endpoint', 'seller_dashbaord', 'catalog-details')),
                    'capability' => true,
                    'position' => 10,
                    'link_target' => '_self',
                ),
                'catalog-settings' => array(
                    'label' => __('Enquiry Settings', 'woocommerce-catalog-enquiry-pro'),
                    'url' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_catalog_settings_endpoint', 'seller_dashbaord', 'catalog-settings')),
                    'capability' => true,
                    'position' => 20,
                    'link_target' => '_self',
                )
            ),
            'link_target' => '_self',
            'nav_icon' => 'mvx-font ico-catalog-icon',
        );
        return $menu;
    }

    /**
     * Display vendor catalog details
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_catalog_details_endpoint() {
        global $Woocommerce_Catalog_Enquiry_Pro,$Woocommerce_Catalog_Enquiry_Pro_Cart,$MVX;
        $Woocommerce_Catalog_Enquiry_Pro->nocache();
        $suffix = defined( 'WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG' ) && WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG ? '' : '.min';
        $user_id = get_current_user_id();
        $vendor = get_mvx_vendor($user_id);
        register_post_status( 'completed' );


        $enquiry_status = apply_filters( 'wcce_enquiry_post_status_labels', array(
            'completed'=>__( 'Completed', 'woocommerce-catalog-enquiry-pro' ),
            'read'=>__( 'Read', 'woocommerce-catalog-enquiry-pro' ),
            'unread'=>__( 'Unread', 'woocommerce-catalog-enquiry-pro' ),
            ));
        foreach($enquiry_status as $key => $lebel){
            register_post_status( $key );
        }



        // Find each vendor enquiry product and title
        $product_titles = array();
        $enquiry_titles = array();
        foreach (get_vendor_enquiry_details(get_current_user_id()) as $key_title => $value_title) {
            $product_id = get_post_meta( $value_title, '_enquiry_product', true );
            $product_titles[$product_id] = get_the_title($product_id);
            $enquiry_titles[$value_title] = get_the_title($value_title);
        }

        $form_data_product = array_unique($product_titles);
        $form_data_customer = woocommerce_catalog_wp_users();
        $enquiry_titles = array_unique($enquiry_titles);

        $vendor_product_enquiry = get_vendor_enquiry_details($vendor->id, '', $_POST);
        $enquiry_ids = array();
        $enquiry_product = array();
        $first_enquiry_details = '';
        foreach ($vendor_product_enquiry as $key_enquiry => $value_enquiry) {
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

        // MVX 
        if (is_active_MVX()) {            
            wp_enqueue_style('mvx_catalog_frontend_css',  $Woocommerce_Catalog_Enquiry_Pro->plugin_url.'assets/frontend/css/mvx-catalog-frontend' . $suffix . '.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
            wp_enqueue_script('mvx_catalog_frontend_js', $Woocommerce_Catalog_Enquiry_Pro->plugin_url.'assets/frontend/js/mvx-catalog-frontend' . $suffix . '.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
            wp_localize_script(
                'mvx_catalog_frontend_js', 
                'mvx_catalog', 
                apply_filters( 'wc_enquiry_vendor_chat_script',array(
                'ajaxurl'           => admin_url('admin-ajax.php'), 
                'reply_sent'        => __('Reply sent successfully!','woocommerce-catalog-enquiry-pro'),    
                'something_wrong'       => __('Somethings wrong with your mail!','woocommerce-catalog-enquiry-pro'),    
                'empty_text'       => __('You have to write something','woocommerce-catalog-enquiry-pro'),
                'send_button' => __('Send','woocommerce-catalog-enquiry-pro'),
                'wait_msg' => __('Please wait...','woocommerce-catalog-enquiry-pro'), 
                'scroll_limit' => 5000,
                'form_data_product' => $form_data_product,
                'form_data_customer' => $form_data_customer,
                'enquiry_titles' => $enquiry_titles,
                'first_enquiry_details' => $first_enquiry_details,
                'type_text' => __('Start Typing','woocommerce-catalog-enquiry-pro')
            )));
            // load style & scripts
            if ( class_exists( 'woocommerce' ) ) {
                wp_dequeue_style( 'select2' );
                wp_deregister_style( 'select2' );
                wp_dequeue_script( 'select2');
                wp_deregister_script('select2');
            }
            $MVX->library->load_select2_lib();
        }

        wp_enqueue_script('cloudjs', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
        wp_enqueue_script('block_uijs','https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
        
        $vendor_cap = get_option("mvx_capabilities_product_settings_name");
        //$vendor_can_manage = isset($vendor_cap['can_manage_catalog_details']) ? $vendor_cap['can_manage_catalog_details'] : '';
        // If vendor does not have permission to access catalog enquiry details
        if ($vendor) {
            //_e('You do not have permission to access the Catalog Enquiry Details. Please contact site administrator.', 'woocommerce-catalog-enquiry-pro');
            //return;
        }
        // vendor dashboard enquiry template
        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('vendor-dashboard/woocommerce-catalog-enquiry-pro-vendor-catalog-details.php');
    }

    /**
     * Display vendor catalog settings
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_catalog_settings_endpoint() {
        global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry_Pro_Cart, $MVX, $Woocommerce_Catalog_Enquiry;
        $Woocommerce_Catalog_Enquiry_Pro->nocache();
        $settings_buttons = get_woocommerce_catalog_catalog_settings();
        $user = wp_get_current_user();
        $suffix = defined( 'WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG' ) && WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG ? '' : '.min';
        // MVX
        $is_vendor_product = false;
        if (is_active_MVX()) {
            if (is_object($user)) {
                $is_vendor_product = is_user_mvx_vendor($user->ID);
            }
        }
        if ($is_vendor_product) {
            $settings_buttons = get_woocommerce_catalog_catalog_settings($user->ID);
        }
        // MVX 
        if (is_active_MVX()) {
            wp_enqueue_style('mvx_catalog_frontend_css',  $Woocommerce_Catalog_Enquiry_Pro->plugin_url.'assets/frontend/css/mvx-catalog-frontend' . $suffix . '.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
            wp_enqueue_script('mvx_catalog_frontend_js', $Woocommerce_Catalog_Enquiry_Pro->plugin_url.'assets/frontend/js/mvx-catalog-frontend' . $suffix . '.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
            wp_localize_script(
                'mvx_catalog_frontend_js', 
                'mvx_catalog', 
            array(
                'ajaxurl'           => admin_url('admin-ajax.php'), 
                'reply_sent'        => __('Reply sent successfully!','woocommerce-catalog-enquiry-pro'),    
                'something_wrong'       => __('Somethings wrong with your mail!','woocommerce-catalog-enquiry-pro'),    
                'type_text' => __('Start Typing','woocommerce-catalog-enquiry-pro')
            ));
            // load style & scripts
            if ( class_exists( 'woocommerce' ) ) {
                wp_dequeue_style( 'select2' );
                wp_deregister_style( 'select2' );
                wp_dequeue_script( 'select2');
                wp_deregister_script('select2');
            }
            $MVX->library->load_select2_lib();
        }
        //wp_dequeue_script( 'colorpicker_init');
        //wp_deregister_script('colorpicker_init');
        wp_enqueue_script('jquery-ui-slider');
        $Woocommerce_Catalog_Enquiry_Pro->library->load_jqueryui_lib();

        wp_enqueue_style( 'wp-color-picker' );
                        $Woocommerce_Catalog_Enquiry_Pro->library->load_qtip_lib();
                $Woocommerce_Catalog_Enquiry_Pro->library->load_select2_lib();
                $Woocommerce_Catalog_Enquiry_Pro->library->load_upload_lib();
                $Woocommerce_Catalog_Enquiry_Pro->library->load_colorpicker_lib();
                $Woocommerce_Catalog_Enquiry_Pro->library->load_datepicker_lib();
               // Colorpicker css
                wp_enqueue_style('button_color_picker_css', $Woocommerce_Catalog_Enquiry->plugin_url . 'assets/admin/css/colorpicker_btn.css', array(), $Woocommerce_Catalog_Enquiry->version);
                // Colorpicker js
                wp_enqueue_script('button_color_picker_js', $Woocommerce_Catalog_Enquiry->plugin_url . 'assets/admin/js/colorpicker_btn.js', array('jquery'), $Woocommerce_Catalog_Enquiry->version, true);

        wp_enqueue_script('button_gen_js', $Woocommerce_Catalog_Enquiry->plugin_url.'assets/admin/js/button_gen.js', array('jquery'), $Woocommerce_Catalog_Enquiry->version, true);
        wp_localize_script(
            'button_gen_js', 
            'mvx_catalog_btn', 
        array(
            'custom_css' => isset($settings_buttons['custom_enquiry_buttons_css']) ? $settings_buttons['custom_enquiry_buttons_css'] : '',
            'custom_cssStuff' => isset($settings_buttons['custom_enquiry_buttons_cssStuff']) ? $settings_buttons['custom_enquiry_buttons_cssStuff'] : '',
            'custom_cssValues' => isset($settings_buttons['custom_enquiry_buttons_cssValues']) ? $settings_buttons['custom_enquiry_buttons_cssValues'] : '',
        ));

        $user_id = get_current_user_id();
        $vendor = get_mvx_vendor($user_id);
        $vendor_cap = get_option("mvx_capabilities_product_settings_name");
        //$vendor_can_manage = isset($vendor_cap['can_manage_catalog_settings']) ? $vendor_cap['can_manage_catalog_settings'] : '';
        // If vendor does not have permission to access catalog settings
        if ($vendor) {
            //_e('You do not have permission to access the Catalog Enquiry Settings. Please contact site administrator.', 'woocommerce-catalog-enquiry-pro');
            //return;
        }
        $Woocommerce_Catalog_Enquiry_Pro->template->get_template('vendor-dashboard/woocommerce-catalog-enquiry-pro-vendor-catalog-settings.php');
    }

    public function save_mvx_vendor_catalog_data() {
        global $MVX, $Woocommerce_Catalog_Enquiry_Pro;
        $vendor = get_mvx_vendor(get_current_user_id());
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            switch ($MVX->endpoints->get_current_endpoint()) {
                case 'catalog-settings':
                    if ( !isset( $_POST['vendor_catalog_settings_nonce'] ) || !wp_verify_nonce( $_POST['vendor_catalog_settings_nonce'], 'mvx_vendor_catalog_settings_nonce' ) ) return;
                    $excepts = array('vendor_catalog_settings_nonce','_wp_http_referer','vendor_catalog_settings');
                    $mvx_catalog_settings = array();
                    $is_saved = 0;
                    if (isset($_POST['vendor_catalog_settings'])) { 
                        foreach($excepts as $value) {
                            if (isset($_POST[$value]))
                                unset($_POST[$value]);
                        }
                        if (isset($_POST['custom_enquiry_buttons_cssStuff']))
                                $_POST['custom_enquiry_buttons_cssStuff'] = maybe_serialize(wp_unslash($_POST['custom_enquiry_buttons_cssStuff']));
                        if (isset($_POST['custom_enquiry_buttons_cssValues']))
                                $_POST['custom_enquiry_buttons_cssValues'] = maybe_serialize(wp_unslash($_POST['custom_enquiry_buttons_cssValues']));
                        update_user_meta($vendor->id, '_mvx_vendor_catalog_settings', $_POST);
                        $is_saved = 1;
                    }

                    if ($is_saved == 1) {
                        wc_add_notice(__('All Options Saved', $MVX->text_domain), 'success');
                    } else {
                        wc_add_notice(__('Somethings went wrong!', $MVX->text_domain), 'error');
                    }
                    break;
                case 'catalog-details':
                    
                    break;
                default :
                    break;
            }
        }
    }

    /**
    * avatar_override()
    *
    * Overrides an avatar with a profile image
    *
    * @param string $avatar SRC to the avatar
    * @param mixed $id_or_email 
    * @param int $size Size of the image
    * @param string $default URL to the default image
    * @param string $alt Alternative text
    **/
    public function woocommerce_catalog_user_avatar_override( $avatar, $id_or_email, $size, $default, $alt, $args=array()) {
        //Get user data
        if ( is_numeric( $id_or_email ) ) {
                $user = get_user_by( 'id', ( int )$id_or_email );
        } elseif ( is_object( $id_or_email ) )  {
            $comment = $id_or_email;
            if ( empty( $comment->user_id ) ) {
                    $user = get_user_by( 'id', $comment->user_id );
            } else {
                    $user = get_user_by( 'email', $comment->comment_author_email );
            }
            if ( !$user ) return $avatar;
        } elseif ( is_string( $id_or_email ) ) {
            $user = get_user_by( 'email', $id_or_email );
        } else {
            return $avatar;
        }
        if ( !$user ) return $avatar;
        $classes = array(
            'avatar',
            sprintf( 'avatar-%s', esc_attr( $size ) ),
            'photo'
        );  
        if ( isset( $args[ 'class' ] ) ) {
            if ( is_array( $args['class'] ) ) {
                $classes = array_merge( $classes, $args['class'] );
            } else {
                $args[ 'class' ] = explode( ' ', $args[ 'class' ] );
                $classes = array_merge( $classes, $args[ 'class' ] );
            }
        } 
        //Get custom filter classes
        $classes = (array)apply_filters( 'woocommerce_catalog_user_avatar_classes', $classes );

        //Determine if the user is MVX user
        $mvx_vendor_avatar = '';
        if (class_exists('MVX_Woocommerce_Catalog_Enquiry')) {
            if (is_user_mvx_vendor($user->ID)) {
                $vendor = get_mvx_vendor($user->ID);
                if ($vendor->image == '') {
                    return $avatar; 
                } else {    
                    $mvx_vendor_avatar = sprintf(
                        "<img alt='%s' src='%s' class='%s' height='%d' width='%d' %s/>",
                        esc_attr( $args['alt'] ),
                        esc_url( $vendor->image ),
                        esc_attr(implode( ' ', $classes ) ),
                        (int) $size,
                        (int) $size,
                        $args['extra_attr']
                    );
                }
            }
        }

        if (!empty($mvx_vendor_avatar))
            return $mvx_vendor_avatar;
        else
            return $avatar; 
    }
    

    function frontend_scripts() {
        global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry_Pro_Cart, $Woocommerce_Catalog_Enquiry;
        $frontend_script_path = $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
        $suffix = defined( 'WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG' ) && WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG ? '' : '.min';
        // Enqueue your frontend javascript from here
        $settings = get_woocommerce_catalog_catalog_settings(); 
        
        if (isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable") {
          if (isset($settings['is_enable_multiple_product_enquiry']) && mvx_catalog_get_settings_value($settings['is_enable_multiple_product_enquiry'], 'checkbox') == "Enable") {
            // For block area
            wp_enqueue_script('woocommerce_catalog_add_to_enquiry_js', $frontend_script_path.'add-to-enquiry-cart' . $suffix . '.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
            wp_localize_script(
                'woocommerce_catalog_add_to_enquiry_js', 
                'enquiry_cart', 
            apply_filters( 'woocommerce_catalog_localize_script_enquiry_cart_data', array(
                'ajaxurl'           => admin_url('admin-ajax.php'), 
                'is_empty_enquiry'      => $Woocommerce_Catalog_Enquiry_Pro_Cart->is_empty_enquiry(),   
                'no_more_product'       => __('No more product in Enquiry Cart!','woocommerce-catalog-enquiry-pro'),    
            )));
          }
        }
        /*********************    Cleanup      ***************************/
        if (isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable") {
            wp_enqueue_script('wce_frontend_js', $frontend_script_path.'frontend' . $suffix . '.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
            // For localize add_filter `woocommerce_catalog_enquiry_localize_script_data` added
            // if enquiry form use google reCAPTCHA
            $wcce_enquiry_form_data = get_option('wcce_enquiry_form_data');
            if (!empty($wcce_enquiry_form_data) && is_array($wcce_enquiry_form_data)) {
                foreach ($wcce_enquiry_form_data as $key => $value) {
                    $this->enquiry_form_field_types[] = $value['type'];
                }
            }
            if (is_array($this->enquiry_form_field_types) && !empty($this->enquiry_form_field_types) && in_array('recaptcha', $this->enquiry_form_field_types))
                wp_enqueue_script('woocommerce_catalog_google_recaptcha', 'https://www.google.com/recaptcha/api.js', array(), $Woocommerce_Catalog_Enquiry_Pro->version, false);
            // date-time picker
            if (is_array($this->enquiry_form_field_types) && !empty($this->enquiry_form_field_types) && in_array('datepicker', $this->enquiry_form_field_types))
                wp_enqueue_script( 'jquery-ui-datepicker' );
            if (is_array($this->enquiry_form_field_types) && !empty($this->enquiry_form_field_types) && in_array('timepicker', $this->enquiry_form_field_types))
                wp_enqueue_script('wce_timepicker_js', $frontend_script_path.'dctimepicker' . $suffix . '.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
        }
    }

    function frontend_styles() {
        global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry;
        $settings = get_woocommerce_catalog_catalog_settings();

        $frontend_style_path = $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
        $suffix = defined( 'WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG' ) && WOOCOMMERCE_CATALOG_ENQUIRY_PRO_SCRIPT_DEBUG ? '' : '.min';

        // Enqueue your frontend stylesheet from here
        if (isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable") {
            wp_enqueue_style('wce_frontend_css',  $frontend_style_path.'frontend' . $suffix . '.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
            
            $popup_backdrop = isset($settings['is_disable_popup_backdrop']) ? 'transparent' : 'rgba(0,0,0,0.4)';

            $inline_css = "
                    /* The Modal (background) */
                    #woo_catalog .catalog-modal {
                        background-color: {$popup_backdrop}; /* Black w/ opacity */
                    }";

            wp_add_inline_style('wce_frontend_css', $inline_css );

            // date-time picker
            $Woocommerce_Catalog_Enquiry_Pro->library->load_datepicker_lib();
            wp_enqueue_style('wce_timepicker_css',  $frontend_style_path.'dctimepicker' . $suffix . '.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
        }
    }

    public function woocommerce_catalog_enquiry_localize_script_data($settings_data) {
        $wcce_enquiry_form_data = get_option('wcce_enquiry_form_data');
        unset( $settings_data['json_arr'] );
        unset( $settings_data['settings'] );
        unset( $settings_data['settings_gen'] );
        unset( $settings_data['redirect_link'] );
        $settings_data['wcce_enquiry_form_data'] = $wcce_enquiry_form_data;
        $settings_data['no_more_product'] = __('No more product in Enquiry Cart!','woocommerce-catalog-enquiry-pro');
        
        $woocommerce_catalog_pro_errors_data = apply_filters( 'woocommerce_catalog_enquiry_pro_errors_data', array( 
            'email_invalid' => __('Please Enter Valid Email Id.', 'woocommerce-catalog-enquiry-pro'),
            'is_required' => __('is required.', 'woocommerce-catalog-enquiry-pro'),
            'norecaptcha' => __('Please check the Recaptcha','woocommerce-catalog-enquiry-pro'),
            'spam' => __('Please dont spam','woocommerce-catalog-enquiry-pro'),
            ) );
        $settings_data['error_levels'] = array_merge( $settings_data['error_levels'], $woocommerce_catalog_pro_errors_data );
        return apply_filters( 'woocommerce_catalog_enquiry_pro_errors_data_return', $settings_data );
    }

    public function catalog_enquiry_custom_link_for_disable_cart( $link ) {
        $general_data = get_option( 'woocommerce_catalog_enquiry_general_settings' );
        if ( !empty($general_data) && isset($general_data['disable_cart_page_link']) && !empty($general_data['disable_cart_page_link'])) {
            $link = get_permalink($general_data['disable_cart_page_link']);
        }
        return $link;
    }

}
