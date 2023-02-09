<?php
/**
 * WCCE Reply Email 
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/emails/woocommerce-catalog-enquiry-pro-reply-email.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $Woocommerce_Catalog_Enquiry_Pro,$woocommerce,$Woocommerce_Catalog_Enquiry_Pro_Cart;

$email_tpl = get_woocommerce_catalog_catalog_settings();
$is_vendor_product = false;
$product_data = get_post_meta( $enquiry_data['enquiry_id'] , '_enquiry_product', true );
if($enquiry_data['enquiry_action_type'] != 'multiple'){
  if(is_active_MVX()){
    if(!is_object($product_data)){
      $product = wc_get_product( $product_data );
      $post = get_post( $product->get_id() );
      $product_author = get_mvx_product_vendors($post->ID);
      $is_vendor_product = is_user_mvx_vendor($product_author->id);
    }
  }
  if($is_vendor_product){
    $product = wc_get_product( $product_data );
    $post = get_post( $product->get_id() );
    $product_author = get_mvx_product_vendors($post->ID);
    $email_tpl = get_woocommerce_catalog_catalog_settings($product_author->id);
    $owner = get_userdata($product_author->id)->user_login;
  }
}
if($email_tpl['selected_email_tpl'] == 1){					
                          /*-------------------------------------------------------------------------*/
                                                //**** Enquiry Template 1 ****//
                          /*-------------------------------------------------------------------------*/

if($enquiry_data['enquiry_action_type'] == 'multiple'){ //**** Multiple Product ****//
?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"><?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 46px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h1>
      <div style="width: 100%; border-bottom: 1px solid #fff; height: 10px;margin-bottom: 10px; text-align: center;"><span><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/divider_box.png'; ?>"></span>
      </div>
      <p style="margin-bottom: 2px;"> &nbsp;</p>
      <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 20px;display: inline-block;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #fd7373;">
      <h2 style="font-family: Arial; line-height: 20px; color: #292929; font-size: 16px; font-weight: 400; margin: 0;padding: 10px 0 0 20px;"> <?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h2>
      <p style="margin: 0; padding: 6px 0 20px 20px"><?php echo $enquiry_data['subject_mail']; ?></p>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background: #fdfdfd url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/slice_1.png'; ?>) 0 0 repeat-x;">
    <div style="width: 550px; background-color: #f75d5d; position: relative;">
      <div style="position: absolute; top: -1px; left: 16px; width: auto;"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/arrow_red.png'; ?>" alt=""></div>
      <table width="100%" cellspacing="10" cellpadding="10" border="0">
        <tr>
          <td valign="top" align="left" style="width: 99%;">
            <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 20px;display: inline-block;"><?php echo $enquiry_data['body_mail']; ?></p>
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
<?php 
	}else{												//**** Single Product ****//
?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"><?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 46px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name']; ?></h1>
      <div style="width: 100%; border-bottom: 1px solid #fff; height: 10px;margin-bottom: 10px; text-align: center;"><span><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/divider_box.png'; ?>"></span>
      </div>
      <p style="margin-bottom: 2px;"> &nbsp;</p>
      <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 20px;display: inline-block;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #fd7373;">
      <h2 style="font-family: Arial; line-height: 20px; color: #292929; font-size: 16px; font-weight: 400; margin: 0;padding: 10px 0 0 20px;"> <?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h2>
      <p style="margin: 0; padding: 6px 0 20px 20px"><?php echo $enquiry_data['subject_mail']; ?></p>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background: #fdfdfd url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/slice_1.png'; ?>) 0 0 repeat-x;">
    <div style="width: 550px; background-color: #f75d5d; position: relative;">
      <div style="position: absolute; top: -1px; left: 16px; width: auto;"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/arrow_red.png'; ?>" alt=""></div>
      <table width="100%" cellspacing="10" cellpadding="10" border="0">
        <tr>
          <td valign="top" align="left" style="width: 99%;">
            <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 20px;display: inline-block;"><?php echo $enquiry_data['body_mail']; ?></p>
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

} elseif($email_tpl['selected_email_tpl'] == 2){        
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
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 36px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h1>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #5ba5af;">
     <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 0px;display: inline-block;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background: #4d96a0 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/envelop_bg.png'; ?>) right bottom no-repeat;">
    <div style="width: 550px; position: relative; padding: 16px 0;">
      
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td valign="top" align="left" style="width: 99%;">
            <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h3>
            <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo $enquiry_data['subject_mail']; ?></p>
        </td>
      </tr>
      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>
    
  </div>
</div>
<div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
  <div style="width: 550px; padding: 30px 0 40px 0">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td valign="top" align="left" style="width: 99%;">
           
          <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo $enquiry_data['body_mail']; ?></p>       
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
<?php
  }else{                                              //**** Single Product ****//

?>
  <body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
   <div style="width: 550px; padding: 25px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #f75d5d;">
      <p style="text-align: center; padding: 25px 0 2px 0; margin: 0"><img src="<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/envelop_shdow.png'; ?>" alt=""></p>
      <h2 style="font-family: Arial; line-height: 43px; text-align: center; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
      <h1 style="font-family: Arial; line-height: 43px; text-align: center; color: #fff; font-size: 36px; font-weight: 700; margin: 0;padding: 0 0 0px 0; text-transform: uppercase;"><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name']; ?></h1>
    </div>
  </div>
  <div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
    <div style="width: 550px; background-color: #5ba5af;">
     <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 25px; padding: 0 0 0 0px;display: inline-block;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
     </div>
   </div>
   <div style="width: 550px; padding: 0px 25px 0; background: #4d96a0 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/envelop_bg.png'; ?>) right bottom no-repeat;">
    <div style="width: 550px; position: relative; padding: 16px 0;">
      
      <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td valign="top" align="left" style="width: 99%;">
            <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h3>
            <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo $enquiry_data['subject_mail']; ?></p>
        </td>
      </tr>
      <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
    </table>
    
  </div>
</div>
<div style="width: 550px; padding: 0px 25px 0; background-color: #5ba5af;">
  <div style="width: 550px; padding: 30px 0 40px 0">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td valign="top" align="left" style="width: 99%;">
           
          <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo $enquiry_data['body_mail']; ?></p>       
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

}elseif($email_tpl['selected_email_tpl'] == 3){       
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
        <h2 style="font-family: Arial; line-height: 23px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
        <h1 style="color: white; font-family: Arial; font-size: 36px; font-weight: 700; line-height: 30px; text-transform: uppercase; margin: 0;padding: 0 0 0px 0;  border-bottom: 1px solid #fff"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h1>
        <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 20px; padding: 10px 0 0 0px; display: inline-block;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
      </td>
      <td valign="top" align="left" style="width: 255px; background: #00a8c5 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/mail_top.png'; ?>) right top no-repeat; padding: 20px 20px 0px 25px; border-bottom: 1px solid #fff">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td valign="bottom" align="left">
              <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h3>
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo $enquiry_data['subject_mail']; ?></p>
            </td>
          </tr>
          <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
          </table>
      </td>
    </tr>
    <tr></tr>
    <tr>
      <td colspan="2" valign="top" align="left" style="width: 99%; background: #00a8c5; padding: 20px 20px 30px 25px">
        <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo $enquiry_data['body_mail']; ?></p> 
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
<?php
  }else{                                              //**** Single Product ****//

?>
<body style="background: #ddd">
  <div style="width: 600px; margin: 0 auto;">
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td valign="top" rowspan="2" align="left" style="width: 265px; background: #e2a209; padding: 20px 15px 20px 20px">
        <h2 style="font-family: Arial; line-height: 23px; color: #292929; font-size: 22px; font-weight: 400; margin: 0;padding: 10px 0 0;"> <?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
        <h1 style="color: white; font-family: Arial; font-size: 36px; font-weight: 700; line-height: 30px; text-transform: uppercase; margin: 0;padding: 0 0 0px 0;  border-bottom: 1px solid #fff"><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name'];?></h1>
        <p style="color: white; font-family: Arial; font-size: 15px; font-weight: 400; line-height: 20px; padding: 10px 0 0 0px; display: inline-block;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
      </td>
      <td valign="top" align="left" style="width: 255px; background: #00a8c5 url(<?php echo $Woocommerce_Catalog_Enquiry_Pro->plugin_url . 'assets/images/email/mail_top.png'; ?>) right top no-repeat; padding: 20px 20px 0px 25px; border-bottom: 1px solid #fff">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td valign="bottom" align="left">
              <h3 style="color: #fff; font-family: Arial; line-height: 25px; font-size: 20px; font-weight: 700; text-transform: uppercase;margin: 0; padding: 0;"> <?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h3>
              <p style="color: #fff; font-family: Arial; line-height: 15px; font-size: 13px; font-weight: 400; margin: 0; padding: 0;"><?php echo $enquiry_data['subject_mail']; ?></p>
            </td>
          </tr>
          <tr><td style="height: 30px">&nbsp;</td><td style="height: 30px">&nbsp;</td></tr>
          </table>
      </td>
    </tr>
    <tr></tr>
    <tr>
      <td colspan="2" valign="top" align="left" style="width: 99%; background: #00a8c5; padding: 20px 20px 30px 25px">
        <p style="color: #fff; font-family: Arial; font-size: 13px; font-weight: 400; line-height: 16px; padding: 5px 0 0 0; margin: 0"><?php echo $enquiry_data['body_mail']; ?></p> 
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

}elseif($email_tpl['selected_email_tpl'] == 4){       
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
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: #292929; font-size: 22px; font-weight: 400; "><?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h1>
                                        <p style="width: 160px;height: 1px;background-color: white;margin: 0 auto;"></p>
                                    </div>
                                    <div style="text-align: left; padding:0 25px 20px; ">
                                        <p style="line-height: 25px;margin: 0;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>                           
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
                                            <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                              <h1 style="line-height: 18px;margin: 0;color: #fff;font-size: 20px;font-weight: 700;text-transform: uppercase;"><?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h1>
                                                <span style="line-height: 29px;color: #fff;font-size: 15px;"><?php echo $enquiry_data['subject_mail']; ?></span>
                                                
                                            </div>
                                        </div>
                                        <div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">
                                          <div style="float: left; min-width:99%;">
                                              <div style="display: inline-block; vertical-align: bottom; line-height: 0.7;">
                                                  <p style="line-height: 16px;color: #fff;font-size: 13px; margin: 0;"><?php echo $enquiry_data['body_mail']; ?></p>
                                              </div>
                                          </div>
                                        </div>
                                        
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
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: #292929; font-size: 22px; font-weight: 400; "><?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name'];?></h1>
                                        <p style="width: 160px;height: 1px;background-color: white;margin: 0 auto;"></p>
                                    </div>
                                    <div style="text-align: left; padding:0 25px 20px; ">
                                        <p style="line-height: 25px;margin: 0;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>                           
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
                                            <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                              <h1 style="line-height: 18px;margin: 0;color: #fff;font-size: 20px;font-weight: 700;text-transform: uppercase;"><?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h1>
                                                <span style="line-height: 29px;color: #fff;font-size: 15px;"><?php echo $enquiry_data['subject_mail']; ?></span>
                                                
                                            </div>
                                        </div>
                                        
                                        <div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">
                                          <div style="float: left; min-width:99%;">
                                              <div style="display: inline-block; vertical-align: bottom; line-height: 0.7;">
                                                  <p style="line-height: 16px;color: #fff;font-size: 13px; margin: 0;"><?php echo $enquiry_data['body_mail']; ?></p>
                                              </div>
                                          </div>
                                        </div>
                                        
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

}elseif($email_tpl['selected_email_tpl'] == 5){       
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
                                    <h2 style=" text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: white; font-size: 22px; font-weight: 400; "><?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
                                    <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: #ff6c6c; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h1>
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
                                                <p style="line-height: 25px;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
                                                <div style="box-sizing: border-box;padding: 20px 30px; width: 100%; text-align: left;border: 1px solid #233648; margin-top: 35px;">
                                                    <div style=" margin: 0 auto 20px; display: table;">
                                                        <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                            <h1 style="text-align: center; font-family: Arial;line-height: 18px;margin: 0;color: #585858;font-size: 20px;font-weight: 700;text-transform: uppercase;"><?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h1>
                                                            <span style="line-height: 29px;color: #ff6c6c;font-size: 15px;"><?php echo $enquiry_data['subject_mail']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">
                                                        <div style="float: left; min-width:99%;">
                                                          
                                                            <p style="line-height: 16px;color: #585858;font-size: 13px; margin: 3px 0 0 0;"><?php echo $enquiry_data['body_mail']; ?></p>
                                                        </div>
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
  }else{                                              //**** Single Product ****//

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
                                    <h2 style=" text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 30px; color: white; font-size: 22px; font-weight: 400; "><?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
                                    <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: #ff6c6c; font-size: 40px; font-weight: 700; text-transform: uppercase;line-height: 41px; "><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name'];?></h1>
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
                                                <p style="line-height: 25px;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
                                                <div style="box-sizing: border-box;padding: 20px 30px; width: 100%; text-align: left;border: 1px solid #233648; margin-top: 35px;">
                                                    <div style=" margin: 0 auto 20px; display: table;">
                                                        <div style="display: inline-block; vertical-align: middle; line-height: 0.7;">
                                                            <h1 style="text-align: center; font-family: Arial;line-height: 18px;margin: 0;color: #585858;font-size: 20px;font-weight: 700;text-transform: uppercase;"><?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h1>
                                                            <span style="line-height: 29px;color: #ff6c6c;font-size: 15px;"><?php echo $enquiry_data['subject_mail']; ?></span>
                                                        </div>
                                                    </div>
                                                    <div style="overflow: hidden; display: inline-block; width: 100%; margin-top: 20px;">
                                                        <div style="float: left; min-width:99%;">
                                                          
                                                            <p style="line-height: 16px;color: #585858;font-size: 13px; margin: 3px 0 0 0;"><?php echo $enquiry_data['body_mail']; ?></p>
                                                        </div>
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

}elseif($email_tpl['selected_email_tpl'] == 6){       
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
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 50px;font-size: 27px; color: #292929; font-weight: 400;text-transform: uppercase; "><?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white;line-height: 37px;font-size: 34px;font-weight: 700; text-transform: uppercase; "><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h1>
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


                                            <p style="line-height: 25px;margin: 0; padding: 0 15px 15px;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
                                            
                                            <div style="text-align: left; padding:15px;background-color: #f2f2f2; ">
                                                <h2 style="font-family: Arial; margin: 0; padding: 0;line-height: 34px; text-transform: uppercase; color: #292929; font-size: 20px; font-weight: 400; display: inline-block;vertical-align: middle;"><?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h2>
                                                <p style="line-height: 16px;color: #292929;font-size: 13px; margin: 0;"><?php echo $enquiry_data['subject_mail']; ?></p>
                                            </div>

                                            <div style="padding: 15px;">
                                                 <p style="line-height: 16px;color: #292929;font-size: 13px; margin: 0;"><?php echo $enquiry_data['body_mail']; ?></p>
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
                                        <h2 style="text-align: center; font-family: Arial; margin: 0; padding: 0; line-height: 50px;font-size: 27px; color: #292929; font-weight: 400;text-transform: uppercase; "><?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
                                        <h1 style="text-align: center; font-family: Arial; margin: 0; padding: 0; color: white;line-height: 37px;font-size: 34px;font-weight: 700; text-transform: uppercase; "><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name'];?></h1>
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


                                            <p style="line-height: 25px;margin: 0; padding: 0 15px 15px;"><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
                                            
                                            <div style="text-align: left; padding:15px;background-color: #f2f2f2; ">
                                                <h2 style="font-family: Arial; margin: 0; padding: 0;line-height: 34px; text-transform: uppercase; color: #292929; font-size: 20px; font-weight: 400; display: inline-block;vertical-align: middle;"><?php echo __('Subject','woocommerce-catalog-enquiry-pro');?></h2>
                                                <p style="line-height: 16px;color: #292929;font-size: 13px; margin: 0;"><?php echo $enquiry_data['subject_mail']; ?></p>
                                            </div>

                                            <div style="padding: 15px;">
                                                 <p style="line-height: 16px;color: #292929;font-size: 13px; margin: 0;"><?php echo $enquiry_data['body_mail']; ?></p>
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
    <h2 style="text-align: center;"><?php echo __('Multiple Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
    <h3 style="text-align: center;"><?php echo __('By ','woocommerce-catalog-enquiry-pro');?> <?php echo $enquiry_data['vendor_name']; ?></h3>
    <p><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
    <h4><?php echo __('Subject : ','woocommerce-catalog-enquiry-pro');?><?php echo $enquiry_data['subject_mail']; ?></h4>
    <p><?php echo $enquiry_data['body_mail']; ?></p>

  <?php }else{                                           //**** Single Product ****//
    ?>
    <h2 style="text-align: center;"><?php echo __('Product Enquiry Response','woocommerce-catalog-enquiry-pro');?></h2>
    <h3 style="text-align: center;"><?php echo __('By ','woocommerce-catalog-enquiry-pro').$enquiry_data['vendor_name'];?></h3>
    <p><?php echo __('First of all, Thanks for showing interest on ','woocommerce-catalog-enquiry-pro') . get_option('blogname');?>.</p>
    <h4><?php echo __('Subject : ','woocommerce-catalog-enquiry-pro');?><?php echo $enquiry_data['subject_mail']; ?></h4>
    <p><?php echo $enquiry_data['body_mail']; ?></p>
    <?php 
  } ?>
  <p style="text-align: center;"><?php echo apply_filters( 'woocommerce_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) ); ?></p>
<?php }