<?php
/**
 * WCCE Widget Init Class
 *
 * @version		1.0.5
 * @package		WCCE
 * @author 		WC Marketplace
 */
 
class Woocommerce_Catalog_Enquiry_Pro_Widget{
  
	public function __construct() {
		add_action('widgets_init', array($this, 'woocommerce_catalog_catalog_enquiry_widgets_init'));
	}
	
	/**
	* Add WCCE Catalog widgets
	*/
	function woocommerce_catalog_catalog_enquiry_widgets_init() {
		global $Woocommerce_Catalog_Enquiry_Pro;
		$settings = get_woocommerce_catalog_catalog_settings();
		if(isset($settings['is_enable']) && $settings['is_enable'] == "Enable") {
			require_once ('widgets/class-woocommerce-catalog-enquiry-pro-widget-enquiry-cart.php');
			register_widget('Woocommerce_Catalog_Enquiry_Pro_Widget_Enquiry_Cart'); 
		}   
  	}
  
}