<?php

/**
 * Plugin Name: Woocommerce Catalog Enquiry Pro
 * Plugin URI: https://multivendorx.com
 * Description: Convert your WooCommerce store into a catalog website in a click
 * Author: WC Marketplace 
 * Version: 2.0.7
 * Author URI: https://multivendorx.com
 * Requires at least: 4.0
 * Tested up to: 6.1.1
 * WC requires at least: 3.0
 * WC tested up to: 7.2.2
 * Text Domain: woocommerce-catalog-enquiry-pro
 * Domain Path: /languages/
 */

if ( ! class_exists( 'Woocommerce_Catalog_Enquiry_Pro_Dependencies' ) )
	require_once trailingslashit(dirname(__FILE__)).'includes/class-woocommerce-catalog-enquiry-pro-dependencies.php';

require_once trailingslashit(dirname(__FILE__)).'includes/class-woocommerce-catalog-enquiry-pro-core-functions.php';

require_once trailingslashit(dirname(__FILE__)).'woocommerce-catalog-enquiry-pro-config.php';

if(!defined('ABSPATH')) exit; // Exit if accessed directly

if(!defined('WOOCOMMERCE_CATALOG_ENQUIRY_PRO_PLUGIN_TOKEN')) exit;

if(!defined('WOOCOMMERCE_CATALOG_ENQUIRY_PRO_TEXT_DOMAIN')) exit;

// Woocommerce active check
if(!Woocommerce_Catalog_Enquiry_Pro_Dependencies::woocommerce_active_check()) {
  	
  	add_action( 'admin_notices', 'woocommerce_catalog_enquiry_alert_notice' );

}
// Woocommerce catalog enquiry active check
if(!Woocommerce_Catalog_Enquiry_Pro_Dependencies::woocommerce_catalog_enquiry_active_check()) {
  	
  	add_action( 'admin_notices', 'woocommerce_catalog_enquiry_not_active_alert_notice' );
}

// Migration at activation hook
register_activation_hook(__FILE__, 'woocommerce_catalog_enquiry_pro_migration_1_to_2');
add_action( 'upgrader_process_complete', 'woocommerce_catalog_enquiry_pro_migration_1_to_2' );

/**
* Plugin page links
*/
function mvx_woocommerce_catalog_enquiry_plugin_links( $links ) {	
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=woo-catalog' ) . '">' . __( 'Settings', WOOCOMMERCE_CATALOG_ENQUIRY_PRO_TEXT_DOMAIN ) . '</a>',
		'<a href="https://multivendorx.com/support-forum/forum/mvx-catalog-enquiry/">' . __( 'Support', WOOCOMMERCE_CATALOG_ENQUIRY_PRO_TEXT_DOMAIN ) . '</a>',			
	);	
	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mvx_woocommerce_catalog_enquiry_plugin_links' );

if(!class_exists('Woocommerce_Catalog_Enquiry_Pro') && Woocommerce_Catalog_Enquiry_Pro_Dependencies::woocommerce_active_check() && Woocommerce_Catalog_Enquiry_Pro_Dependencies::woocommerce_catalog_enquiry_active_check()) {

	require_once( trailingslashit(dirname(__FILE__)).'classes/class-woocommerce-catalog-enquiry-pro.php' );
	global $Woocommerce_Catalog_Enquiry_Pro;
	$Woocommerce_Catalog_Enquiry_Pro = new Woocommerce_Catalog_Enquiry_Pro( __FILE__ );
	$GLOBALS['Woocommerce_Catalog_Enquiry_Pro'] = $Woocommerce_Catalog_Enquiry_Pro;
	add_action( 'plugins_loaded', 'woocommerce_catalog_enquiry_session_init' );

}

function woocommerce_catalog_enquiry_session_init(){

	require_once trailingslashit(dirname(__FILE__)).'includes/class-woocommerce-catalog-enquiry-pro-session.php';
    require_once trailingslashit(dirname(__FILE__)).'includes/class-woocommerce-catalog-enquiry-pro-cart.php';
    global $Woocommerce_Catalog_Enquiry_Pro_Cart;
	$Woocommerce_Catalog_Enquiry_Pro_Cart = new Woocommerce_Catalog_Enquiry_Pro_Cart();
	$GLOBALS['Woocommerce_Catalog_Enquiry_Pro_Cart'] = $Woocommerce_Catalog_Enquiry_Pro_Cart;

}
