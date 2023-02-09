<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Woocommerce_Catalog_Enquiry_Pro_Send_Email' ) ) :

/**
 * Email to Admin for enquiry details
 *
 * An email will be sent to the admin when customer subscribe an out of stock product.
 *
 * @class 		Woocommerce_Catalog_Enquiry_Pro_Send_Email
 * @version		1.0.0
 * @author 		WC Marketplace
 * @extends 	WC_Email
 */
class Woocommerce_Catalog_Enquiry_Pro_Send_Email extends WC_Email {
	
	public $product_data;
	public $customer_email;
	public $enquiry_data;
	public $email_template;
	public $attachments;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $Woocommerce_Catalog_Enquiry_Pro;
		
		$this->id 			= 'woocommerce_catalog_send_email';
		$this->title 			= __( 'Catalog Enquiry Send', 'woocommerce-catalog-enquiry-pro' );
		$this->description		= __( 'Admin and Customer will get an alert when customer enquiry about a product', 'woocommerce-catalog-enquiry-pro' );

		$this->template_html 	= 'emails/woocommerce-catalog-enquiry-pro-send-email.php';
		$this->template_plain 	= 'emails/plain/woocommerce-catalog-enquiry-pro-send-email.php';
	
		$this->subject 		= __( '[{site_title}] Product Enquiry for |PRODUCT_NAME| by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');
		
		$this->heading      	= __( 'Product Enquiry for |PRODUCT_NAME|', 'woocommerce-catalog-enquiry-pro');
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
	function trigger( $recipient, $product_data, $enquiry_data ) {
		global $Woocommerce_Catalog_Enquiry_Pro;
		//print_r(array($recipient, $product_data, $enquiry_data));die;
		$settings = get_woocommerce_catalog_catalog_settings();

		if (isset($enquiry_data['enquiry_action_type']) && $enquiry_data['enquiry_action_type'] == 'multiple') {
			// MVX
			if (isset($enquiry_data['product_author']) && !empty($enquiry_data['product_author'])) {
				$author_id = (int)$enquiry_data['product_author'];
				if (is_active_MVX() && is_user_mvx_vendor($author_id)) {
					$settings = get_woocommerce_catalog_catalog_settings($author_id);
					if (isset($settings['custom_email_subject']) && !empty($settings['custom_email_subject'])) {
						$this->subject = $settings['custom_email_subject'];
					} else {
						$this->subject = __( '[{site_title}] Multiple Product Enquiry by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');
					}
					$this->email_template = $settings['selected_email_tpl'];

				}
			} else {
				if (isset($settings['custom_email_subject']) && !empty($settings['custom_email_subject'])) {
					$this->subject = $settings['custom_email_subject'];
				} else {
					$this->subject = __( '[{site_title}] Multiple Product Enquiry by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');
				}
				$this->email_template = isset($settings['selected_email_tpl']) ? $settings['selected_email_tpl'] : '';
			}

			$this->heading      = __( 'Multiple Product Enquiry by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');

			$this->find[ ]      = '|PRODUCT_NAME|';
			$this->replace[ ]   = 'Multiple Products';

			$this->find[ ]      = '|PRODUCT_URL|';
			$this->replace[ ]   = '';

			$this->find[ ]      = '|PRODUCT_SKU|';
			$this->replace[ ]   = '';

			$this->find[ ]      = '|PRODUCT_TYPE|';
			$this->replace[ ]   = '';

			$this->find[ ]      = '|USER_EMAIL|';
			$this->user_email    = $enquiry_data['user_email'];
			$this->replace[ ]   = $this->user_email;

			$this->find[ ]      = '|USER_NAME|';
			$this->user_name    = $enquiry_data['user_name'];
			$this->replace[ ]   = $this->user_name;

			$this->product_data = $product_data;
			$this->enquiry_data = $enquiry_data;
		} else {
			if (isset($enquiry_data['product_author']) && !empty($enquiry_data['product_author'])) {
				$author_id = (int)$enquiry_data['product_author'];
				if (is_active_MVX() && is_user_mvx_vendor($author_id)) {
					$settings = get_woocommerce_catalog_catalog_settings($author_id);
					if (isset($settings['custom_email_subject']) && !empty($settings['custom_email_subject'])) {
						$this->subject = $settings['custom_email_subject'];
					} else {
						$this->subject = __( '[{site_title}] Product Enquiry for |PRODUCT_NAME| by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');
					}
					$this->email_template = isset($settings['selected_email_tpl']) ? $settings['selected_email_tpl'] : '';
				} else {
					if (isset($settings['custom_email_subject']) && !empty($settings['custom_email_subject'])) {
						$this->subject = $settings['custom_email_subject'];
					} else {
						$this->subject = __( '[{site_title}] Product Enquiry for |PRODUCT_NAME| by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');
					}
					$this->email_template = $settings['selected_email_tpl'];
				}
			} else {
				if (isset($settings['custom_email_subject']) && !empty($settings['custom_email_subject'])) {
					$this->subject = $settings['custom_email_subject'];
				} else {
					$this->subject = __( '[{site_title}] Product Enquiry for |PRODUCT_NAME| by |USER_NAME|', 'woocommerce-catalog-enquiry-pro');
				}
				$this->email_template = isset($settings['selected_email_tpl']) ? $settings['selected_email_tpl'] : '';
			}
				
			$product = wc_get_product( $product_data );

			$this->find[ ]      = '|PRODUCT_NAME|';
			$this->product_name = $product->get_title();
			$this->replace[ ]   = $this->product_name;

			$this->find[ ]      = '|PRODUCT_URL|';
			$this->replace[ ]   = $product->get_permalink();

			$this->find[ ]      = '|PRODUCT_SKU|';
			$this->replace[ ]   = $product->get_sku();

			$this->find[ ]      = '|PRODUCT_TYPE|';
			$this->replace[ ]   = $product->get_type();

			$this->find[ ]      = '|USER_NAME|';
			$this->user_name    = $enquiry_data['user_name'];
			$this->replace[ ]   = $this->user_name;

			$this->find[ ]      = '|USER_EMAIL|';
			$this->user_email    = $enquiry_data['user_email'];
			$this->replace[ ]   = $this->user_email;

			$this->product_data = $product_data;
			$this->enquiry_data = $enquiry_data;
		}

		$this->recipient = $recipient;

		// Set email attachments
		if (isset($enquiry_data['user_attachment']) && !empty($enquiry_data['user_attachment']) && count($enquiry_data['user_attachment']) > 0) {
			$this->attachments = $enquiry_data['user_attachment'];
		}
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}
		
		$send = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		return $send;
	}

	/**
     * Get email attachments.
     *
     * @return string
     */
    public function get_attachments() {
        return apply_filters( 'woocommerce_catalog_enquiry_send_email_attachments', $this->attachments, $this->id, $this->object );
    }

	
	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
		return apply_filters( 'woocommerce_catalog_enquiry_send_email_subject', $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
			return apply_filters( 'woocommerce_catalog_enquiry_send_email_heading', $this->format_string( $this->heading ), $this->object );
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
			'email_tpl' => $this->email_template,
			'product_data' => $this->product_data,
			'enquiry_data' => $this->enquiry_data,
			'customer_email' => $this->customer_email,
			'sent_to_admin' => true,
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
			'email_tpl' => $this->email_template,
			'product_data' => $this->product_data,
			'enquiry_data' => $this->enquiry_data,
			'customer_email' => $this->customer_email,
			'sent_to_admin' => true,
			'plain_text' => true
		) ,'', $this->template_base );
		return ob_get_clean();
	}
	
}

endif;

return new Woocommerce_Catalog_Enquiry_Pro_Send_Email();
