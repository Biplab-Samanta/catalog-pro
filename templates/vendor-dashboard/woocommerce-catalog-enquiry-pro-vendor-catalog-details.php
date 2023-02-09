<?php
/**
 * The template for displaying enquiry details data
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/vendor-dashboard/woocommerce-catalog-enquiry-pro-vendor-catalog-details.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $MVX;
?>
<form method="post" action="">
<?php
$user = wp_get_current_user();
$vendor = get_mvx_vendor($user->ID);
$vendor_product_enquiry = get_vendor_enquiry_details($vendor->id, '', $_POST);
?>
<div class="msg-wrap">
  <!-- Filter section start -->
  <div class='selectInputArea extraCls vendorHeaderCls'> 
    <div>
      <!--Search by status:-->
      <?php 
      $enquiry_status = apply_filters( 'mvx_vendor_catalog_status', array(
        'base' => __( 'All', 'woocommerce-catalog-enquiry-pro' ),
        'read_name' => __( 'Read', 'woocommerce-catalog-enquiry-pro' ),
        'unread_name' => __( 'Unread', 'woocommerce-catalog-enquiry-pro' ),
        'completed_name' => __( 'Closed', 'woocommerce-catalog-enquiry-pro' )

        ) );
        ?>
        <div class='singleInputCls'>
          <p><?php esc_html_e('Search By Status', 'woocommerce-catalog-enquiry-pro'); ?></p>
          <select id="others-choice" name="others_choice">
            <?php foreach ($enquiry_status as $key => $value) { ?>
            <option value="<?php esc_attr_e($key); ?>"><?php esc_html_e($value); ?></option>
            <?php } ?>
          </select>
        </div>

        <!--search by different type:-->
        <?php 
        $enquiry_search_type = apply_filters( 'mvx_vendor_catalog_status', array(
        'base' => __( 'Select a Segment', 'woocommerce-catalog-enquiry-pro' ),
        'product_name' => __( 'Product Name', 'woocommerce-catalog-enquiry-pro' ),
        'customer_name' => __( 'Customer name', 'woocommerce-catalog-enquiry-pro' ),
        'enquiry_number' => __( 'Enquiry Number', 'woocommerce-catalog-enquiry-pro' )
        ) );
          ?>
          <div class='singleInputCls'>
            <p><?php esc_html_e('Search By', 'woocommerce-catalog-enquiry-pro'); ?></p>
            <select id="first-choice" name="first_choice">
              <?php foreach ($enquiry_search_type as $key => $value) { ?>
              <option value="<?php esc_attr_e($key); ?>"><?php esc_html_e($value); ?></option>
              <?php } ?>
            </select>
          </div>

          <!--Select options :-->
          <div class='singleInputCls'>
            <p><?php esc_html_e('Choose Segment', 'woocommerce-catalog-enquiry-pro'); ?></p>
            <select multiple='multiple' id="second-choice" name="second_choice[]" >
              <option><?php esc_html_e('Please choose from above', 'woocommerce-catalog-enquiry-pro'); ?></option>
            </select>
            <input type="submit" name="filter_action" id="post-query-submit" class="button btn btn-default" value="<?php esc_attr_e('Filter', 'woocommerce-catalog-enquiry-pro'); ?>">   
          </div>
        </div>


        <!--Date Range:-->
        <div class='dateFieldInput'>
          <div class='singleInputCls'>
            <p><?php esc_html_e('Date Range:', 'woocommerce-catalog-enquiry-pro'); ?></p>
            <span class="date-inp-wrap">
              <input type="text" name="catalog_start_date_order" class="pickdate gap1 catalog_start_date_order form-control" placeholder="<?php esc_attr_e('from', 'dc-woocommerce-multi-vendor'); ?>" value="<?php echo isset($_POST['catalog_start_date_order']) ? $_POST['catalog_start_date_order'] : date('Y-m-01'); ?>" />
            </span> 
            <span class="date-inp-wrap">
              <input type="text" name="catalog_end_date_order" class="pickdate catalog_end_date_order form-control" placeholder="<?php esc_attr_e('to', 'dc-woocommerce-multi-vendor'); ?>" value="<?php echo isset($_POST['catalog_end_date_order']) ? $_POST['catalog_end_date_order'] : date('Y-m-d'); ?>" />
            </span>
            <input type="submit" name="date_action" id="post-query-submit" class="button btn btn-default" value="<?php esc_attr_e('Filter', 'woocommerce-catalog-enquiry-pro'); ?>">
          </div>
        </div>
      </div>
      <!-- Filter section end -->

  <div class="messaging">
    <div class="inbox-msg">
      <div class="inbox-people">
        <div class="inbox-chat">
          <?php 
          if( !empty( $vendor_product_enquiry ) ) {

          foreach ($vendor_product_enquiry as $key => $value) { 

            $product_id = get_post_meta( $value ,'_enquiry_product', true );
            $enquiry_user_email = get_post_meta( $value , '_enquiry_useremail', true );
            $user_details = get_user_by( 'ID' , get_post_field ('post_author', $value) );
            $product = wc_get_product( $product_id );
            if(!$product) continue;
            $to_user_id = $user_details ? $user_details->data->ID : 0;
            $last_massage = get_user_last_massage( get_current_user_id() , $to_user_id, $product_id);

            $histrory_unread = get_customer_vendor_admin_conversation_details( get_current_user_id() , $to_user_id, $product_id );
            $count = 0;
            foreach ($histrory_unread as $key => $value1) {
              if( $value1->status == 'unread' && $value1->from_user_id != get_current_user_id() ){
                $count++;
              }
            }
            ?>
            <div class="chat-list chat-list_<?php echo $value ?>" id="<?php echo $value; ?>" data-option="<?php echo $product_id; ?>" data-conv="<?php echo $to_user_id; ?>" onclick="woocommerce_catalog_enquiry_open_chat(this)">
              <div class="chat-people">
                <div class="chat-img"> <?php echo $product->get_image(); ?> </div>
                <div class="chat-ib">
                  <h5><?php echo $product->get_name() ?> <?php if( $count > 0 ) { ?><div class="hide_unread<?php echo $value; ?>">  <span class="chat-notify"><?php echo $count; ?></span></div><?php } ?></h5>
                  <p class="vendor-short-comming-msg"><?php if( !empty( $last_massage ) ) echo end($last_massage)->chat_message; ?></p>
                </div>
              </div>
            </div>
            <?php } ?>

          <?php } else { 
            $no_enquiry = apply_filters( 'woocommerce_no_enquiry_msg' , __( '*** No enquiry Found ***' , 'woocommerce-catalog-enquiry-pro' ) );
            ?>
            <h3 class=" text-center"><?php echo $no_enquiry; ?></h3>
          <?php } ?>

          </div>
      </div>
      <div class="mesgs"></div>
    </div>   
  </div>
</div>
</form>