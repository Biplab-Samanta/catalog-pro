
<?php
/**
 * The template for displaying enquiry details data
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/woocommerce-catalog-enquiry-pro-customer-reply-template.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<form method="post" action="">
<?php
$customer_details = get_customer_enquiry_details( $current_user_id, '', $_POST );
if( !empty($customer_details) ) {
?>
<div class="msg-wrap">

  <!-- Filter section end -->
  <div class="messaging">
    <div class="inbox-msg">
      <div class="inbox-people">
        <div class="inbox-chat">
          <?php foreach ($customer_details as $key => $value) { 
            $product_id = get_post_meta( $value ,'_enquiry_product', true );
            $product = wc_get_product( $product_id );
            if(!$product) continue;

            if(is_active_MVX()) {
              $vendor_details = get_mvx_product_vendors( $product_id ) ? get_mvx_product_vendors( $product_id ) : false;
            } else {
              $vendor_details = false;
            }
            if( $vendor_details ) {
              $to_user_id = $vendor_details->id;
            } else {
              $admin_email = get_option('admin_email');
              $User_details = get_user_by('email', $admin_email);
              $to_user_id = $User_details->data->ID;
            }
            $last_massage = get_user_last_massage( get_current_user_id() ,$to_user_id , $product_id);

            $histrory_unread = get_customer_vendor_admin_conversation_details( get_current_user_id() , $to_user_id, $product_id );
            $count = 0;
            foreach ($histrory_unread as $key => $value1) {
              if( $value1->status == 'unread' && $value1->from_user_id != get_current_user_id() ) {
                $count++;
              }
            }
            ?>
            <div class="chat-list chat-list_<?php echo $value ?>" id="<?php echo $value; ?>" data-option="<?php echo $product_id; ?>" data-conv="<?php echo $to_user_id; ?>" onclick="woocommerce_catalog_enquiry_open_chat(this)">
              <div class="chat-people">
                <div class="chat-img"> <?php echo $product->get_image(); ?> </div>
                <div class="chat-ib">
                  <h5><?php echo $product->get_name() ?> <?php if( $count > 0 ) { ?> <div class="hide_unread<?php echo $value; ?>"> <span class="chat-notify"><?php echo $count; ?></span></div><?php } ?></h5>
                  <p class="customer-short-comming-msg"><?php if( !empty( $last_massage ) ) echo end($last_massage)->chat_message; ?></p>

                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="mesgs"></div>
    </div>   
  </div>
</div>

<?php } else {
  $no_enquiry = apply_filters( 'woocommerce_no_enquiry_msg' , __( 'No enquiry Found' , 'woocommerce-catalog-enquiry-pro' ) )
  ?> <h3 class=" text-center"><?php echo $no_enquiry; ?></h3> <?php
}
?>
</form>