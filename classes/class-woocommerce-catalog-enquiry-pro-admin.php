<?php
class Woocommerce_Catalog_Enquiry_Pro_Admin {
  
  public $settings;

    public function __construct() {
        //admin script and style
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'), 99);
        
        // Text update/changed from free to pro
        add_filter( 'woocommerce_catalog_enquiry_enable_catalog_text', array($this, 'woocommerce_catalog_enquiry_enable_catalog_text'));
        add_filter( 'woocommerce_catalog_enquiry_hide_cart', array($this, 'woocommerce_catalog_enquiry_hide_cart'));

        $settings = get_woocommerce_catalog_catalog_settings();
        if(isset($settings['is_enable']) && mvx_catalog_get_settings_value($settings['is_enable'], 'checkbox') == "Enable") {
          $this->post_type = 'wcce_enquiry';
          $this->register_post_type();
          $this->register_post_status();
        }
    }

    /**
    * Register Enquiry post type
    *
    * @access public
    * @return void
    */
    function register_post_type() {

      if ( post_type_exists($this->post_type) ) return;

      $labels = array(
        'name' => _x( 'WC Catalog Enquiry Details', 'post type general name' , 'woocommerce-catalog-enquiry-pro' ),
        'singular_name' => __( 'WC Catalog Enquiry Detail', 'post type singular name' , 'woocommerce-catalog-enquiry-pro' ),
        'add_new' => __( 'Add New', $this->post_type , 'woocommerce-catalog-enquiry-pro' ),
        'add_new_item' => sprintf( __( 'Add New %s' , 'woocommerce-catalog-enquiry-pro' ), __( 'WC Catalog Enquiry Detail' , 'woocommerce-catalog-enquiry-pro' ) ),
        'edit_item' => sprintf( __( 'Edit %s' , 'woocommerce-catalog-enquiry-pro' ), __( 'WC Catalog Enquiry Detail' , 'woocommerce-catalog-enquiry-pro') ),
        'menu_name' => __( 'WC Catalog Enquiry Details' , 'woocommerce-catalog-enquiry-pro' )
        );

      $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'show_in_nav_menus' => false,
        'query_var' => false,
        'rewrite' => true,
        'capability_type' => 'post',
        'capabilities' => array('create_posts' => 'do_not_allow', 'delete_posts' => false),
        'map_meta_cap' => true,       
        'has_archive' => true,
        'hierarchical' => true,
        'supports' => array( 'title' ),
        );
      register_post_type( $this->post_type, $args );
    }

    function register_post_status() {
      global $MVX_Woocommerce_Catalog_Enquiry;
      $enquiry_status = apply_filters( 'wcce_enquiry_post_status_labels', array(
        'completed'=>__( 'Close', 'woocommerce-catalog-enquiry-pro' ),
        'read'=>__( 'Read', 'woocommerce-catalog-enquiry-pro' ),
        'unread'=>__( 'Unread', 'woocommerce-catalog-enquiry-pro' ),
        ));
      foreach($enquiry_status as $key => $lebel){
        register_post_status( $key, array(
          'label'                     => $lebel,
          'public'                    => true,
          'exclude_from_search'       => false,
          'show_in_admin_all_list'    => true,
          'show_in_admin_status_list' => true,
          'label_count'               => _n_noop( $lebel.' <span class="count">(%s)</span>', $lebel.' <span class="count">(%s)</span>' ),
          ) );
      }
    }
    
    /**
     * Admin Scripts
     */

    public function enqueue_admin_script() {
      global $Woocommerce_Catalog_Enquiry_Pro;
      $screen = get_current_screen();
      if ( in_array( $screen->id , array( 'toplevel_page_catalog' )) ) {
        wp_enqueue_script('woocommerce_catalog_build', $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'build/index.js', array( 'wp-element' ), $Woocommerce_Catalog_Enquiry_Pro->version);


        wp_enqueue_style('mvx_catalog-pro-backend-css', $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'build/style-index.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);

        $all_products = $all_users = $enquiry_titles = array();
        $args = apply_filters('woocommerce_catalog_limit_backend_product', array( 'posts_per_page' => -1, 'post_type' => 'product', 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ));
        $woocommerce_product = get_posts( $args );
        foreach ( $woocommerce_product as $post => $value ){
          $all_products[$value->ID] = $value->post_title;     
        }

        $users = get_users();
        foreach($users as $user) {                  
          $all_users[$user->data->ID] = $user->data->display_name;
        }

        $enquiry_posts = get_posts( 
          array(
              'posts_per_page'    => -1,
              'post_type'         => 'wcce_enquiry',
              'post_status' => array('publish', 'pending', 'completed', 'read', 'unread'),
              )
          );
        foreach ($enquiry_posts as $key => $value) {
            $enquiry_titles[$value->ID] = $value->post_title;
        }
        
        wp_localize_script( 'mvx-catalog-script', 'catalogproappLocalizer', apply_filters('catalog_settings', [
        'apiUrl' => home_url( '/wp-json' ),
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'search_by_status'  =>  array(
            array(
                'key' => "base",
                'label'=> __('All', 'woocommerce-catalog-enquiry'),
                'value'=> "base",
            ),
            array(
                'key'=> "read_name",
                'label'=> __('Read', 'woocommerce-catalog-enquiry'),
                'value'=> "read_name",
            ),
            array(
                'key'=> "unread_name",
                'label'=> __('Unread', 'woocommerce-catalog-enquiry'),
                'value'=> 'unread_name',
            ),
            array(
                'key'=> "completed_name",
                'label'=> __('Closed', 'woocommerce-catalog-enquiry'),
                'value'=> 'completed_name',
            )
          ),
        'search_by'  =>  array(
            array(
                'key' => "base",
                'label'=> __('Select a Segment', 'woocommerce-catalog-enquiry'),
                'value'=> "base",
            ),
            array(
                'key'=> "product_name",
                'label'=> __('Product Name', 'woocommerce-catalog-enquiry'),
                'value'=> "product_name",
            ),
            array(
                'key'=> "customer_name",
                'label'=> __('Customer name', 'woocommerce-catalog-enquiry'),
                'value'=> 'customer_name',
            ),
            array(
                'key'=> "enquiry_number",
                'label'=> __('Enquiry Number', 'woocommerce-catalog-enquiry'),
                'value'=> 'enquiry_number',
            )
          ),
          'all_users'  =>  mvx_catalog_pro_convert_select_structure($all_users),
          'all_products'  =>  mvx_catalog_pro_convert_select_structure($all_products),
          'enquiry_titles'  =>  mvx_catalog_pro_convert_select_structure($enquiry_titles),

      ] ) );

      }

      if ( in_array( $screen->id , array( 'catalog_page_mvx-catalog-license-admin' )) ) {
        wp_enqueue_style('mvx_catalog_admin_css', $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/admin/css/admin.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
      }

      /*wp_localize_script('admin_enquiry_details_js', 'enquiry_admin',apply_filters( 'wc_enquiry_admin_chat_script', array('ajaxurl' => admin_url('admin-ajax.php'), 'reply_sent' => __('Reply sent successfully!','woocommerce-catalog-enquiry-pro'),'empty_text' => __('Field is empty','woocommerce-catalog-enquiry-pro'),'something_wrong' => __('Somethings wrong with your mail!','woocommerce-catalog-enquiry-pro'),'send_button' => __('Send','woocommerce-catalog-enquiry-pro'),'wait_msg' => __('Please wait...','woocommerce-catalog-enquiry-pro'),'scroll_limit' => 5000, 'first_enquiry_details' => ''
      ) ) );*/
    }

    public function woocommerce_catalog_admin_reply_page() {
      ?>
      <form method="post" action="">
      <div class="wrap">
        <div class="" >
          <p class="catalog-reply-padding">
            <?php
            global $Woocommerce_Catalog_Enquiry_Pro, $MVX, $woocommerce, $Woocommerce_Catalog_Enquiry_Pro_Cart;
            $user = wp_get_current_user();

            $vendor_product_enquiry = get_all_enquiry_details($_POST);
              ?>
              <div class="msg-wrap">

              <!-- Filter section start -->

              <!--Search by status:-->
              <div class='selectInputArea'>
                <div>
                  <?php 
                  $enquiry_status = apply_filters( 'mvx_vendor_catalog_status', array(
                  'base' => __( 'All', 'woocommerce-catalog-enquiry-pro' ),

                  'read_name' => __( 'Read', 'woocommerce-catalog-enquiry-pro' ),
                  'unread_name' => __( 'Unread', 'woocommerce-catalog-enquiry-pro' ),
                  'completed_name' => __( 'Closed', 'woocommerce-catalog-enquiry-pro' )

                    ) );
                    ?>
                    <div class='singleInputCls'>
                      <p><?php esc_html_e('Search By Status', 'woocommerce-catalog-enquiry-pro'); ?></p>
                      <select id="others-choice" name="others_choice">
                        <?php foreach ($enquiry_status as $key => $value) { ?>
                        <option value="<?php esc_attr_e($key); ?>"><?php esc_html_e($value); ?></option>
                        <?php } ?>
                      </select>
                    </div>

                    <!--search by different type:-->
                    <?php 
                    $enquiry_search_type = apply_filters( 'mvx_vendor_catalog_status', array(
                      'base' => __( 'Select a Segment', 'woocommerce-catalog-enquiry-pro' ),
                      'product_name' => __( 'Product Name', 'woocommerce-catalog-enquiry-pro' ),
                      'customer_name' => __( 'Customer name', 'woocommerce-catalog-enquiry-pro' ),
                      'enquiry_number' => __( 'Enquiry Number', 'woocommerce-catalog-enquiry-pro' )
                      ) );
                      ?>
                      <div class='singleInputCls'>
                        <p><?php esc_html_e('Search By', 'woocommerce-catalog-enquiry-pro'); ?></p>
                        <select id="first-choice" name="first_choice">
                          <?php foreach ($enquiry_search_type as $key => $value) { ?>
                          <option value="<?php esc_attr_e($key); ?>"><?php esc_html_e($value); ?></option>
                          <?php } ?>
                        </select>
                      </div>

                      <!--Select options :-->
                      <div class='singleInputCls'>
                        <p><?php esc_html_e('Choose Segment', 'woocommerce-catalog-enquiry-pro'); ?></p>
                        <select multiple='multiple' id="second-choice" name="second_choice[]" >
                          <option><?php esc_html_e('Please choose from above', 'woocommerce-catalog-enquiry-pro'); ?></option>
                        </select>
                        <input type="submit" name="filter_action" id="post-query-submit" class="button btn btn-default" value="<?php esc_attr_e('Filter', 'woocommerce-catalog-enquiry-pro'); ?>">   
                      </div>
                    </div>

                    <!--Date Range:-->
                    <div class='dateFieldInput'>
                      <div class='singleInputCls'>
                        <p><?php esc_html_e('Date Range', 'woocommerce-catalog-enquiry-pro'); ?></p>
                        <span class="date-inp-wrap">
                          <input type="text" name="catalog_start_date_order" class="pickdate gap1 catalog_start_date_order form-control" placeholder="<?php esc_attr_e('from', 'dc-woocommerce-multi-vendor'); ?>" value="<?php echo isset($_POST['catalog_start_date_order']) ? $_POST['catalog_start_date_order'] : date('Y-m-01'); ?>" />
                        </span> 

                        <span class="date-inp-wrap">
                          <input type="text" name="catalog_end_date_order" class="pickdate catalog_end_date_order form-control" placeholder="<?php esc_attr_e('to', 'dc-woocommerce-multi-vendor'); ?>" value="<?php echo isset($_POST['catalog_end_date_order']) ? $_POST['catalog_end_date_order'] : date('Y-m-d'); ?>" />
                        </span>
                        <input type="submit" name="date_action" id="post-query-submit" class="button" value="Filter">
                      </div>
                    </div>
                  </div>

              <!-- Filter section end -->

                <div class="messaging">
                  <div class="inbox-msg">
                    <div class="inbox-people">
                      <div class="inbox-chat">
                        <?php 

                        if( !empty($vendor_product_enquiry) ) {

                        foreach ($vendor_product_enquiry as $key => $value) { 
                          $product_id = get_post_meta( $value ,'_enquiry_product', true );
                          $enquiry_user_email = get_post_meta( $value , '_enquiry_useremail', true );

                          $user_details = get_user_by( 'ID' , get_post_field ('post_author', $value) );

                          $product = wc_get_product( $product_id );
                          if(!$product) continue;

                          $to_user_id = $user_details ? $user_details->data->ID : 0;

                          if(is_active_MVX()){
                            $vendor_details = get_mvx_product_vendors( $product_id ) ? get_mvx_product_vendors( $product_id ) : false;
                          } else {
                            $vendor_details = false;
                          }

                          if( $vendor_details ){
                            $last_massage = get_user_last_massage( $vendor_details->id , $to_user_id, $product_id);
                          } else {
                            $last_massage = get_user_last_massage( get_current_user_id() , $to_user_id, $product_id);
                          }

                          $histrory_unread = get_customer_vendor_admin_conversation_details( get_current_user_id() , $to_user_id, $product_id );
                          $count = 0;
                          foreach ($histrory_unread as $key => $value1) {
                            if( $value1->status == 'unread' && $value1->from_user_id != get_current_user_id() ){
                              $count++;
                            }
                          }

                          ?>
                          <div class="chat-list chat-list_<?php echo $value ?>" id="<?php echo $value; ?>" data-option="<?php echo $product_id; ?>" data-conv="<?php echo $to_user_id; ?>" onclick="woocommerce_catalog_enquiry_open_chat(this)">
                            <div class="chat-people">
                              <div class="chat-img"> <?php echo $product->get_image(); ?> </div>
                              <div class="chat-ib">
                                <h5>
                                  <?php echo $product->get_name() ?><?php if( $count > 0 ) { ?>
                                  <div class="hide_unread<?php echo $value; ?>">  <span class="chat-notify"><?php echo $count; ?></span></div>
                                  <?php } ?>
                                </h5>
                                <p class="admin-short-comming-msg"><?php if( !empty( $last_massage ) ) echo end($last_massage)->chat_message; ?></p>
                              </div>
                            </div>
                          </div>
                          <?php }

                        } else { 
                          $no_enquiry = apply_filters( 'woocommerce_no_enquiry_msg' , __( '** No enquiry Found **' , 'woocommerce-catalog-enquiry-pro' ) );
                          ?>
                          <h3 class="text-center"><?php echo $no_enquiry; ?></h3><?php 
                        } 
                        ?>
                        </div>
                      </div>
                      <div class="mesgs"></div>
                    </div>
                  </div>
                </div>
                
            </p>
          </div>
        </div>
        </form>
        <?php
    }

    // Enable enquiry text change
    public function woocommerce_catalog_enquiry_enable_catalog_text( $enquiry_text ) {
      $enquiry_text = __( 'Enable this to activate catalog mode sitewide.', 'woocommerce-catalog-enquiry-pro' );
      return $enquiry_text;
    } 
    
    // Enable enquiry text change
    public function woocommerce_catalog_enquiry_hide_cart( $enquiry_text ) {
      $enquiry_text = __( 'Enable this to hide cart and checkout page and set new page to redirect the user.', 'woocommerce-catalog-enquiry-pro' );
      return $enquiry_text;
    }
}