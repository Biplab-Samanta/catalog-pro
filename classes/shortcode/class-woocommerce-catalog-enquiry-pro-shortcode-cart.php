<?php
/**
 * Woocommerce_Catalog_Enquiry_Pro_Shortcode_Cart
 *
 * @version		1.0.0
 * @package		Woocommerce-catalog-enquiry-pro/shortcode
 * @author 		WC Marketplace
 */
 
class Woocommerce_Catalog_Enquiry_Pro_Shortcode_Cart {

	public function __construct() {

	}

	/**
	 * Output Woocommerce_Catalog_Enquiry_Pro_Shortcode_Cart.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function cart_content( $attr ) {
		global $Woocommerce_Catalog_Enquiry_Pro,$Woocommerce_Catalog_Enquiry_Pro_Cart;
		$Woocommerce_Catalog_Enquiry_Pro->nocache();

		$enquiry_data  = $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data();
	
        $args = array(
            'enquiry_data'   => $enquiry_data
        );
        $args['args'] = $args;
		$Woocommerce_Catalog_Enquiry_Pro->template->get_template( 'shortcode/woocommerce-catalog-enquiry-pro-cart.php', $args ); 
	}
}