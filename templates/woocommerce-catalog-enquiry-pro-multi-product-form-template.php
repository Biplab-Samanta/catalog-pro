<?php
/**
 * MVX Catalog Multiple Enquiry Form
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-multi-product-form-template.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $Woocommerce_Catalog_Enquiry_Pro, $woocommerce, $product, $Woocommerce_Catalog_Enquiry_Pro_Cart;
$settings = get_woocommerce_catalog_catalog_settings();

$current_user = wp_get_current_user();
if(isset($settings['is_disable_popup']) && mvx_catalog_get_settings_value($settings['is_disable_popup'], 'checkbox') == "Enable" ) {
?>

	<div id="responsive"  class="catalog_enquiry_form" tabindex="-1" style="width:100%;">
		<form id="woocommerce-enquiry-form" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="product_data_for_enquiry" id="product-data-for-enquiry" value='<?php echo json_encode($Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data()); ?>' />
			<input type="hidden" name="product_enquiry_action" id="product-enquiry-action" value="multiple" />
			<div class="modal-header">
			<?php if( isset($settings['is_override_form_heading']) && mvx_catalog_get_settings_value($settings['is_override_form_heading'], 'checkbox') == "Enable" ) { ?>
				<?php if( isset($settings['custom_static_heading'])) { ?>
					<h2 style="font-size:20px;"><?php echo  $settings['custom_static_heading']; ?></h2>
				<?php }?>
			<?php } else{?>
				<h2 style="font-size:20px;"><?php echo __('Multiple Enquiry','woocommerce-catalog-enquiry-pro')?></h2>
			<?php }?>
			</div>
			<div class="modal-body woocommerce_catalog_enq_form_wrapper">
				<div class="row-fluid">
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
				</div>
			</div>
			<div class="modal-footer">		
				<input type="submit" id="woocommerce_catalog_enq_submit" class="btn btn-primary" value="<?php echo __('Send', 'woocommerce-catalog-enquiry-pro');?>"/>
			</div>
		</form>
	</div>
<?php }else{ ?>
	<div id="responsive"  class="catalog-modal">
		<form id="woocommerce-enquiry-form" role="form" method="post" enctype="multipart/form-data">
			<input type="hidden" name="product_data_for_enquiry" id="product-data-for-enquiry" value='<?php echo json_encode($Woocommerce_Catalog_Enquiry_Pro_Cart->get_enquiry_data()); ?>' />
			<input type="hidden" name="product_enquiry_action" id="product-enquiry-action" value="multiple" />
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close">&times;</button>
					<?php if( isset($settings['is_override_form_heading']) && mvx_catalog_get_settings_value($settings['is_override_form_heading'], 'checkbox') == "Enable" ) { ?>
						<?php if( isset($settings['custom_static_heading'])) { ?>
							<h2 style="font-size:20px;"><?php echo  $settings['custom_static_heading']; ?></h2>
						<?php }?>
					<?php } else{?>
					<h2 style="font-size:20px;"><?php echo __('Multiple Enquiry','woocommerce-catalog-enquiry-pro')?></h2>
					<?php }?>
				</div>
				<div class="modal-body woocommerce_catalog_enq_form_wrapper">
					<div class="row-fluid">
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
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default"><?php echo __('Close','woocommerce-catalog-enquiry-pro');?></button>
					<input type="submit" id="woocommerce_catalog_enq_submit" class="btn btn-primary" value="<?php echo __('Send', 'woocommerce-catalog-enquiry-pro');?>"/>
				</div>
			</div>
		</form>
	</div>
<?php }