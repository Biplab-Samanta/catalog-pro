<?php
/**
 * MVX Catalog Single Enquiry Form
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-form-template.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $Woocommerce_Catalog_Enquiry_Pro, $woocommerce, $post, $product, $Woocommerce_Catalog_Enquiry_Pro_Cart;
$settings = get_woocommerce_catalog_catalog_settings();
// MVX
$is_vendor_product = false;
if(is_active_MVX()){
	if(is_object($post)){
            $product_author = get_mvx_product_vendors($post->ID);
            if($product_author){
                $is_vendor_product = is_user_mvx_vendor($product_author->id);
                $settings = get_woocommerce_catalog_catalog_settings($product_author->id);
            }
	}
}

$productid = $post->ID;
$current_user = wp_get_current_user();
$product_name = get_post_field('post_title',$productid);
$product_url = get_permalink($productid);


$customer_details = get_customer_enquiry_details( $current_user->ID );
$product_id = array();
if ( is_user_logged_in() ) {
	foreach ($customer_details as $key => $value) { 
		$product_id[] = get_post_meta( $value ,'_enquiry_product', true );
	}
}
              		

if(isset($settings['is_disable_popup']) && $settings['is_disable_popup'] == "Enable" ) {
?>
	<div id="responsive"  class="catalog_enquiry_form" tabindex="-1" style="width:100%;">
		<form id="woocommerce-enquiry-form" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="product_data_for_enquiry" id="product-data-for-enquiry" value="<?php echo $post->ID; ?>" />
			<input type="hidden" name="product_enquiry_action" id="product-enquiry-action" value="single" />
			<div class="modal-header">
			<?php if( isset($settings['is_override_form_heading']) && mvx_catalog_get_settings_value($settings['is_override_form_heading'], 'checkbox') == "Enable" ) { ?>
				<?php if( isset($settings['custom_static_heading'])) { ?>
					<h2 style="font-size:20px;"><?php echo  $settings['custom_static_heading']; ?></h2>
				<?php }?>
			<?php } else{?>
				<h2 style="font-size:20px;"><?php echo __('Enquiry about ','woocommerce-catalog-enquiry-pro')?> <?php echo $product_name; ?></h2>
			<?php }?>
			</div>
			<div class="modal-body woocommerce_catalog_enq_form_wrapper">
				<div class="row-fluid">
					<?php if( !in_array($productid, $product_id) ) { ?>
					<div class="span12">
						<?php if( isset($settings['top_content_form']) && !empty($settings['top_content_form'])) { echo '<p>'.$settings['top_content_form'].'</p>'; }?>
						
						<div class="woocommerce-catalog-enquiry-massage"></div>
						<div class="wcce-enq-12">
		                    <label for="enq_user_name"><?php echo apply_filters('wcce_enquiry_form_name_field_label',__('Name ','woocommerce-catalog-enquiry-pro')); ?> <span class="required">*</span></label>
		                    <input type="text"  name="enq_user_name" id="enq_user_name" value="<?php echo $current_user->display_name; ?>" required="required" />
		                </div>

		                <div class="wcce-enq-12">
		                    <label for="enq_user_email"><?php echo apply_filters('wcce_enquiry_form_email_field_label',__('Email ','woocommerce-catalog-enquiry-pro')); ?> <span class="required">*</span></label>
		                    <input type="text"  name="enq_user_email" id="enq_user_email" value="<?php echo $current_user->user_email; ?>" required="required" />
		                </div>

		                <?php do_action('wcce_enquiry_form_fields'); ?>
            			<div class="clearboth"></div>
						
						<?php if(isset($settings['bottom_content_form']) && !empty($settings['bottom_content_form'])) { echo '<p>'.$settings['bottom_content_form'].'</p>'; } ?>
					</div>
					<?php } else {
						echo apply_filters( 'woocommerce_product_in_enquiry', _e('This product is already in your Enquiry list','woocommerce-catalog-enquiry-pro') );
						?>
						<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'/enquiry'; ?>" ><?php _e('My Enquiry','woocommerce-catalog-enquiry-pro'); ?></a>
						<?php
					} ?>
				</div>
			</div>
			<div class="modal-footer">
			<?php if( !in_array($productid, $product_id) ) { ?>
				<input type="submit" id="woocommerce_catalog_enq_submit" class="btn btn-primary" value="<?php echo __('Send', 'woocommerce-catalog-enquiry-pro');?>"/>
			<?php } ?>		
			</div>
		</form>
	</div>
<?php }else{ ?>
	
	<div id="responsive"  class="catalog-modal">
		<form id="woocommerce-enquiry-form" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="product_data_for_enquiry" id="product-data-for-enquiry" value="<?php echo $post->ID; ?>" />
			<input type="hidden" name="product_enquiry_action" id="product-enquiry-action" value="single" />
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close">&times;</button>
					<?php if( isset($settings['is_override_form_heading']) && mvx_catalog_get_settings_value($settings['is_override_form_heading'], 'checkbox') == "Enable" ) { ?>
						<?php if( isset($settings['custom_static_heading'])) { ?>
							<h2 style="font-size:20px;"><?php echo str_replace("PRODUCT_NAME", $product_name, $settings['custom_static_heading']); ?></h2>
						<?php }?>
					<?php } else{?>
					<h2 style="font-size:20px;"><?php echo __('Enquiry about ','woocommerce-catalog-enquiry-pro')?> <?php echo $product_name; ?></h2>
					<?php }?>
				</div>
				<div class="modal-body woocommerce_catalog_enq_form_wrapper">
					<div class="row-fluid">
					<?php if( !in_array($productid, $product_id) ) { ?>
						<div class="span12">
							<?php if( isset($settings['top_content_form']) && !empty($settings['top_content_form'])) { echo '<p>'.$settings['top_content_form'].'</p>'; }?>
							
							<div class="woocommerce-catalog-enquiry-massage"></div>
							<div class="wcce-enq-12">
			                    <label for="enq_user_name"><?php echo apply_filters('wcce_enquiry_form_name_field_label',__('Name ','woocommerce-catalog-enquiry-pro')); ?> <span class="required">*</span></label>
			                    <input type="text"  name="enq_user_name" id="enq_user_name" value="<?php echo $current_user->display_name; ?>" required="required" />
			                </div>

			                <div class="wcce-enq-12">
			                    <label for="enq_user_email"><?php echo apply_filters('wcce_enquiry_form_email_field_label',__('Email ','woocommerce-catalog-enquiry-pro')); ?> <span class="required">*</span></label>
			                    <input type="text"  name="enq_user_email" id="enq_user_email" value="<?php echo $current_user->user_email; ?>" required="required" />
			                </div>

			                <?php do_action('wcce_enquiry_form_fields'); ?>
	            			<div class="clearboth"></div>
							
							<?php if(isset($settings['bottom_content_form']) && !empty($settings['bottom_content_form'])) { echo '<p>'.$settings['bottom_content_form'].'</p>'; } ?>
						</div>
						<?php } else {
							echo apply_filters( 'woocommerce_product_in_enquiry', _e('This product is already in your Enquiry list','woocommerce-catalog-enquiry-pro') );
							?>
							<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'/enquiry'; ?>" ><?php _e('My Enquiry','woocommerce-catalog-enquiry-pro'); ?></a>
							<?php
						} ?>
					</div>
				</div>
				<div class="modal-footer">
					<?php if( !in_array($productid, $product_id) ) { ?>
					<input type="submit" id="woocommerce_catalog_enq_submit" class="btn btn-primary" value="<?php echo __('Send', 'woocommerce-catalog-enquiry-pro');?>"/>
					<?php } ?>
				</div>
			</div>
		</form>
	</div>
<?php }