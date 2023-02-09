<?php
class Woocommerce_Catalog_Enquiry_Pro_Library {
  
    public $lib_url;  
    public $jquery_lib_url;

    public function __construct() {
        
        global $Woocommerce_Catalog_Enquiry_Pro;
        $this->lib_url = $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'lib/';
        $this->jquery_lib_url = $this->lib_url . 'jquery/';
    
    }

       /**
     * Jquery qTip library
     */
    public function load_qtip_lib() {
        global $Woocommerce_Catalog_Enquiry_Pro;
        wp_enqueue_script('qtip_js', $this->jquery_lib_url . 'qtip/qtip.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
        wp_enqueue_style('qtip_css',  $this->jquery_lib_url . 'qtip/qtip.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
    }

    /**
     * Select2 library
     */
    public function load_select2_lib() {
        global $Woocommerce_Catalog_Enquiry_Pro;
        wp_enqueue_script('select2_js', $this->jquery_lib_url . 'select2/select2.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
        wp_enqueue_style('select2_css', $this->jquery_lib_url . 'select2/select2.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
    }
    
    /**
     * WP Media library
     */
    public function load_upload_lib() {
      global $Woocommerce_Catalog_Enquiry_Pro;
      wp_enqueue_media();
      wp_enqueue_script('upload_js', $this->jquery_lib_url . 'upload/media-upload.js', array('jquery'), $Woocommerce_Catalog_Enquiry_Pro->version, true);
      wp_enqueue_style('upload_css',  $this->jquery_lib_url . 'upload/media-upload.css', array(), $Woocommerce_Catalog_Enquiry_Pro->version);
    }
    
    /**
     * WP ColorPicker library
     */
    public function load_colorpicker_lib() {
      global $Woocommerce_Catalog_Enquiry_Pro;
      wp_enqueue_script( 'wp-color-picker' );
      wp_enqueue_script( 'colorpicker_init', $this->jquery_lib_url . 'colorpicker/colorpicker.js', array( 'jquery', 'wp-color-picker' ), $Woocommerce_Catalog_Enquiry_Pro->version, true );
      wp_enqueue_style( 'wp-color-picker' );
    }
    
    /**
     * WP DatePicker library
     */
    public function load_datepicker_lib() {
      wp_enqueue_script('jquery-ui-datepicker');
      $this->load_jqueryui_lib();
    }


    /**
     * Load JqueryUI library
     */
    public function load_jqueryui_lib() {
      global $wp_scripts;
      if (wp_style_is( 'jquery-ui-style', 'registered' )){
        wp_enqueue_style( 'jquery-ui-style' );
      } else {
        $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';
        wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );
        wp_enqueue_style( 'jquery-ui-style' );
      }
    }

}
