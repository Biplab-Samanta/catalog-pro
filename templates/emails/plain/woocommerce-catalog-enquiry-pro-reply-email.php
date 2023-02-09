<?php
/**
 * WCCE Reply Email 
 *
 * Override this template by copying it to yourtheme/woocommerce-catalog-enquiry-pro/emails/plain/woocommerce-catalog-enquiry-pro-reply-email.php
 *
 * @author    WC Marketplace
 * @package   woocommerce-catalog-enquiry-pro/Templates
 * @version   1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $Woocommerce_Catalog_Enquiry_Pro;

echo $email_heading . "\n\n";

echo sprintf( __( "First of all, Thanks for showing interest. Enquiry Response", 'woocommerce-catalog-enquiry-pro' ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo "\n Subject : ".$enquiry_data['subject_mail'];

echo "\n\n Message : ".$enquiry_data['body_mail'];

echo apply_filters( 'woocommerce_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiry Pro', 'woocommerce-catalog-enquiry-pro' ), get_bloginfo( 'name', 'display' ) ) );