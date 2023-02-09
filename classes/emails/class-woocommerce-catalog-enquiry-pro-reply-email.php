<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Woocommerce_Catalog_Enquiry_Pro_Reply_Email' ) ) :

/**
 * Email for user about enquiry details
 *
 * An confirmation email will be sent to the customer when they subscribe product.
 *
 * @class 		Woocommerce_Catalog_Enquiry_Pro_Reply_Email
 * @version		1.0.0
 * @author 		WC Marketplace
 * @extends 	WC_Email
 */
class Woocommerce_Catalog_Enquiry_Pro_Reply_Email extends WC_Email {
	
	public $enquiry_data;
	public $recipient;
	public $sender ='';

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $Woocommerce_Catalog_Enquiry_Pro;
		
		$this->id 				= 'woocommerce_catalog_reply_email';
		$this->title 			= __( 'Catalog Enquiry Reply', 'woocommerce-catalog-enquiry-pro' );
		$this->description		= __( 'Customer will get an email when admin replies on customer enquiry', 'woocommerce-catalog-enquiry-pro' );

		$this->template_html 	= 'emails/woocommerce-catalog-enquiry-pro-reply-email.php';
		$this->template_plain 	= 'emails/plain/woocommerce-catalog-enquiry-pro-reply-email.php';

		$this->subject 			= __( 'Enquiry reply by [{site_title}] {owner_name}', 'woocommerce-catalog-enquiry-pro');
		$this->heading      	= __( 'Enquiry Reply on [{product_name}]', 'woocommerce-catalog-enquiry-pro');
		$this->template_base = $Woocommerce_Catalog_Enquiry_Pro->plugin_path . 'templates/';
		
		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $enquiry_data ) {
		global $Woocommerce_Catalog_Enquiry_Pro;
		if(!empty($enquiry_data['enquiry_id'])){
			$enquiry = get_post($enquiry_data['enquiry_id']);
			$product_data = get_post_meta( $enquiry->ID , '_enquiry_product', true );
			$user = wp_get_current_user();
			$user_info = get_userdata($user->ID);
	    	$vendor = false;
	    	// MVX
	    	if(is_active_MVX()){
	    		$vendor = is_user_mvx_vendor($user->ID);
	    	}
	    	if($vendor){
				$this->sender = $user_info->user_email;
	    	}else{
	    		$this->sender = get_option( 'woocommerce_email_from_address' );
	    	}
	    	
			if(isset($enquiry_data['enquiry_action_type'])){
				if($enquiry_data['enquiry_action_type'] == 'multiple'){
					$this->subject 	= $enquiry_data['subject_mail'];
					$this->heading  = __( 'Multiple Product Enquiry Reply', 'woocommerce-catalog-enquiry-pro');
					// set from email for mvx vendor
					
				}else{
					$product = wc_get_product( $product_data );
					// set from email for mvx vendor
					
					$this->find[ ]  = '{owner_name}';
					$owner = 'Admin';
					if($vendor){ $owner = $user_info->user_login;}
					$this->replace[ ]   = $owner;

					$this->find[ ]  = '{product_name}';
					$product_name = $product->get_title();
					$this->replace[ ]   = $product_name;
				}
			}

			$this->enquiry_data = $enquiry_data;
			$this->recipient = get_post_meta( $enquiry->ID , '_enquiry_useremail', true );
			
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}
			
			$send = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			return $send;
		}
	}
	
	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
			return apply_filters( 'woocommerce_catalog_reply_email_subject', $this->format_string( $this->subject ), $this->object );
	}

	public function get_from_name( $from_name = "" ) {
        $from_name = apply_filters( 'woocommerce_catalog_reply_email_from_name', get_option( 'woocommerce_email_from_name' ), $this );
        return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
    }

    /**
     * Get the from address for outgoing emails.
     * @return string
     */
    public function get_from_address( $from_address = "" ) {
        $from_address = apply_filters( 'woocommerce_catalog_reply_email_from_address', $this->sender, $this );
        return sanitize_email( $from_address );
    }

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
			return apply_filters( 'woocommerce_email_heading_stock_alert', $this->format_string( $this->heading ), $this->object );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'email_heading' => $this->get_heading(),
			'enquiry_data' => $this->enquiry_data,
			'customer_email' => $this->recipient,
			'sent_to_admin' => false,
			'plain_text' => false
		), '', $this->template_base);
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'email_heading' => $this->get_heading(),
			'enquiry_data' => $this->enquiry_data,
			'customer_email' => $this->recipient,
			'sent_to_admin' => false,
			'plain_text' => true
		) ,'', $this->template_base );
		return ob_get_clean();
	}
	
}

endif;

return new Woocommerce_Catalog_Enquiry_Pro_Reply_Email();
