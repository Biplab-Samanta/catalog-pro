<?php
/**
 * WCCE Send Email 
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/emails/woocommerce-catalog-enquiry-pro-send-email.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $Woocommerce_Catalog_Enquiry_Pro,$woocommerce,$Woocommerce_Catalog_Enquiry_Pro_Cart;

$is_vendor_email = isset($enquiry_data['send_vendor_email']) ? $enquiry_data['send_vendor_email'] : false;

if($email_tpl == 1){					
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 1 ****//
                          /*-------------------------------------------------------------------------*/

	if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"><?php echo __('Multiple Product Enquiry by','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 46px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
      <div style="width: 100%; border-bottom: 1px solid #fff; height: 10px;margin-bottom: 10px; text-align: center;"><span><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/divider_box.png'; ?>"></span>
      </div>
      <p style="margin-bottom: 2px;"> &nbsp;</p>
      <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 20px;display: inline-block;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
     </div>
   </div>
  <div style="width: 550px; padding: 0px 25px 0; background: #fdfdfd;">
    <div style="width: 550px; background-color: #f75d5d; position: relative;">
      <div style="position: absolute; top: -1px; left: 16px; width: auto;"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/arrow_red.png'; ?>" alt=""></div>
      <table width="100%" cellspacing="10" cellpadding="10" border="0">
        <tr>
          <td valign="top" align="left" style="width: 49%; border-right: 1px solid #fff">
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
               <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic1.png'; ?>" alt=""></td>
               <td valign="bottom" align="left">
                <?php if (!$is_vendor_email) { ?>
                  <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</p>
                  <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo $enquiry_data['user_name']?></h3>
                <?php } ?>
              </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr>
             <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic2.png'; ?>" alt=""></td>
             <td valign="bottom" align="left">
              <?php if (!$is_vendor_email) { ?>
                <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Email','woocommerce-catalog-enquiry-pro');?>:<br>
                <?php echo $enquiry_data['user_email']?></p>
              <?php } ?>           
              </td>
            </tr>
          </table>
        </td>
        <td valign="top" align="left" style="width: 49%;">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
            <tr>
             <td valign="bottom" align="left">
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;margin-bottom: 10px;"><?php echo '<strong>'.$field['label'].':</strong><br>'.$field['value']; ?>
			        </p>
              </td>
            </tr>
          <?php } } } ?>
          </table>
        </td>
      </tr>
    </table>
  </div>
</div>
<div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd;">
  <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0 30px 0">
    <table width="100%" cellspacing="10" cellpadding="0" border="0">
	<?php if(is_object($product_data)){
		$k = 0;
		foreach($product_data as $key =>$pro){
			if ($k % 2 == 0) echo '<tr>'; 
			$product = wc_get_product( $pro->product_id );
			?>
		   	<td valign="top" align="left" style="width: 49%;">
          	<table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td valign="top" align="left" style="width: auto;"><?php //echo $product->get_image(array(64,64)); 
                $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                echo '<img src="'.$pro_img[0].'">';
               ?></td>
              <td valign="top" align="left" style="">
                <p style="color: #f75d5d; font-family: Arial; font-size: 14px; line-height: 16px; padding: 0 0 0 8px; margin: 0"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:<br>
                  <strong><?php echo $product->get_name(); ?></strong>
                  <?php if($product->get_type() == 'variation'){
                      if(isset($pro->variation) && count($pro->variation) > 0 ){
                        foreach ($pro->variation as $label => $value) {
                            $label = str_replace( 'attribute_pa_', '', $label );
                            $label = str_replace( 'attribute_', '', $label );
                            echo "<br>".ucfirst($label).": ".ucfirst($value);
                        } 
                      }else{
                        foreach ($product->get_attributes() as $label => $value) {
                            echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                        }
                      }
                  } ?>
                  <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></span><?php } ?></p>
              </td>
            </tr>
            </table>  
             <p style="color: #424242; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 0 0 0 0; margin: 0"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>       
                <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>             
           	</td>
		    <?php if ($k % 2 == 1) echo '</tr>';
		    $k++;
		}
		if ($k % 2 != 0){
		    echo '</tr>';
		}
	} ?>
         </table>
       </div>
     </div>
     <div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd; border-top: 1px solid #ccc">
      <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0; font-family: Arial; text-align: center; color: #aaa; font-size: 12px">
        <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
      </div>
    </div>
  </div>
</body>
<?php 
	}else{						//**** Single Product ****//

      $product = wc_get_product( $product_data );

?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"><?php echo __('Product Enquiry For','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 46px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"> <?php echo $product->get_title(); ?></h1>
      <h4 style="color: white; font-family: Arial; font-size: 16px; font-weight: 400; line-height: 40px; text-transform: uppercase; margin: 0;padding: 0 0 10px 0; text-align: center;"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['user_name']?></h4>
      <div style="width: 100%; border-bottom: 1px solid #fff; height: 10px;margin-bottom: 10px; text-align: center;"><span><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/divider_box.png'; ?>"></span>
      </div>
      <p style="margin-bottom: 2px;"> &nbsp;</p>
      <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 20px;display: inline-block;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #fd7373;">
      <h2 style="font-family: Arial; line-height: 43px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0 20px;"> <?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="color: #ffffff; font-size: 36px; font-weight: 700; text-transform: uppercase; font-family: Arial; margin: 0; line-height: 37px; padding: 0px 0 0 20px;"><?php echo $product->get_formatted_name(); ?></h1>
      <p style="margin: 0; padding: 6px 0 20px 20px"><a href="<?php echo $product->get_permalink(); ?>" style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; margin: 0; text-decoration: underline; padding: 0px 0 0 0px;"><?php echo __('Find the product link','woocommerce-catalog-enquiry-pro');?>
      </a></p>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background: #fdfdfd;">
    <div style="width: 550px; background-color: #f75d5d; position: relative;">
      <div style="position: absolute; top: -1px; left: 16px; width: auto;"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/arrow_red.png'; ?>" alt=""></div>
      <table width="100%" cellspacing="10" cellpadding="10" border="0">
        <tr>
          <td valign="top" align="left" style="width: 49%; border-right: 1px solid #fff">
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
               <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic1.png'; ?>" alt=""></td>
               <td valign="bottom" align="left">
                <?php if (!$is_vendor_email) { ?>
                  <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</p>
                  <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo $enquiry_data['user_name']?></h3>
                <?php } ?>
              </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr>
             <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic2.png'; ?>" alt=""></td>
             <td valign="bottom" align="left">
              <?php if (!$is_vendor_email) { ?>
                <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Email','woocommerce-catalog-enquiry-pro');?>:<br>
                <?php echo $enquiry_data['user_email']?></p>
              <?php } ?>           
              </td>
            </tr>
          </table>
        </td>
        <td valign="top" align="left" style="width: 49%;">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <?php if($enquiry_data['user_enquiry_fields'] != '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
            <tr>
             <td valign="bottom" align="left">
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;margin-bottom: 10px;"><?php echo '<strong>'.$field['label'].':</strong><br>'.$field['value']; ?>
              </p>
              </td>
            </tr>
          <?php } } } ?>
          </table>
        </td>
      </tr>
    </table>
  </div>
</div>
<div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd;">
  <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0 30px 0">
    <table width="100%" cellspacing="10" cellpadding="0" border="0">
      <tr>
        <td valign="top" align="left" style="width: 49%;">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td valign="top" align="left" style="width: 20%;">
              <?php //echo $product->get_image(array(64,64)); 
                $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                echo '<img src="'.$pro_img[0].'">';
              ?>
              </td>
              <td valign="top" align="left" style="">
                <p style="color: #f75d5d; font-family: Arial; font-size: 14px; line-height: 16px; padding: 0 0 0 8px; margin: 0"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:<br>
                  <strong><?php echo $product->get_name(); ?></strong>
                  <?php if($product->get_type() == 'variation'){
                      if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
                        foreach ($enquiry_data['product_variations'] as $label => $value) {
                            $label = str_replace( 'attribute_pa_', '', $label );
                            $label = str_replace( 'attribute_', '', $label );
                            echo "<br>".ucfirst($label).": ".ucfirst($value);
                        } 
                      }else{
                        foreach ($product->get_attributes() as $label => $value) {
                          echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                        }
                      }
                  } ?>
                  <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></span><?php } ?></p>
                 </td>
               </tr>
             </table>  
             <p style="color: #424242; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 0 0 0 0; margin: 0"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>       
            <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
           </td>
           </tr>
         </table>
       </div>
     </div>
     <div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd; border-top: 1px solid #ccc">
      <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0; font-family: Arial; text-align: center; color: #aaa; font-size: 12px">
        <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
      </div>
    </div>
  </div>
</body>	
<?php	}

}elseif($email_tpl == 2){				
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 2 ****//
                          /*-------------------------------------------------------------------------*/

  if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
?>
  <body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <p style="text-align: center; padding: 25px 0 2px 0; margin: 0"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/envelop_shdow.png'; ?>" alt=""></p>
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Multiple Product Enquiry by','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 46px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #5ba5af;">
     <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 0px;display: inline-block;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background: #4d96a0 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/envelop_bg.png'; ?>) right bottom no-repeat;">
    <div style="width: 550px; position: relative; padding: 16px 0;">
      
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td valign="top" align="left" style="width: 49%;">
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
               <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic1.png'; ?>" alt=""></td>
               <td valign="bottom" align="left">
                <?php if (!$is_vendor_email) { ?>
                  <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</p>
                  <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo $enquiry_data['user_name']?></h3>
                <?php } ?>
              </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr>
             <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic2.png'; ?>" alt=""></td>
             <td valign="bottom" align="left">
              <?php if (!$is_vendor_email) { ?>
                <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Email','woocommerce-catalog-enquiry-pro');?>:<br>
                <?php echo $enquiry_data['user_email']?></p>
              <?php } ?>           
              </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
          </table>
        </td>
        <td valign="top" align="left" style="width: 49%;">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
           <tr>
             <td valign="bottom" align="left">
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;margin-bottom: 10px;"><?php echo '<strong>'.$field['label'].':</strong><br>'.$field['value']; ?></p>           
              </td>
            </tr>
          <?php } } } ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
          </table>
        </td>
      </tr>
    </table>
    
  </div>
</div>
<div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
  <div style="width: 550px; padding: 30px 0 40px 0">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <?php if(is_object($product_data)){
      $k = 0;
      foreach($product_data as $key =>$pro){
      if ($k % 2 == 0) echo '<tr>'; 
      $product = wc_get_product( $pro->product_id );
      ?>
        <td valign="top" align="left" style="width: 41%; padding-right: 8%">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
              <td valign="middle" align="left" style="width: auto; border-right: 1px solid #fdfdfd;"><?php //echo $product->get_image(array(64,64)); 
                $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                echo '<img src="'.$pro_img[0].'">';
               ?></td>
              <td valign="top" align="left" style="">
                <p style="color: #fff; font-family: Arial; font-size: 14px; line-height: 16px; padding: 0 0 0 8px; margin: 0"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:<br>
                  <strong><?php echo $product->get_name(); ?></strong>
                  <?php if($product->get_type() == 'variation'){
                    if(isset($pro->variation) && count($pro->variation) > 0 ){
                        foreach ($pro->variation as $label => $value) {
                            $label = str_replace( 'attribute_pa_', '', $label );
                            $label = str_replace( 'attribute_', '', $label );
                            echo "<br>".ucfirst($label).": ".ucfirst($value);
                        } 
                    }else{
                        foreach ($product->get_attributes() as $label => $value) {
                            echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                        }
                    }
                  } ?>
                  <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></span><?php } ?></p>
                 </td>
               </tr>
             </table>  
             <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>       
                             <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
           </td>
          <?php if ($k % 2 == 1) echo '</tr>';
        $k++;
    }
    if ($k % 2 != 0){
        echo '</tr>';
    }
  } ?>
         </table>
       </div>
     </div>
     <div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd; border-top: 1px solid #ccc; border-bottom: 5px solid #f75d5d;">
      <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0; font-family: Arial; text-align: center; color: #aaa; font-size: 12px">
        <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
      </div>
    </div>
  </div>
</body>
<?php
  }else{                                              //**** Single Product ****//

      $product = wc_get_product( $product_data );
?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <p style="text-align: center; padding: 25px 0 2px 0; margin: 0"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/envelop_shdow.png'; ?>" alt=""></p>
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Product Enquiry For','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 46px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"> <?php echo $product->get_title(); ?></h1>
      <h4 style="color: white; font-family: Arial; font-size: 16px; font-weight: 400; line-height: 40px; text-transform: uppercase; margin: 0;padding: 0 0 10px 0; text-align: center;"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['user_name']?></h4>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #5ba5af;">
     <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 0px;display: inline-block;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
       <h2 style="font-family: Arial; line-height: 43px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0 0px;"> <?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?></h2>
       <h1 style="color: #ffffff; font-size: 46px; font-weight: 700; text-transform: uppercase; font-family: Arial; margin: 0; line-height: 37px; padding: 0px 0 0 0px;"><?php echo $product->get_formatted_name(); ?></h1>
       <p style="margin: 0; padding: 6px 0 20px 0px"><a href="<?php echo $product->get_permalink(); ?>" style="color: white; font-family: Arial; font-size: 17px; font-weight: 400; line-height: 25px; margin: 0; text-decoration: underline; padding: 0px 0 0 0px;"><?php echo __('Find the product link','woocommerce-catalog-enquiry-pro');?>
       </a></p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; position: relative; padding: 16px 0;">
      
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td valign="top" align="left" style="width: 49%;">
            <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
               <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic1.png'; ?>" alt=""></td>
               <td valign="bottom" align="left">
                <?php if (!$is_vendor_email) { ?>
                  <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</p>
                  <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo $enquiry_data['user_name']?></h3>
                <?php } ?>
              </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr>
             <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic2.png'; ?>" alt=""></td>
             <td valign="bottom" align="left">
              <?php if (!$is_vendor_email) { ?>
                <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Email','woocommerce-catalog-enquiry-pro');?>:<br>
                <?php echo $enquiry_data['user_email']?></p>
              <?php } ?>           
              </td>
            </tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
          </table>
        </td>
        <td valign="top" align="left" style="width: 49%;">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
           <tr>
             <td valign="bottom" align="left">
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;margin-bottom: 10px;"><?php echo '<strong>'.$field['label'].':</strong><br>'.$field['value']; ?></p>           
              </td>
            </tr>
          <?php } } } ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
          </table>
        </td>
      </tr>
    </table>
    
  </div>
</div>
<div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
  <div style="width: 550px; padding: 30px 0 40px 0">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td valign="top" align="left" style="width: 41%; padding-right: 8%">
          <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td valign="middle" align="left" style="width: auto; border-right: 1px solid #fdfdfd;"><?php //echo $product->get_image(array(64,64)); 
                $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                echo '<img src="'.$pro_img[0].'">';
               ?></td>
              <td valign="top" align="left" style="">
                <p style="color: #fff; font-family: Arial; font-size: 14px; line-height: 16px; padding: 0 0 0 8px; margin: 0"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:<br>
                  <strong><?php echo $product->get_name(); ?></strong>
                  <?php if($product->get_type() == 'variation'){
                    if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
                        foreach ($enquiry_data['product_variations'] as $label => $value) {
                            $label = str_replace( 'attribute_pa_', '', $label );
                            $label = str_replace( 'attribute_', '', $label );
                            echo "<br>".ucfirst($label).": ".ucfirst($value);
                        } 
                      }else{
                        foreach ($product->get_attributes() as $label => $value) {
                          echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                        }
                      }
                  } ?>
                  <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></span><?php } ?></p>
                 </td>
               </tr>
             </table>  
             <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                             <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
           </td>
           </tr>
         </table>
       </div>
     </div>
     <div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd; border-top: 1px solid #ccc; border-bottom: 5px solid #f75d5d;">
      <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0; font-family: Arial; text-align: center; color: #aaa; font-size: 12px">
        <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
      </div>
    </div>
  </div>
</body>
<?php  }

}elseif($email_tpl == 3){				
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 3 ****//
                          /*-------------------------------------------------------------------------*/
	
  if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td valign="top" rowspan="2" align="left" style="width: 265px; background: #e2a209; padding: 20px 15px 20px 20px">
        <h2 style="font-family: Arial; line-height: 23px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Multiple Product Enquiry by','woocommerce-catalog-enquiry-pro');?></h2>
        <h1 style="color: white; font-family: Arial; font-size: 36px; font-weight: 700; line-height: 30px; text-transform: uppercase; margin: 0;padding: 0 0 0px 0;  border-bottom: 1px solid #fff"><?php echo $enquiry_data['user_name']?></h1>
        <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 20px; padding: 10px 0 0 0px; display: inline-block;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
      </td>
      <td valign="top" align="left" style="width: 255px; background: #00a8c5 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/mail_top.png'; ?>) right top no-repeat; padding: 20px 20px 0px 25px; border-bottom: 1px solid #fff">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
               <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic1.png'; ?>" alt=""></td>
               <td valign="bottom" align="left">
                <?php if (!$is_vendor_email) { ?>
                  <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</p>
                  <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo $enquiry_data['user_name']?></h3>
                <?php } ?>
              </td>
            </tr>
            <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
            <tr>
             <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic2.png'; ?>" alt=""></td>
             <td valign="bottom" align="left">
              <?php if (!$is_vendor_email) { ?>
                <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Email','woocommerce-catalog-enquiry-pro');?>:<br>
                <?php echo $enquiry_data['user_email']?></p>
              <?php } ?>           
              </td>
            </tr>
            <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
          <?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
            <tr>
             <td valign="bottom" align="left"></td>
             <td valign="bottom" align="left">
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;margin-bottom: 10px;"><?php echo '<strong>'.$field['label'].':</strong><br>'.$field['value']; ?></p>
              </td>
            </tr>
          <?php } } } ?>
          </table>
      </td>
    </tr>
    <tr></tr>
    <?php if(is_object($product_data)){
    $k = 0; $total = count($product_data);
    foreach($product_data as $key =>$pro){
      if ($k % 2 == 0) echo '<tr>'; 
      $product = wc_get_product( $pro->product_id );
    ?>
      <td valign="top" align="left" style="width: 255px; background: #00a8c5; padding: 20px 20px 30px 25px">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td valign="middle" align="left" style="width: auto; border-right: 1px solid #fdfdfd;"><?php //echo $product->get_image(array(64,64)); 
                $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                echo '<img src="'.$pro_img[0].'">';
               ?></td>
                <td valign="top" align="left" style="">
                  <p style="color: #fff; font-family: Arial; font-size: 14px; line-height: 16px; padding: 0 0 0 8px; margin: 0"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:<br>
                    <strong><?php echo $product->get_name(); ?></strong>
                    <?php if($product->get_type() == 'variation'){
                      if(isset($pro->variation) && count($pro->variation) > 0 ){
                        foreach ($pro->variation as $label => $value) {
                            $label = str_replace( 'attribute_pa_', '', $label );
                            $label = str_replace( 'attribute_', '', $label );
                            echo "<br>".ucfirst($label).": ".ucfirst($value);
                        } 
                      }else{
                        foreach ($product->get_attributes() as $label => $value) {
                            echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                        }
                      }
                    } ?>
                    <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></span><?php } ?></p>
            
                   </td>
                 </tr>
               </table>  
               <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p> 
      <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                               </td>
    <?php if ($k % 2 == 1) echo '</tr>';
        $k++;
    }
    if ($k % 2 != 0){
        echo '</tr>';
    }
  } ?>
    <tr>
      <td colspan="2">
        <div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd; border-top: 1px solid #ccc; border-bottom: 5px solid #00a8c5;">
          <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0; font-family: Arial; text-align: center; color: #aaa; font-size: 12px">
            <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
          </div>
        </div>
      </td>
    </tr>
  </table>
  </div>
</body>
<?php
  }else{                                              //**** Single Product ****//

      $product = wc_get_product( $product_data );

?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td valign="top" rowspan="2" align="left" style="width: 265px; background: #e2a209; padding: 20px 15px 20px 20px">
        <h2 style="font-family: Arial; line-height: 23px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> 
          <?php echo __('Product Enquiry For','woocommerce-catalog-enquiry-pro');?></h2>
        <h1 style="font-family: Arial; line-height: 58px; color: #fff; font-size: 58px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"> <?php echo str_replace(' ', '<br>', $product->get_title()); ?></h1>
        <h4 style="color: white; font-family: Arial; font-size: 16px; font-weight: 400; line-height: 30px; text-transform: uppercase; margin: 0;padding: 0 0 0px 0;  border-bottom: 1px solid #fff"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['user_name']?></h4>
        <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 20px; padding: 10px 0 0 0px; display: inline-block;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
       <h3 style="font-family: Arial; line-height: 43px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0 0px;"> <?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
       <h2 style="color: #ffffff; font-size: 36px; font-weight: 700; text-transform: uppercase; font-family: Arial; margin: 0; line-height: 37px; padding: 0px 0 0 0px;"><?php echo $product->get_formatted_name(); ?></h2>
       <p style="margin: 0; padding: 6px 0 2px 0px"><a href="<?php echo $product->get_permalink(); ?>" style="color: white; font-family: Arial; font-size: 17px; font-weight: 400; line-height: 25px; margin: 0; text-decoration: underline; padding: 0px 0 0 0px;"><?php echo __('Find the product link','woocommerce-catalog-enquiry-pro');?>
       </a></p>
      </td>
      <td valign="top" align="left" style="width: 255px; background: #00a8c5 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/mail_top.png'; ?>) right top no-repeat; padding: 20px 20px 0px 25px; border-bottom: 1px solid #fff">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
               <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic1.png'; ?>" alt=""></td>
               <td valign="bottom" align="left">
                <?php if (!$is_vendor_email) { ?>
                  <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</p>
                  <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo $enquiry_data['user_name']?></h3>
                <?php } ?>
              </td>
            </tr>
            <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
            <tr>
             <td valign="bottom" align="left"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/ic2.png'; ?>" alt=""></td>
             <td valign="bottom" align="left">
              <?php if (!$is_vendor_email) { ?>
                <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo __('User Email','woocommerce-catalog-enquiry-pro');?>:<br>
                <?php echo $enquiry_data['user_email']?></p>
              <?php } ?>           
              </td>
            </tr>
            <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
          <?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
            <tr>
             <td valign="bottom" align="left"></td>
             <td valign="bottom" align="left">
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;margin-bottom: 10px;"><?php echo '<strong>'.$field['label'].':</strong><br>'.$field['value']; ?></p>
              </td>
            </tr>
           <?php } } } ?>
            <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
          </table>
      </td>
    </tr>
    <tr>
      <td colspan="2" valign="top" align="left" style="width: 255px; background: #00a8c5; padding: 20px 20px 0px 25px">
         <table width="100%" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td valign="middle" align="left" style="width: auto; border-right: 1px solid #fdfdfd;"><?php //echo $product->get_image(array(64,64)); 
                $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                echo '<img src="'.$pro_img[0].'">';
               ?></td>
                <td valign="top" align="left" style="">
                  <p style="color: #fff; font-family: Arial; font-size: 14px; line-height: 16px; padding: 0 0 0 8px; margin: 0"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:<br>
                    <strong><?php echo $product->get_name(); ?></strong>
                    <?php if($product->get_type() == 'variation'){
                      if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
                        foreach ($enquiry_data['product_variations'] as $label => $value) {
                            $label = str_replace( 'attribute_pa_', '', $label );
                            $label = str_replace( 'attribute_', '', $label );
                            echo "<br>".ucfirst($label).": ".ucfirst($value);
                        } 
                      }else{
                        foreach ($product->get_attributes() as $label => $value) {
                          echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                        }
                      }
                    } ?>
                    <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></span><?php } ?></p>
                  
                   </td>
                 </tr>
               </table>  
               <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p> 
<?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <div style="width: 550px; padding: 0px 25px 0; background-color: #fdfdfd; border-top: 1px solid #ccc; border-bottom: 5px solid #00a8c5;">
          <div style="width: 550px; background-color: #fdfdfd; padding: 20px 0; font-family: Arial; text-align: center; color: #aaa; font-size: 12px">
            <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
          </div>
        </div>
      </td>
    </tr>
  </table>
  </div>
</body>
<?php  }

}elseif($email_tpl == 4){				
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 4 ****//
                          /*-------------------------------------------------------------------------*/
	
  if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
?>
<body width="100%" bgcolor="#eee" style="margin: 0;font-family: Arial;color: #292929;color: #292929; font-size: 15px; font-weight: 400; line-height: 20px;">  
    <div style="width: 100%; background-color: #c3c3c3;">
        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600">
            <tbody>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;background-color: #f1f1f1; background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/top-bkg-tpl4.png'; ?>'); background-position: top center; background-repeat: no-repeat; padding: 20px 0 10px;">
                            <tr>
                                <td style=" text-align: center">
                                    <div style="width: 339px;height: 339px;background-color: #ff9231;border-radius: 50%; margin: 0 auto; padding-top: 60px; box-sizing: border-box;">
                                        <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/mail-icon-tpl4.png'; ?>" alt="email" border="0" style=" margin-bottom: 18px; ">
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: #292929; font-size: 22px; font-weight: 400; "><?php echo __('Multiple Product Enquiry by','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo $enquiry_data['user_name']?></h1>
                                        <p style="width: 160px;height: 1px;background-color: white;margin: 0 auto;"></p>
                                    </div>
                                    <div style="text-align: left; padding:0 25px 20px; ">
                                        <p style="line-height: 25px;margin: 0;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>                           
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;background-color: #ff9231; background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/down-arrow-tpl4.png'; ?>'); background-position: center top; background-repeat: no-repeat;">
                            <tr>
                                <td>
                                    <div style="box-sizing: border-box;padding: 40px 25px; width: 100%; text-align: left;">
                                        <div style=" margin-bottom: 20px;">
                                            <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/user-icon-tpl4.png'; ?>" alt="" style="display: inline-block; vertical-align: bottom;margin-right: 3px;" />
                                            <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                <span style="line-height: 29px;color: #fff;font-size: 15px;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</span>
                                                <h1 style="line-height: 18px;margin: 0;color: #fff;font-size: 32px;font-weight: 700;text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
                                            </div>
                                        </div>
                                        <?php if(isset($enquiry_data['user_enquiry_fields']) && is_array($enquiry_data['user_enquiry_fields'])){
                                        $email_array = array('0'=>array(
                                                        'value'=> $enquiry_data['user_email'],
                                                        'label'=> __('User Email','woocommerce-catalog-enquiry-pro'),
                                                        'type'=> 'textbox'
                                                        ));
                                        $user_fields = array_merge($email_array,$enquiry_data['user_enquiry_fields']); 
                                        $k = 0;
                                        foreach($user_fields as $key => $field){ if($field['type'] != 'file'){ ?>
                                          <?php if ($k % 2 == 0) echo '<div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">'; ?>
                                            <div style="float: left; min-width:49%;">
                                                <div style="display: inline-block; vertical-align: bottom; line-height: 0.7;">
                                                    <span style="line-height: 16px; color: #fff; font-size: 15px;"><?php echo $field['label'];?>:</span>
                                                    <p style="line-height: 16px;color: #fff;font-size: 13px; margin: 0;"><?php echo $field['value'];?></p>
                                                </div>
                                            </div>
                                        <?php } 
                                        if ($k % 2 == 1) echo '</div>';
                                        $k++; }
                                        if ($k % 2 != 0){
                                              echo '</div>';
                                          } 
                                        } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600"> 
                            <tr>
                                <td bgcolor="#f1f1f1">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 30px 25px;">
                                                <div style="width: 100%;">
                                                <?php if(is_object($product_data)){
                                                  $j = 1;
                                                  foreach($product_data as $key =>$pro){ 
                                                  $product = wc_get_product( $pro->product_id ); $border ='border-right: 1px solid #cfcfcf;'; if($j %2 == 0){ $border ='';} ?>
                                                    <div style="width: 49%; min-height: 150px; display: inline-block; padding: 25px; box-sizing: border-box;<?php echo $border; ?> "> 
                                                        <h3 style=" margin: 0; padding: 0; font-size: 14px;line-height: 16px;color: #292929;font-weight: 400; "><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
                                                        <h3 style=" margin: 0; padding: 0; font-size: 14px; line-height: 16px;color: #ff9231;"><?php echo $product->get_name(); ?>
                                                        <?php if($product->get_type() == 'variation'){
                                                          if(isset($pro->variation) && count($pro->variation) > 0 ){
                                                            foreach ($pro->variation as $label => $value) {
                                                                $label = str_replace( 'attribute_pa_', '', $label );
                                                                $label = str_replace( 'attribute_', '', $label );
                                                                echo "<br>".ucfirst($label).": ".ucfirst($value);
                                                            } 
                                                          }else{
                                                            foreach ($product->get_attributes() as $label => $value) {
                                                                echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                                            }
                                                          }
                                                        } ?>
                                                        <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></span><?php } ?></h3>
                                                   
                                                        <p style="margin: 0;"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                                                                        <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                                                                        </div>
                                                    <?php $j++; } } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr> 

                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" role="presentation">
                            <tr>
                                <td style="padding: 30px 0;width: 100%;font-size: 14px; text-align: center; color: #879abf; background: #fff;">
                                    <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
                                </td>
                            </tr>
                        </table>                         
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>

<?php
  }else{                                              //**** Single Product ****//

      $product = wc_get_product( $product_data );

?>
<body width="100%" bgcolor="#eee" style="margin: 0;font-family: Arial;color: #292929;color: #292929; font-size: 15px; font-weight: 400; line-height: 20px;">  
    <div style="width: 100%; background-color: #c3c3c3;">
        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600">
            <tbody>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;background-color: #f1f1f1; background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/top-bkg-tpl4.png'; ?>'); background-position: top center; background-repeat: no-repeat; padding: 20px 0 10px;">
                            <tr>
                                <td style=" text-align: center">
                                    <div style="width: 339px;height: 339px;background-color: #ff9231;border-radius: 50%; margin: 0 auto; padding-top: 60px; box-sizing: border-box;">
                                        <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/mail-icon-tpl4.png'; ?>" alt="email" border="0" style=" margin-bottom: 18px; ">
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: #292929; font-size: 22px; font-weight: 400; "><?php echo __('Product Enquiry For','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo $product->get_title(); ?></h1>
                                        <p style=" margin: 0; padding: 0;color: white; font-size: 16px; font-weight: 400; line-height: 33px; text-transform: uppercase; "><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['user_name']?></p>
                                        <p style="width: 59px;height: 1px;background-color: white;margin: 0 auto;"></p>
                                    </div>
                                    <div style="text-align: left; padding:0 25px 20px; ">
                                        <p style="line-height: 25px;margin: 0;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
                                        <h2 style="font-family: Arial; margin: 0; padding: 0;line-height: 31px; margin-top: 35px; text-transform: uppercase; color: #233648; font-size: 22px; font-weight: 400;"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h2>
                                        <h1 style="font-family: Arial;margin: 0 0 7px; padding: 0;line-height: 42px; text-transform: uppercase; color: #ff9231; font-size: 46px; font-weight: 700;"><?php echo $product->get_formatted_name(); ?></h1>
                                        <a href="<?php echo $product->get_permalink(); ?>" style="margin-top: 5px; color: #b85700; font-size: 17px; font-weight: 400; line-height: 25px; text-decoration:  underline;"><?php echo __('Find the product link','woocommerce-catalog-enquiry-pro');?></a>                                 
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;background-color: #ff9231; background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/down-arrow-tpl4.png'; ?>'); background-position: center top; background-repeat: no-repeat;">
                            <tr>
                                <td>
                                    <div style="box-sizing: border-box;padding: 40px 25px; width: 100%; text-align: left;">
                                        <div style=" margin-bottom: 20px;">
                                            <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/user-icon-tpl4.png'; ?>" alt="" style="display: inline-block; vertical-align: bottom;margin-right: 3px;" />
                                            <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                <span style="line-height: 29px;color: #fff;font-size: 15px;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</span>
                                                <h1 style="line-height: 18px;margin: 0;color: #fff;font-size: 32px;font-weight: 700;text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
                                            </div>
                                        </div>
                                        <?php if(isset($enquiry_data['user_enquiry_fields']) && is_array($enquiry_data['user_enquiry_fields'])){
                                        $email_array = array('0'=>array(
                                                        'value'=> $enquiry_data['user_email'],
                                                        'label'=> __('User Email','woocommerce-catalog-enquiry-pro'),
                                                        'type'=> 'textbox'
                                                        ));
                                        $user_fields = array_merge($email_array,$enquiry_data['user_enquiry_fields']); 
                                        $k = 0;
                                        foreach($user_fields as $key => $field){ if($field['type'] != 'file'){ ?>
                                          <?php if ($k % 2 == 0) echo '<div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">'; ?>
                                            <div style="float: left; min-width:49%;">
                                                <div style="display: inline-block; vertical-align: bottom; line-height: 0.7;">
                                                    <span style="line-height: 16px; color: #fff; font-size: 15px;"><?php echo $field['label'];?>:</span>
                                                    <p style="line-height: 16px;color: #fff;font-size: 13px; margin: 0;"><?php echo $field['value'];?></p>
                                                </div>
                                            </div>
                                        <?php } 
                                        if ($k % 2 == 1) echo '</div>';
                                        $k++; }
                                        if ($k % 2 != 0){
                                              echo '</div>';
                                          } 
                                        } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600"> 
                            <tr>
                                <td bgcolor="#f1f1f1">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 30px 25px;">
                                                <div style="width: 100%;">

                                                    <div style="width: 49%; min-height: 190px; display: inline-block; padding: 25px; box-sizing: border-box;"> 
                                                        <h3 style=" margin: 0; padding: 0; font-size: 14px;line-height: 16px;color: #292929;font-weight: 400; "><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
                                                        <h3 style=" margin: 0; padding: 0; font-size: 14px; line-height: 16px;color: #ff9231;"><?php echo $product->get_name(); ?>
                                                        <?php if($product->get_type() == 'variation'){
                                                          if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
                                                            foreach ($enquiry_data['product_variations'] as $label => $value) {
                                                                $label = str_replace( 'attribute_pa_', '', $label );
                                                                $label = str_replace( 'attribute_', '', $label );
                                                                echo "<br>".ucfirst($label).": ".ucfirst($value);
                                                            } 
                                                          }else{
                                                            foreach ($product->get_attributes() as $label => $value) {
                                                              echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                                            }
                                                          }
                                                        } ?>
                                                        <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></span><?php } ?></h3>
                                                      
                                                        <p style="margin: 0;"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                                                    <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                                                                        </div>
                                                   
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr> 

                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" role="presentation">
                            <tr>
                                <td style="padding: 30px 0;width: 100%;font-size: 14px; text-align: center; color: #879abf; background: #fff;">
                                    <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
                                </td>
                            </tr>
                        </table>                         
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>

<?php  }

}elseif($email_tpl == 5){				
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 5 ****//
                          /*-------------------------------------------------------------------------*/
	
  if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//

?>
<body width="100%" style="margin: 0;font-family: Arial;color: #292929;color: #292929; font-size: 15px; font-weight: 400; line-height: 20px;">  
    <div style="background-color: #eee; width: 100%;">
        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600">
            <tbody>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;background-color: #233648; background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/top-bkg-tpl5.png'; ?>'); background-position: bottom center; background-repeat: no-repeat; padding: 40px 0 70px;">
                            <tr>
                                <td style=" text-align: center">
                                    <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/email-icon-tpl5.png'; ?>" alt="email" border="0" style=" margin-bottom: 22px; ">
                                    <h2 style=" text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: white; font-size: 22px; font-weight: 400; "><?php echo __('Multiple Product Enquiry by','woocommerce-catalog-enquiry-pro');?></h2>
                                    <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: #ff6c6c; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo $enquiry_data['user_name']?></h1>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600"> 
                            <tr>
                                <td bgcolor="#ffffff">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 30px 25px; text-align: center; ">
                                                <p style="line-height: 25px;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
                                                <div style="box-sizing: border-box;padding: 20px 30px; width: 100%; text-align: left;border: 1px solid #233648; margin-top: 35px;">
                                                    <div style=" margin: 0 auto 20px; display: table;">
                                                        <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/user-icon-tpl5.png'; ?>" alt="" style="display: inline-block; vertical-align: bottom;margin-right: 10px;" />
                                                        <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                            <span style="line-height: 29px;color: #ff6c6c;font-size: 15px;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</span>
                                                            <h1 style="text-align: center; font-family: Arial;line-height: 18px;margin: 0;color: #585858;font-size: 32px;font-weight: 700;text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
                                                        </div>
                                                    </div>
                                                    <?php if(isset($enquiry_data['user_enquiry_fields']) && is_array($enquiry_data['user_enquiry_fields'])){
                                                    $email_array = array('0'=>array(
                                                                    'value'=> $enquiry_data['user_email'],
                                                                    'label'=> __('User Email','woocommerce-catalog-enquiry-pro'),
                                                                    'type'=> 'textbox'
                                                                    ));
                                                    $user_fields = array_merge($email_array,$enquiry_data['user_enquiry_fields']); 
                                                    $k = 0;
                                                    foreach($user_fields as $key => $field){ if($field['type'] != 'file'){ ?>
                                                      <?php if ($k % 2 == 0) echo '<div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">'; ?>
                                                        <div style="float: left; min-width:49%;">
                                                            <span style="line-height: 16px; color: #ff6c6c; font-size: 15px;"><?php echo $field['label'];?>:</span>
                                                            <p style="line-height: 16px;color: #585858;font-size: 13px; margin: 3px 0 0 0;"><?php echo $field['value'];?></p>
                                                        </div>
                                                    <?php } 
                                                    if ($k % 2 == 1) echo '</div>';
                                                    $k++; }
                                                    if ($k % 2 != 0){
                                                          echo '</div>';
                                                      } 
                                                    } ?>
                                                    
                                                </div>
                                                <div style="width: 100%; overflow: hidden;margin: 50px 0 30px;">
                                                  <?php if(is_object($product_data)){ 
                                                    foreach($product_data as $key =>$pro){
                                                      $product = wc_get_product( $pro->product_id );
                                                  ?>
                                                    <div style="width: 49%; min-height: 220px; display: inline-block; padding: 25px; box-sizing: border-box; border-right: 1px solid #cfcfcf;">
                                                        <?php //echo $product->get_image(array(64,64)); 
                                                          $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                                                          echo '<img src="'.$pro_img[0].'">';
                                                         ?>
                                                        <h3 style="text-align: center; font-family: Arial; margin: 0; padding: 0; font-size: 14px;line-height: 16px;color: #292929;font-weight: 400; "><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
                                                        <h3 style="text-align: center; font-family: Arial; margin: 0; padding: 0; font-size: 14px; line-height: 16px;color: #ff6c6c;"><?php echo $product->get_name(); ?>
                                                        <?php if($product->get_type() == 'variation'){
                                                          if(isset($pro->variation) && count($pro->variation) > 0 ){
                                                            foreach ($pro->variation as $label => $value) {
                                                                $label = str_replace( 'attribute_pa_', '', $label );
                                                                $label = str_replace( 'attribute_', '', $label );
                                                                echo "<br>".ucfirst($label).": ".ucfirst($value);
                                                            } 
                                                          }else{
                                                            foreach ($product->get_attributes() as $label => $value) {
                                                                echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                                            }
                                                          }
                                                        } ?>
                                                        <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></span><?php } ?></h3>
                                                     
                                                        <p style="margin: 0;"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                                                    <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                                                                        </div>
                                                    <?php }
                                                   } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr> 

                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" role="presentation">
                            <tr>
                                <td style="padding: 30px 0;width: 100%;font-size: 14px; text-align: center; color: #879abf; border-bottom: 5px solid #233648;border-top: 1px solid #233648; background: #fff;">
                                    <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
                                </td>
                            </tr>
                        </table>                         
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>
<?php
  }else{                                              //**** Single Product ****//

      $product = wc_get_product( $product_data );

?>
<body width="100%" style="margin: 0;font-family: Arial;color: #292929;color: #292929; font-size: 15px; font-weight: 400; line-height: 20px;">  
    <div style="background-color: #eee; width: 100%;">
        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600">
            <tbody>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;background-color: #233648; background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/top-bkg-tpl5.png'; ?>'); background-position: bottom center; background-repeat: no-repeat; padding: 40px 0 70px;">
                            <tr>
                                <td style=" text-align: center">
                                    <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/email-icon-tpl5.png'; ?>" alt="email" border="0" style=" margin-bottom: 22px; ">
                                    <h2 style=" text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: white; font-size: 22px; font-weight: 400; "><?php echo __('Product Enquiry For','woocommerce-catalog-enquiry-pro');?></h2>
                                    <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: #ff6c6c; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo $product->get_title(); ?></h1>
                                    <p style=" margin: 0; padding: 0;color: white; font-size: 16px; font-weight: 400; line-height: 33px; text-transform: uppercase; "><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['user_name']?></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600"> 
                            <tr>
                                <td bgcolor="#ffffff">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding: 30px 25px; text-align: center; ">
                                                <p style="line-height: 25px;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
                                                <h2 style=" text-align: center; font-family: Arial; margin: 0; padding: 0;line-height: 31px; margin-top: 35px; text-transform: uppercase; color: #233648; font-size: 22px; font-weight: 400;"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h2>
                                                <h1 style="text-align: center; font-family: Arial;margin: 0 0 7px; padding: 0;line-height: 42px; text-transform: uppercase; color: #ff6c6c; font-size: 46px; font-weight: 700;"><?php echo $product->get_formatted_name(); ?></h1>
                                                <a href="<?php echo $product->get_permalink(); ?>" style="margin-top: 5px; color: #233648; font-size: 17px; font-weight: 400; line-height: 25px; text-decoration:  underline;"><?php echo __('Find the product link','woocommerce-catalog-enquiry-pro');?></a>
                                                <div style="box-sizing: border-box;padding: 20px 30px; width: 100%; text-align: left;border: 1px solid #233648; margin-top: 35px;">
                                                    <div style=" margin: 0 auto 20px; display: table;">
                                                        <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/user-icon-tpl5.png'; ?>" alt="" style="display: inline-block; vertical-align: bottom;margin-right: 10px;" />
                                                        <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                            <span style="line-height: 29px;color: #ff6c6c;font-size: 15px;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</span>
                                                            <h1 style="text-align: center; font-family: Arial;line-height: 18px;margin: 0;color: #585858;font-size: 32px;font-weight: 700;text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
                                                        </div>
                                                    </div>

                                                    <?php if(isset($enquiry_data['user_enquiry_fields']) && is_array($enquiry_data['user_enquiry_fields'])){
                                                    $email_array = array('0'=>array(
                                                                    'value'=> $enquiry_data['user_email'],
                                                                    'label'=> __('User Email','woocommerce-catalog-enquiry-pro'),
                                                                    'type'=> 'textbox'
                                                                    ));
                                                    $user_fields = array_merge($email_array,$enquiry_data['user_enquiry_fields']); 
                                                    $k = 0;
                                                    foreach($user_fields as $key => $field){ if($field['type'] != 'file'){ ?>
                                                      <?php if ($k % 2 == 0) echo '<div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">'; ?>
                                                        <div style="float: left; min-width:49%;">
                                                            <span style="line-height: 16px; color: #ff6c6c; font-size: 15px;"><?php echo $field['label'];?>:</span>
                                                            <p style="line-height: 16px;color: #585858;font-size: 13px; margin: 3px 0 0 0;"><?php echo $field['value'];?></p>
                                                        </div>
                                                    <?php } 
                                                    if ($k % 2 == 1) echo '</div>';
                                                    $k++; }
                                                    if ($k % 2 != 0){
                                                          echo '</div>';
                                                      } 
                                                    } ?>
                                                </div>
                                                <div style="width: 100%; overflow: hidden;margin: 50px 0 30px;">
                                                    <div style="width: 49%; min-height: 220px; display: inline-block; padding: 25px; box-sizing: border-box;">
                                                        <?php //echo $product->get_image(array(64,64)); 
                                                          $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                                                          echo '<img src="'.$pro_img[0].'">';
                                                         ?>
                                                        <h3 style="text-align: center; font-family: Arial; margin: 0; padding: 0; font-size: 14px;line-height: 16px;color: #292929;font-weight: 400; "><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
                                                        <h3 style="text-align: center; font-family: Arial; margin: 0; padding: 0; font-size: 14px; line-height: 16px;color: #ff6c6c;"><?php echo $product->get_name(); ?>
                                                        <?php if($product->get_type() == 'variation'){
                                                          if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
                                                            foreach ($enquiry_data['product_variations'] as $label => $value) {
                                                                $label = str_replace( 'attribute_pa_', '', $label );
                                                                $label = str_replace( 'attribute_', '', $label );
                                                                echo "<br>".ucfirst($label).": ".ucfirst($value);
                                                            } 
                                                          }else{
                                                            foreach ($product->get_attributes() as $label => $value) {
                                                              echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                                            }
                                                          }
                                                        } ?>
                                                        <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></span><?php } ?></h3>
                                                     
                                                        <p style="margin: 0;"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                                                    <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                                                                        </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr> 

                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center">
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" role="presentation">
                            <tr>
                                <td style="padding: 30px 0;width: 100%;font-size: 14px; text-align: center; color: #879abf; border-bottom: 5px solid #233648;border-top: 1px solid #233648; background: #fff;">
                                    <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
                                </td>
                            </tr>
                        </table>                         
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>
<?php
  }

}elseif($email_tpl == 6){  			
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 6 ****//
                          /*-------------------------------------------------------------------------*/
	
  if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
?>
  
  <body width="100%" bgcolor="#eee" style="margin: 0;font-family: Arial;color: #292929;color: #292929; font-size: 15px; font-weight: 400; line-height: 20px;">  
    <div style="width: 100%; background-color: #eee;">
        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="background-color: #7dc1c4;">
            <tbody>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;  background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/top-bkg-tpl6.png'; ?>'); background-position: top center; background-repeat: no-repeat;">
                            <tr>
                                <td style=" text-align: center">
                                    <div style="margin: 150px 0 20px"> 
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 50px;font-size: 27px; color: #292929; font-weight: 400;text-transform: uppercase; "><?php echo __('Multiple Product Enquiry by','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white;line-height: 37px;font-size: 54px;font-weight: 700; text-transform: uppercase; "><?php echo $enquiry_data['user_name']?></h1>
                                        <p style="width: 186px;height: 1px;background-color: white;margin: 0 auto;"></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;">
                            <tr>
                                <td>
                                    <div style="box-sizing: border-box; width: 100%; text-align: left; padding: 10px 25px 25px; ">
                                        <div style="background-color: #fff; overflow: hidden; padding: 20px 0 0;">


                                            <p style="line-height: 25px;margin: 0; padding: 0 15px 15px;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
                                            
                                            <div style="padding: 15px;">
                                                <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/user-icon-tpl6.png'; ?>" alt="" style="display: inline-block; vertical-align: bottom;margin-right: 3px;" />
                                                <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                    <span style="line-height: 29px;color: #292929;font-size: 15px;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</span>
                                                    <h1 style="line-height: 18px;margin: 0;color: #7dc1c4;font-size: 32px;font-weight: 700;text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
                                                </div>
                                                <div style="width: 100%; height: 1px; background-color: #7dc1c4; margin-top: 15px;"></div>
                                            </div>
                                            <?php if(isset($enquiry_data['user_enquiry_fields']) && is_array($enquiry_data['user_enquiry_fields'])){
                                              $email_array = array('0'=>array(
                                                              'value'=> $enquiry_data['user_email'],
                                                              'label'=> __('User Email','woocommerce-catalog-enquiry-pro'),
                                                              'type'=> 'textbox'
                                                              ));
                                              $user_fields = array_merge($email_array,$enquiry_data['user_enquiry_fields']); 
                                              $k = 0;
                                              foreach($user_fields as $key => $field){ if($field['type'] != 'file'){ ?>
                                                <?php if ($k % 2 == 0) echo '<div style="overflow: hidden; display: inline-block; width: 100%; padding: 10px 15px; box-sizing: border-box;">'; ?>
                                                  <div style="float: left; min-width:49%;">
                                                      <div style="display: inline-block; vertical-align: bottom; line-height: 0.7;">
                                                          <span style="line-height: 16px; color: #292929; font-size: 15px;"><?php echo $field['label'];?>:</span>
                                                          <p style="line-height: 16px;color: #292929;font-size: 13px; margin: 0;"><?php echo $field['value'];?></p>
                                                      </div>
                                                  </div>
                                              <?php } 
                                              if ($k % 2 == 1) echo '</div>';
                                              $k++; }
                                              if ($k % 2 != 0){
                                                    echo '</div>';
                                                } 
                                              } ?>
                                           
                                            <div style="width: 100%; margin: 0; overflow: hidden; box-sizing: border-box; padding: 35px 0 30px;">
                                            <?php if(is_object($product_data)){
                                              $j = 1;
                                              foreach($product_data as $key =>$pro){
                                              $lft_rgt ='left'; if($j %2 == 0){ $lft_rgt ='right';}
                                              $product = wc_get_product( $pro->product_id );
                                              ?>
                                                <div style="width: 100%; display: inline-block; padding:0 20px 20px; box-sizing: border-box; float: <?php echo $lft_rgt; ?>;text-align:<?php echo $lft_rgt; ?>;"> 
                                                    <?php //echo $product->get_image(array(64,64)); 
                                                    $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                                                    echo '<img src="'.$pro_img[0].'">';
                                                   ?>
                                                    <h3 style=" margin: 0; padding: 0; font-size: 14px;line-height: 16px;color: #292929;font-weight: 400;text-align:<?php echo $lft_rgt; ?>; "><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
                                                    <h3 style=" margin: 0; padding: 0; font-size: 14px; line-height: 16px;color: #7dc1c4;text-align:<?php echo $lft_rgt; ?>;"><?php echo $product->get_name(); ?>
                                                    <?php if($product->get_type() == 'variation'){
                                                          if(isset($pro->variation) && count($pro->variation) > 0 ){
                                                            foreach ($pro->variation as $label => $value) {
                                                                $label = str_replace( 'attribute_pa_', '', $label );
                                                                $label = str_replace( 'attribute_', '', $label );
                                                                echo "<br>".ucfirst($label).": ".ucfirst($value);
                                                            } 
                                                          }else{
                                                            foreach ($product->get_attributes() as $label => $value) {
                                                                echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                                            }
                                                          }
                                                        } ?>
                                                    <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></span><?php } ?></h3>
                                              
                                                    <p style="margin: 0;"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                                                                    <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                                                    <div class="divider" style="width: 340px;height: 1px;background-color: #d9d9d9; margin: 30px 0px; float: <?php if($lft_rgt == 'left') {echo 'right';}elseif($lft_rgt == 'right') {echo 'left';}else{echo $lft_rgt;} ?>; clear: both; "><!--img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/divider-img-tpl6.png'; ?>" alt="" style="margin: -5px 0 0 -3px; float: <?php echo $lft_rgt; ?>;" /--></div>
                                                </div>
                                                <?php $j++;} } ?>
                                                
                                            </div>
                                        </div> <!-- White area -->
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                </tr> 
                <tr>
                    <td valign="top" align="center">
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" role="presentation">
                            <tr>
                                <td style="padding: 20px 0;width: 100%;font-size: 14px; text-align: center; color: #879abf; background: #fff;">
                                    <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
                                </td>
                            </tr>
                        </table>                         
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>
<?php
  }else{                                              //**** Single Product ****//

      $product = wc_get_product( $product_data );

?>
  
  <body width="100%" bgcolor="#eee" style="margin: 0;font-family: Arial;color: #292929;color: #292929; font-size: 15px; font-weight: 400; line-height: 20px;">  
    <div style="width: 100%; background-color: #eee;">
        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="background-color: #7dc1c4;">
            <tbody>
                <tr>
                    <td valign="top" align="center">

                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;  background-image: url('<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/top-bkg-tpl6.png'; ?>'); background-position: top center; background-repeat: no-repeat;">
                            <tr>
                                <td style=" text-align: center">
                                    <div style="margin: 150px 0 20px"> 
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 50px;font-size: 27px; color: #292929; font-weight: 400;text-transform: uppercase; "><?php echo __('Product Enquiry For','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white;line-height: 37px;font-size: 54px;font-weight: 700; text-transform: uppercase; "<?php echo $product->get_title(); ?></h1>
                                        <p style=" margin: 0; padding: 0;color: white; font-size: 18px; font-weight: 400; line-height: 40px; text-transform: uppercase; margin-top: 10px; "><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['user_name']?></p>
                                        <p style="width: 59px;height: 1px;background-color: white;margin: 0 auto;"></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" style="max-width: 600px;">
                            <tr>
                                <td>
                                    <div style="box-sizing: border-box; width: 100%; text-align: left; padding: 10px 25px 25px; ">
                                        <div style="background-color: #fff; overflow: hidden; padding: 20px 0 0;">


                                            <p style="line-height: 25px;margin: 0; padding: 0 15px 15px;"><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
                                            <div style="text-align: left; padding:15px;background-color: #f2f2f2; ">
                                                <h2 style="font-family: Arial; margin: 0; padding: 0;line-height: 34px; text-transform: uppercase; color: #292929; font-size: 20px; font-weight: 400; display: inline-block;vertical-align: middle;"><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h2>
                                                <h1 style="font-family: Arial;margin: 0; padding: 0;line-height: 34px; text-transform: uppercase; color: #292929; font-size: 30px; font-weight: 400; display: inline-block; vertical-align: middle;"><?php echo $product->get_formatted_name(); ?></h1>
                                                <a href="<?php echo $product->get_permalink(); ?>" style="color: #7dc1c4; font-size: 17px; font-weight: 400; line-height: 25px; text-decoration:  underline; clear: left; display: inline-block;"><?php echo __('Find the product link','woocommerce-catalog-enquiry-pro');?></a> 
                                            </div>


                                            <div style="padding: 15px;">
                                                <img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/user-icon-tpl6.png'; ?>" alt="" style="display: inline-block; vertical-align: bottom;margin-right: 3px;" />
                                                <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                    <span style="line-height: 29px;color: #292929;font-size: 15px;"><?php echo __('User Name','woocommerce-catalog-enquiry-pro');?>:</span>
                                                    <h1 style="line-height: 18px;margin: 0;color: #7dc1c4;font-size: 32px;font-weight: 700;text-transform: uppercase;"><?php echo $enquiry_data['user_name']?></h1>
                                                </div>
                                                <div style="width: 100%; height: 1px; background-color: #7dc1c4; margin-top: 15px;"></div>
                                            </div>
                                            <?php if(isset($enquiry_data['user_enquiry_fields']) && is_array($enquiry_data['user_enquiry_fields'])){
                                              $email_array = array('0'=>array(
                                                              'value'=> $enquiry_data['user_email'],
                                                              'label'=> __('User Email','woocommerce-catalog-enquiry-pro'),
                                                              'type'=> 'textbox'
                                                              ));
                                              $user_fields = array_merge($email_array,$enquiry_data['user_enquiry_fields']); 
                                              $k = 0;
                                              foreach($user_fields as $key => $field){ if($field['type'] != 'file'){ ?>
                                                <?php if ($k % 2 == 0) echo '<div style="overflow: hidden; display: inline-block; width: 100%; padding: 10px 15px; box-sizing: border-box;">'; ?>
                                                  <div style="float: left; min-width:49%;">
                                                      <div style="display: inline-block; vertical-align: bottom; line-height: 0.7;">
                                                          <span style="line-height: 16px; color: #292929; font-size: 15px;"><?php echo $field['label'];?>:</span>
                                                          <p style="line-height: 16px;color: #292929;font-size: 13px; margin: 0;"><?php echo $field['value'];?></p>
                                                      </div>
                                                  </div>
                                              <?php } 
                                              if ($k % 2 == 1) echo '</div>';
                                              $k++; }
                                              if ($k % 2 != 0){
                                                    echo '</div>';
                                                } 
                                              } ?>
                                            <div style="width: 100%; margin: 0; overflow: hidden; box-sizing: border-box; padding: 35px 0 30px;">
                                                
                                                <div style="width: 100%; display: inline-block; padding:0 20px 20px; box-sizing: border-box; float: left;"> 
                                                    <?php //echo $product->get_image(array(64,64)); 
                                                      $pro_img = wp_get_attachment_image_src($product->get_image_id(),array(64,64));
                                                      echo '<img src="'.$pro_img[0].'">';
                                                     ?>
                                                    <h3 style=" margin: 0; padding: 0; font-size: 14px;line-height: 16px;color: #292929;font-weight: 400; "><?php echo __('Product Name','woocommerce-catalog-enquiry-pro');?>:</h3>
                                                    <h3 style=" margin: 0; padding: 0; font-size: 14px; line-height: 16px;color: #7dc1c4;"><?php echo $product->get_name(); ?>
                                                    <?php if($product->get_type() == 'variation'){
                                                          if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
                                                            foreach ($enquiry_data['product_variations'] as $label => $value) {
                                                                $label = str_replace( 'attribute_pa_', '', $label );
                                                                $label = str_replace( 'attribute_', '', $label );
                                                                echo "<br>".ucfirst($label).": ".ucfirst($value);
                                                            } 
                                                          }else{
                                                            foreach ($product->get_attributes() as $label => $value) {
                                                              echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                                            }
                                                          }
                                                        } ?>
                                                    <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><br><span><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></span><?php } ?></h3>
                                                    
                                                    <p style="margin: 0;"><?php echo __('Product Url','woocommerce-catalog-enquiry-pro');?>: <a href="<?php echo $product->get_permalink(); ?>"><i><?php echo $product->get_title(); ?><i></a></p>
                                                <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
                                                                    </div>
                                            </div>
                                        </div> <!-- White area -->
                                    </div>
                                </td>
                            </tr>
                        </table>
                        
                    </td>
                </tr> 
                <tr>
                    <td valign="top" align="center">
                        <table cellspacing="0" cellpadding="0" border="0" align="center" width="600" role="presentation">
                            <tr>
                                <td style="padding: 20px 0;width: 100%;font-size: 14px; text-align: center; color: #879abf; background: #fff;">
                                    <?php echo apply_filters( 'woocommerce_catalog_footer_email_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?>
                                </td>
                            </tr>
                        </table>                         
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</body>
<?php
  }

}else{														          
                          /*-------------------------------------------------------------------------*/
                                                //**** WC Default Template ****//
                          /*-------------------------------------------------------------------------*/


	do_action( 'woocommerce_email_header', $email_heading );

	if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
	?>

		<p><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
		<h2><?php echo __('User Name : ','woocommerce-catalog-enquiry-pro');?><?php echo $enquiry_data['user_name']; ?></h2>
		<p><?php echo __('User Email : ','woocommerce-catalog-enquiry-pro');?><?php echo $enquiry_data['user_email']; ?></p>
		<table>						
			<tr>
				<td class="padding">
					<p><?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
         <?php echo '<strong>'.$field['label'].':</strong>'.$field['value'].'<br>'; ?>

        <?php } } } ?></p>
				</td>
			</tr>
		</table>
		<?php if(is_object($product_data)){ foreach ( $product_data as $key => $pro ){ 
			$product = wc_get_product( $pro->product_id ); ?>
		<p><?php echo __("Product Name : ",'woocommerce-catalog-enquiry-pro').$product->get_name(); ?>
    <?php if($product->get_type() == 'variation'){
      if(isset($pro->variation) && count($pro->variation) > 0 ){
        foreach ($pro->variation as $label => $value) {
            $label = str_replace( 'attribute_pa_', '', $label );
            $label = str_replace( 'attribute_', '', $label );
            echo "<br>".ucfirst($label).": ".ucfirst($value);
        } 
      }else{
        foreach ($product->get_attributes() as $label => $value) {
            echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
        }
      }
    } ?>
    </p>
    <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><p><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $pro->quantity; ?></p><?php } ?>
		<p><?php echo __("Product Url : ",'woocommerce-catalog-enquiry-pro').$product->get_permalink(); ?></p>	
		<p><?php echo __("Product SKU : ",'woocommerce-catalog-enquiry-pro').$product->get_sku(); ?></p>	
<?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
	<?php } }
	}else{							//**** Single Product ****//

			$product = wc_get_product( $product_data );
	
		?>
		<p><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry-pro');?>.</p>
		<h1><?php echo __('Product Name :','woocommerce-catalog-enquiry-pro');?> <?php echo $product->get_formatted_name(); ?></h1>
		<?php if($enquiry_data['product_otherInfo'] != '') { echo $enquiry_data['product_otherInfo']; }?>
		<p><a href="<?php echo $product->get_permalink(); ?>"> <?php echo __('Find the product Link','woocommerce-catalog-enquiry-pro');?></a></p>
    <?php if (!$is_vendor_email) { ?>
		  <h2><?php echo __('User Name : ','woocommerce-catalog-enquiry-pro');?><?php echo $enquiry_data['user_name']; ?></h2>
		  <p><?php echo __('User Email : ','woocommerce-catalog-enquiry-pro');?><?php echo $enquiry_data['user_email']; ?></p>
    <?php } ?>
		<table>						
			<tr>
				<td class="padding">
					<p><?php if($enquiry_data['user_enquiry_fields']!= '' && is_array($enquiry_data['user_enquiry_fields'])){ 
            foreach($enquiry_data['user_enquiry_fields'] as $key => $field){ if($field['type'] != 'file'){
          ?>
         <?php echo '<strong>'.$field['label'].':</strong>'.$field['value'].'<br>'; ?>

        <?php } } } ?></p>
				</td>
			</tr>
		</table>
		<p><?php echo __("Product Name : ",'woocommerce-catalog-enquiry-pro').$product->get_name(); ?>
    <?php if($product->get_type() == 'variation'){
      if($enquiry_data['product_variations'] && count($enquiry_data['product_variations']) > 0 ){
        foreach ($enquiry_data['product_variations'] as $label => $value) {
            $label = str_replace( 'attribute_pa_', '', $label );
            $label = str_replace( 'attribute_', '', $label );
            echo "<br>".ucfirst($label).": ".ucfirst($value);
        } 
      }else{
        foreach ($product->get_attributes() as $label => $value) {
          echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
        }
      }
    } ?>
    </p>
    <?php if(apply_filters( 'woocommerce_catalog_enquiry_cart_show_quantity', false)){ ?><p><?php echo __('Quantity','woocommerce-catalog-enquiry-pro');?>: <?php echo $enquiry_data['product_quantity']; ?></p><?php } ?>
		<p><?php echo __("Product Url : ",'woocommerce-catalog-enquiry-pro').$product->get_permalink(); ?></p>	
		<p><?php echo __("Product SKU : ",'woocommerce-catalog-enquiry-pro').$product->get_sku(); ?></p>
                <?php do_action('woocommerce_catalog_catalog_enquiry_product_add_extra_data',$product); ?>
	<?php 
	} ?>
	<p style="text-align: center;"><?php echo apply_filters( 'woocommerce_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?></p>
<?php }