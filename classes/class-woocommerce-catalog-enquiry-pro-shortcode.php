<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class 		WCCE Shortcode Class
 *
 * @version	    1.0.0
 * @package		WCCE
 * @author 		WC Marketplace
 */
class Woocommerce_Catalog_Enquiry_Pro_Shortcode {

	public function __construct() {
		// WCCE Enquiry cart
		add_shortcode('woocommerce_catalog_enquiry_cart',array(&$this,'woocommerce_catalog_enquiry_cart'));
	}
	
	/**
	 * WCCE Enquiry cart
	 *
	 * @return void
	 */
	public function woocommerce_catalog_enquiry_cart($attr) {
		global $Woocommerce_Catalog_Enquiry_Pro;
		$this->load_class('cart');
		return $this->shortcode_wrapper(array('Woocommerce_Catalog_Enquiry_Pro_Shortcode_Cart', 'cart_content'));
	}
	
	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	
	public function load_class($class_name = '') {
		global $Woocommerce_Catalog_Enquiry_Pro;
		if ('' != $class_name && '' != $Woocommerce_Catalog_Enquiry_Pro->token) {
			require_once ('shortcode/class-' . esc_attr($Woocommerce_Catalog_Enquiry_Pro->token) . '-shortcode-' . esc_attr($class_name) . '.php');
		}
	}

}