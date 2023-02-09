<?php
/**
 * The template for displaying enquiry cart data
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/shortcode/woocommerce-catalog-enquiry-pro-cart.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $Woocommerce_Catalog_Enquiry_Pro, $woocommerce, $Woocommerce_Catalog_Enquiry_Pro_Cart;
$settings = get_woocommerce_catalog_catalog_settings();
// MVX
$is_vendor_product = false;

do_action( 'woocommerce_catalog_before_enquiry_cart' ); 
?>
<div class="woocommerce wcce-cart-wrapper">
	<div id="wcce-enquiry-cart-message"></div>
	<?php 
	if( count($enquiry_data) == 0):
	?>
        <p class="enquiry-cart-empty"><?php  echo apply_filters('woocommerce_catalog_enquiry_cart_is_empty_text', __('Enquiry Cart is Empty!', 'woocommerce-catalog-enquiry-pro')); ?></p>
	<p class="return-to-shop">
		<a class="button wc-backward" href="<?php echo apply_filters('woocommerce_catalog_enquiry_cart_return_to_shop_btn_link', site_url('shop')); ?>"><?php  echo apply_filters('woocommerce_catalog_enquiry_cart_return_to_shop_btn_text',__('Return To Shop', 'woocommerce-catalog-enquiry-pro')); ?></a>
	</p>
	<?php else: ?>
	<form id="wcce-enquiry-cart-form" name="wcce-enquiry-cart-form" action="" method="post">
		<?php do_action( 'woocommerce_catalog_before_enquiry_cart_table' ); ?>
		<table class="woocommerce_catalog_enq_cart shop_table shop_table_responsive cart" id="wcce-enq-cart-table-list" cellspacing="0">
	        <thead>
	            <tr>
	                <th class="product-remove">&nbsp;</th>
	                <th class="product-thumbnail">&nbsp;</th>
	                <th class="product-name"><?php _e( 'Product', 'woocommerce-catalog-enquiry-pro' ) ?></th>
	                <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?>
	                <th class="product-quantity"><?php _e( 'Quantity', 'woocommerce-catalog-enquiry-pro' ) ?></th>
	                <?php } ?>
	                <?php if(!isset($settings['is_remove_price']) && mvx_catalog_get_settings_value($settings['is_remove_price'], 'checkbox') == 'Enable'){ ?>
	                <th class="product-subtotal"><?php _e( 'Price', 'woocommerce-catalog-enquiry-pro' ); ?></th>
	                <?php } ?>
	            </tr>
	        </thead>
			<tbody>
			<?php do_action( 'woocommerce_catalog_before_enquiry_cart_contents' ); ?>
			<?php foreach ( $enquiry_data as $key => $enquiry ):
				$_product = wc_get_product(  isset( $enquiry['variation_id'] ) ? $enquiry['variation_id'] : $enquiry['product_id'] );
        		if( !isset( $_product ) || !is_object($_product) ) continue; ?>
				<tr class="cart_item">
					<td class="product-remove">
						<?php
							echo apply_filters( 'woocommerce_catalog_enquiry_cart_item_remove_link', sprintf( '<a href="#"  data-remove_item="%s" data-wp_nonce="%s"  data-product_id="%d" class="wcce-enquiry-cart-item-remove remove" title="%s">&times;</a>', $key, wp_create_nonce( 'remove-enquiry-cart-' . $_product->get_id() ), $_product->get_id(),  __( 'Remove this item', 'woocommerce-catalog-enquiry-pro' ) ), $key );
						?>
	                    <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ) ?>" class="ajax-loading" alt="loading" width="16" height="16" style="visibility:hidden" />
					</td>

					<td class="product-thumbnail">
						<?php $thumbnail = $_product->get_image(apply_filters('woocommerce_catalog_enquiry_cart_item_thumbnail_size',array(84,84)));

						if ( ! $_product->is_visible() ){
							echo $thumbnail;
                        }else{
                            printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
                        }
						?>
					</td>

					<td class="product-name">
						<a href="<?php echo $_product->get_permalink() ?>"><?php echo $_product->get_name() ?></a>
						<?php 
						if($enquiry['variation']){
							foreach ($enquiry['variation'] as $label => $value) {
								$label = str_replace( 'attribute_pa_', '', $label );
								$label = str_replace( 'attribute_', '', $label );
								echo "<br>".ucfirst($label).": ".ucfirst($value);
							}
						} ?>
					</td>
					<?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?>
					<td class="product-quantity">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="wcce-enquiry-cart-quantity[%s][qty]" value="1" />', $key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "wcce-enquiry-cart-quantity[{$key}][qty]",
									'input_value' => $enquiry['quantity'],
									'max_value'   => $_product->get_max_purchase_quantity(),
									'min_value'   => '0',
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_catalog_enquiry_cart_item_quantity', $product_quantity, $key, $enquiry );
						?>
					</td>
					<?php } ?>
					<?php if(!isset($settings['is_remove_price']) && mvx_catalog_get_settings_value($settings['is_remove_price'], 'checkbox') == 'Enable') { ?>
	                <td class="product-subtotal">
	                    <?php
	                    if(isset($settings['is_replace_price_with_txt']) && mvx_catalog_get_settings_value($settings['is_replace_price_with_txt'], 'checkbox') == "Enable" && isset($settings['replace_text_in_price']) && !empty($settings['replace_text_in_price'])){
	                    	echo $settings['replace_text_in_price'];
                            }else{
	                        echo apply_filters( 'woocommerce_catalog_enquiry_cart_item_price_html' , WC()->cart->get_product_subtotal( $_product, $enquiry['quantity'] ));
                            }
	                    ?>
	                </td>
	                <?php } ?>
				</tr>
			<?php endforeach ?>
	            <?php do_action( 'woocommerce_catalog_after_enquiry_cart_contents' ); ?>
	            <tr>
	            	<td colspan="6" class="enquiry-actions">
	     
                            <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?>
	            		<input type="submit" class="enq_cart_update_btn button" name="update_woocommerce_catalog_enquiry_cart" value="<?php esc_attr_e( 'Update Enquiry Cart', 'woocommerce-catalog-enquiry-pro' ); ?>" />
                            <?php } ?>
	            	</td>
	            </tr>
			</tbody>
		</table>
		<?php do_action( 'woocommerce_catalog_after_enquiry_cart_table' ); ?>
    </form>
 
    <?php
        
        if(isset($settings)) {
                $button_text = isset($settings['enquiry_button_text']) ? $settings['enquiry_button_text'] : __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
                if($button_text==''){
                        $button_text = __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
                }
        }else{
                $button_text = __('Send an Enquiry','woocommerce-catalog-enquiry-pro');
        }
        ?>
        <style>
        <?php echo isset($settings['custom_enquiry_buttons_css']) ? $settings['custom_enquiry_buttons_css'] : ''; ?>
        </style>  
        <div id="woocommerce-catalog-pro">
                <?php if(isset($settings['is_button']) && mvx_catalog_get_settings_value($settings['is_button'], 'checkbox') == "Enable" && !empty($settings['custom_enquiry_buttons_css'])) { ?>
                        <button class="woocommerce-catalog-send-enquiry custom_enquiry_buttons_css demo button btn btn-primary btn-large" href="#responsive"><?php echo $button_text;?></button>
                        <?php
                }else { ?>
        <button class="woocommerce-catalog-send-enquiry demo button btn btn-primary btn-large" href="#responsive"><?php echo $button_text; ?></button>
        <?php }
        // Enquiry Form
                $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-multi-product-form-template.php');
                ?>
        </div>
    <?php endif; ?>
</div>
<?php do_action( 'woocommerce_catalog_after_enquiry_cart' ); ?>