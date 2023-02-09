<?php
/**
 * MVX Catalog Enquiry Cart Button Section
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-cart-button-template.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$customer_details = get_customer_enquiry_details( get_current_user_id() );
$product_id_en = array();
if ( is_user_logged_in() ) {
    foreach ($customer_details as $key => $value) { 
        $product_id_en[] = get_post_meta( $value ,'_enquiry_product', true );
    }
}

?>

<div class="wcce-enquiry-cart add-enquiry-cart-<?php echo $product_id ?>">
    <div class="wcce-enquiry-add-button <?php echo ( $exists ) ? 'hide': 'show' ?>" style="display:<?php echo ( $exists ) ? 'none': 'block' ?>">
        <?php if ( !in_array($product_id, $product_id_en) ) { ?>
            <a href="#" class="<?php echo $class.$btn_style ?>" data-product_id="<?php echo $product_id ?>" data-wp_nonce="<?php echo $wpnonce ?>">
    		    <?php echo $label ?>
    		</a>
            <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ) ?>" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />
        <?php } else {

        ?>  <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'/enquiry'; ?>"  ><?php apply_filters( 'woocommerce_product_in_enquiry', _e('This product is already in your Enquiry list','woocommerce-catalog-enquiry-pro') ); ?></a>
        <?php    
        } 
        ?>
    </div>
    <?php if ( $exists ): ?>
    <?php if (apply_filters('woocommerce_catalog_show_added_to_enquiry_cart_message', true)){ ?>
        <div class="woocommerce_catalog_enquiry_add_item_response-<?php echo $product_id ?> woocommerce_catalog_enquiry_add_item_response_message"><?php echo apply_filters( 'woocommerce_catalog_enquiry_product_in_list', __('The product is already in Enquiry Cart!', 'woocommerce-catalog-enquiry-pro' ) )?></div>
    <?php } ?>
        <div class="woocommerce_catalog_enquiry_add_item_view_cart_list-<?php echo $product_id ?> woocommerce_catalog_enquiry_add_item_view_cart_message"><a class="added_to_cart wc-forward <?php echo $btn_style; ?>" href="<?php echo $enquiry_cart_url ?>"><?php echo $label_view_cart ?></a></div>
    <?php endif ?>
</div>

<div class="clear"></div>