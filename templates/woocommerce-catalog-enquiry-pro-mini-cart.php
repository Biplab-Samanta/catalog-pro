<?php
/**
 * Mini-enquiry cart
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-mini-cart.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $Woocommerce_Catalog_Enquiry_Pro_Cart;
?>

<?php do_action( 'woocommerce_catalog_before_mini_enquiry_cart_contents' ); ?>

<ul class="enquiry_cart_list product_list_widget">

    <?php if ( ! $Woocommerce_Catalog_Enquiry_Pro_Cart->is_empty_enquiry() ) : ?>

        <?php 
            foreach ( $Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data() as $enquiry_cart_item_key => $enquiry_cart_item ) { 
                $_product = wc_get_product(  isset( $enquiry_cart_item['variation_id'] ) ? $enquiry_cart_item['variation_id'] : $enquiry_cart_item['product_id'] );

                if ( $_product && $_product->exists() && apply_filters( 'woocommerce_catalog_widget_enquiry_cart_item_visible', true, $enquiry_cart_item, $enquiry_cart_item_key ) ) {
                    $product_name      = apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_name', $_product->get_name(), $enquiry_cart_item, $enquiry_cart_item_key );
                    $thumbnail         = apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_thumbnail', $_product->get_image(), $enquiry_cart_item, $enquiry_cart_item_key );
                    $product_price     = apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_price', wc_price( $_product->get_price() ), $enquiry_cart_item, $enquiry_cart_item_key );
                    $product_permalink = apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink() : '', $enquiry_cart_item, $enquiry_cart_item_key );
                    ?>
                    <li class="mini_enquiry_cart_item <?php echo esc_attr( apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_class', 'enquiry_cart', $enquiry_cart_item, $enquiry_cart_item_key ) ); ?>">
                        <?php
                        echo apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_remove_link', sprintf(
                            '<a href="#" class="remove" aria-label="%s" data-remove_item="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                            __( 'Remove this item', 'woocommerce-catalog-enquiry-pro' ),
                            $enquiry_cart_item_key,
                            esc_attr( $_product->get_id() ),
                            esc_attr( $_product->get_sku() )
                        ), $enquiry_cart_item_key );
                        ?>
                        <?php if ( ! $_product->is_visible() ) : ?>
                            <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
                        <?php else : ?>
                            <a href="<?php echo esc_url( $product_permalink ); ?>">
                                <?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
                            </a>
                        <?php endif; ?>
                        <?php 
                        if($enquiry_cart_item['variation']){
                            echo '<dl class="variation">';
                            $var_info = array();
                            foreach ($enquiry_cart_item['variation'] as $label => $value) {
                                $label = str_replace( 'attribute_pa_', '', $label );
                                $label = str_replace( 'attribute_', '', $label );
                                $var_info[] = "<strong>".ucfirst($label).":</strong> ".ucfirst($value);
                            }
                            echo implode( '<br/>', $var_info );
                            echo '</dl>';
                        } ?>
                        
                        <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?>
                        <?php echo apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $enquiry_cart_item['quantity'], $product_price ) . '</span>', $enquiry_cart_item, $enquiry_cart_item_key ); ?>
                        <?php }else{ ?>
                        <?php echo apply_filters( 'woocommerce_catalog_mini_enquiry_cart_item_quantity', '<span class="quantity">' . sprintf( '%s', $product_price ) . '</span>', $enquiry_cart_item, $enquiry_cart_item_key ); ?>
                        <?php } ?>
                    </li>
                    <?php
                }
            }
        ?>

        <?php do_action( 'woocommerce_catalog_mini_enquiry_cart_item_contents' ); ?>

    <?php else : ?>

        <li class="empty"><?php _e( 'No products in the enquiry cart.', 'woocommerce-catalog-enquiry-pro' ); ?></li>

    <?php endif; ?>

</ul><!-- end product list -->

<?php if ( ! $Woocommerce_Catalog_Enquiry_Pro_Cart->is_empty_enquiry() ) : ?>

    <?php do_action( 'woocommerce_catalog_mini_enquiry_cart_before_buttons' ); ?>

    <p class="woocommerce-catalog-mini-enquiry-buttons buttons">
        <?php 
        /**
        * woocommerce_catalog_mini_enquiry_cart_buttons hook.
        *
        * @hooked woocommerce_catalog_mini_enquiry_cart_view_button - 10
        */
        do_action( 'woocommerce_catalog_mini_enquiry_cart_buttons' ); 
        ?>
    </p>

<?php endif; ?>

<?php do_action( 'woocommerce_catalog_after_mini_enquiry_cart_contents' );