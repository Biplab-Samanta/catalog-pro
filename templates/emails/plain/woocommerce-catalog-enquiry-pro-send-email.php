<?php
/**
 * WCCE Send Email 
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/emails/plain/woocommerce-catalog-enquiry-pro-send-email.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $Woocommerce_Catalog_Enquiry_Pro,$woocommerce,$Woocommerce_Catalog_Enquiry_Pro_Cart;

echo $email_heading . "\n\n";

echo sprintf( __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro') ) . "\n\n";

echo "\n****************************************************\n\n";
echo __('User Name : ','woocommerce-catalog-enquiry-pro').$enquiry_data['user_name']."\n\n";
echo __('User Email : ','woocommerce-catalog-enquiry-pro').$enquiry_data['user_email']."\n\n";

if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
    foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
        echo '<strong>'.$field['label'].':</strong><br>'.$field['value']."\n\n";
		} 
	} 
}
echo "\n****************************************************\n\n";
if($enquiry_data['enquiry_action_type'] == 'multiple'){
	if(is_object($product_data)){
		foreach($product_data as $key =>$pro){
			$product = wc_get_product( $pro->product_id );

			echo "\n Product Name : ".$product->get_name()."\n\n";
			if($product->get_type() == 'variation'){
              foreach ($product->get_attributes() as $label => $value) {
                echo "\n".ucfirst(wc_attribute_label($label)).": ".ucfirst($value)."\n";
              }
            }
		if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){
			echo "Quantity : ".$pro->quantity."\n\n";
		}
			echo "\n\n Product link : ".$product->get_permalink();
                do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product);
		echo "\n****************************************************\n\n";
		}
	}
}else{
    $product = wc_get_product( $product_data );

    echo "\n Product Name : ".$product->get_name()."\n\n";
    if($product->get_type() == 'variation'){
      foreach ($product->get_attributes() as $label => $value) {
        echo "\n".ucfirst(wc_attribute_label($label)).": ".ucfirst($value)."\n";
      }
    }
    if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){
		echo "Quantity : ".$enquiry_data['product_quantity']."\n\n";
	}
    echo "\n\n Product link : ".$product->get_permalink();
    do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product);
}

echo apply_filters( 'woocommerce_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) );