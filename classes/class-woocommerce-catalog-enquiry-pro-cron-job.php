<?php

/**
 * MVX Catalog Enquiry Cron Job Class
 *
 * @version		2.2.0
 * @package		MVX
 * @author 		WC Marketplace
 */
class Woocommerce_Catalog_Enquiry_Pro_Cron_Job {

    public function __construct() {
        wp_schedule_event( time(), 'hourly', 'multiple_product_cron' );
        add_action('multiple_product_cron', array(&$this, 'multiple_product_cron'));
        $this->mvx_catalog_clear_scheduled_event();
    }

    /**
     * Clear scheduled event
     */
    function mvx_catalog_clear_scheduled_event() {
        $cron_hook_identifier = apply_filters('mvx_cron_hook_identifier', array(
            'multiple_product_cron',
        ));
        if ($cron_hook_identifier) {
            foreach ($cron_hook_identifier as $cron_hook) {
                $timestamp = wp_next_scheduled($cron_hook);
                if ($timestamp && apply_filters('mvx_unschedule_'. $cron_hook . '_cron_event', false)) {
                    wp_unschedule_event($timestamp, $cron_hook);
                }
            }
        }
    }

    public function multiple_product_cron(){
        $default = array(
          'posts_per_page'    => -1,
          'post_type'         => 'woocommerce_catalog_enquiry',
          'post_status' => array('publish', 'pending', 'approved', 'rejected', 'expired') 
        );
        $oo = array();
        $enquiry_posts = get_posts( $default );
        foreach ($enquiry_posts as $key => $value) {
           $enquery_product = get_post_meta( $value->ID , '_enquiry_product', true );
            if( is_array( $enquery_product ) ) {
                foreach ($enquery_product as $key_product => $value_product) {

                    $user_name = get_post_meta( $value->ID, '_enquiry_username', true );
                    $user_details_email = get_post_meta( $value->ID, '_enquiry_useremail', true );
                    $user_details_by_email = get_user_by( 'email', $user_details_email );
                    $enq_title = sanitize_text_field(apply_filters( 'woocommerce_catalog_enquiry_prefix', '#')) . $user_details_by_email->ID . $value_product['product_id'] ;
                    $enquiry_post = array(
                        'ID' => '',
                        'post_title' => $enq_title,
                        'post_name'  => sanitize_title($enq_title),
                        'post_type' => 'woocommerce_catalog_enquiry',
                        'post_status' => 'publish'
                        );
                    $enq_id = wp_insert_post( $enquiry_post );

                    update_post_meta( $enq_id, '_enquiry_product', $value_product['product_id'] );
                    update_post_meta( $enq_id, '_enquiry_action_type', 'multiple');
                    update_post_meta( $enq_id, '_enquiry_username', $user_name );
                    update_post_meta( $enq_id, '_enquiry_useremail', $user_details_email ); 
                    update_post_meta( $enq_id, '_migrate_catelog_enquery', $value->ID );                    
                }
                wp_delete_post( $value->ID );    
            }
        }
        wp_clear_scheduled_hook('multiple_product_cron');
    }

}
