<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'MVX_Woocommerce_Catalog_Enquiry_Cart' ) ) :

/**
 * MVX_Woocommerce_Catalog_Enquiry_Cart
 */
class Woocommerce_Catalog_Enquiry_Pro_Cart {
	
    public $session;
    public $enq_cart_content = array();

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'woocommerce_catalog_session_start' ));
        add_action( 'wp_loaded', array( $this, 'init_callback' ));
        add_action( 'wp', array( $this, 'maybe_set_cart_cookies' ), 99 ); 
        add_action( 'shutdown', array( $this, 'maybe_set_cart_cookies' ), 0 );
        add_action( 'woocommerce_catalog_add_to_enquiry_cart', array( $this, 'maybe_set_cart_cookies' ) );
        add_action( 'woocommerce_catalog_enquiry_clean_cron', array( $this, 'clean_session'));
        add_action( 'wp_loaded', array( $this, 'add_to_enquiry_cart_action' ), 30);
	}

	/**
	 * Starts the php session data for the enquiry cart.
	 */
	function woocommerce_catalog_session_start(){
		if( ! isset( $_COOKIE['woocommerce_items_in_cart'] ) ) {
			do_action( 'woocommerce_set_cart_cookies', true );
		}
		$this->session = new Woocommerce_Catalog_Enquiry_Pro_Session();
		$this->set_session();
	}

	function init_callback() {
        $this->get_woocommerce_catalog_enquiry_cart_session();
        $this->session->set_customer_session_cookie(true);
        $this->woocommerce_catalog_enquiry_validation_schedule();
    }

    function get_woocommerce_catalog_enquiry_cart_session() {
        $this->enq_cart_content = $this->session->get( 'woocommerce_catalog_enquiry_cart', array() );
        return $this->enq_cart_content;
    }

    public function woocommerce_catalog_enquiry_validation_schedule(){

        if( ! wp_next_scheduled( 'woocommerce_catalog_enquiry_validation_schedule' ) ){
            $ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';
            wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS'), 'daily', 'woocommerce_catalog_enquiry_validation_schedule' );
        }

        if ( !wp_next_scheduled( 'woocommerce_catalog_enquiry_clean_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'woocommerce_catalog_enquiry_clean_cron' );
        }
    }

    public function clean_session(){
        global $wpdb;
        $query = $wpdb->query("DELETE FROM ". $wpdb->prefix ."options  WHERE option_name LIKE '_woocommerce_catalog_session_%'");
    }


	/**
	 * Sets the php session data for the enquiry cart.
	 */
	public function set_session($enquiry_cart_session = array(), $can_be_empty = false) {

		if ( empty( $enquiry_cart_session ) && !$can_be_empty) {
            $enquiry_cart_session = $this->get_woocommerce_catalog_enquiry_cart_session();
        }
        // Set woocommerce_catalog_enquiry_cart  session data
        $this->session->set( 'woocommerce_catalog_enquiry_cart', $enquiry_cart_session );
	}

	public function unset_session() {
        $this->session->__unset( 'woocommerce_catalog_enquiry_cart' );
    }

	function maybe_set_cart_cookies() {
        $set = true;

        if ( !headers_sent() ) {
            if ( sizeof( $this->enq_cart_content ) > 0 ) {
                $this->set_enquiry_cart_cookies( true );
                $set = true;
            }
            elseif ( isset( $_COOKIE['woocommerce_catalog_enquiry_items_in_cart'] ) ) {
                $this->set_enquiry_cart_cookies( false );
                $set = false;
            }
        }

        do_action( 'woocommerce_catalog_set_enquiry_cart_cookies', $set );
    }

    private function set_enquiry_cart_cookies( $set = true ) {
        if ( $set ) {
            wc_setcookie( 'woocommerce_catalog_enquiry_items_in_cart', 1 );
            wc_setcookie( 'woocommerce_catalog_enquiry_hash', md5( json_encode( $this->enq_cart_content ) ) );
        }
        elseif ( isset( $_COOKIE['woocommerce_catalog_enquiry_items_in_cart'] ) ) {
            wc_setcookie( 'woocommerce_catalog_enquiry_items_in_cart', 0, time() - HOUR_IN_SECONDS );
            wc_setcookie( 'woocommerce_catalog_enquiry_hash', '', time() - HOUR_IN_SECONDS );
        }
    }

	public function add_to_enquiry_cart_action() {
		global $Woocommerce_Catalog_Enquiry_Pro;
	    if ( empty( $_REQUEST['add-to-enquiry'] ) || ! is_numeric( $_REQUEST['add-to-enquiry'] ) ) {
		    return;
	    }

	    $product_id      = apply_filters( 'woocommerce_add_to_enquiry_cart_product_id', absint( $_REQUEST['add-to-enquiry'] ) );
	    $adding_to_enquiry = wc_get_product( $product_id );
	    $variation_id    = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
	    $quantity        = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
	    $variations      = isset($_SESSION['variation_list']) ? $_SESSION['variation_list'] : array();
	    $error           = false;

	    $add_to_cart_handler = apply_filters( 'woocommerce_add_to_enquiry_handler', $adding_to_enquiry->get_type(), $adding_to_enquiry );

	    /*if ( 'variation' === $add_to_cart_handler ) {
		    if ( isset( $adding_to_enquiry->variation_id ) ) {
			    $product_id   = $adding_to_enquiry->id;
			    $variation_id = $adding_to_enquiry->variation_id;
		    }
	    }
	    if ( 'variable' === $add_to_cart_handler ) {
		    if ( empty( $variation_id ) ) {
			    $variation_id = $adding_to_enquiry->get_matching_variation( wp_unslash( $_POST ) );
		    }
		    if ( ! empty( $variation_id ) ) {
			    $attributes = $adding_to_enquiry->get_attributes();
			    $variation  = wc_get_product( $variation_id );
			    foreach ( $attributes as $attribute ) {
				    if ( ! $attribute['is_variation'] ) {
					    continue;
				    }
				    $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );
				    if ( isset( $_REQUEST[ $taxonomy ] ) ) {
					    if ( $attribute['is_taxonomy'] ) {
						    $value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
					    } else {
						    $value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
					    }
					    $valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';
					    // Allow if valid
					    if ( '' === $valid_value || $valid_value === $value ) {
						    $variations[ $taxonomy ] = $value;
						    continue;
					    }

				    } else {
					    $missing_attributes[] = wc_attribute_label( $attribute['name'] );
				    }
			    }

			    if ( ! empty( $missing_attributes ) ) {
				    $error = true;
				    wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'wcce-woocommerce-request-a-quote' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
			    }
		    } elseif ( empty( $variation_id ) ) {
			    $error = true;
			    wc_add_notice( __( 'Please choose product options&hellip;', 'wcce-woocommerce-request-a-enquiry' ), 'error' );
		    }
	    }*/

	    if ( $error ) {
		    return;
	    }

	    $enquiry_data = apply_filters( 'woocommerce_catalog_add_to_enquiry_cart_item_data', array(
		    'product_id'   => $product_id,
		    'variation_id' => $variation_id,
		    'quantity'     => $quantity,
		    'variation'    => $variations
	    ),$product_id);

	    $return = $this->add_enquiry( $enquiry_data );

	    if ( $return == 'true' ) {
		    $message = apply_filters( 'woocommerce_catalog_enquiry_product_added_to_list_message', __( 'Product added to Enquiry Cart!', 'woocommerce-catalog-enquiry-pro' ));
		    wc_add_notice( $message, 'success' );
	    } elseif ( $return == 'exists' ) {
		    $message = apply_filters( 'woocommerce_catalog_enquiry_product_already_in_list_message', __( 'Product already in Enquiry Cart.', 'woocommerce-catalog-enquiry-pro' ) );
		    wc_add_notice( $message, 'notice' );
	    }
    }

    public function add_enquiry( $enquiry_data ) {

        $enquiry_data['quantity'] = ( isset( $enquiry_data['quantity'] ) ) ? (int) $enquiry_data['quantity'] : 1;
        $return = '';
        
        do_action( 'woocommerce_catalog_add_to_enquiry_cart', $enquiry_data );
        
        if ( !$this->exists_enquiry( $enquiry_data['product_id'] ) ) {
            $enquiry = array(
                'product_id'    => $enquiry_data['product_id'],
                'variation'     => $enquiry_data['variation'],
                'quantity'      => $enquiry_data['quantity']
            );

            $this->enq_cart_content[md5( $enquiry_data['product_id'] )] = $enquiry;
        }
        else {
            $return = 'exists';
        }

        if ( $return != 'exists' ) {
            $this->set_session( $this->enq_cart_content );
            $return = 'true';
            $this->set_enquiry_cart_cookies( sizeof( $this->enq_cart_content ) > 0 );
        }
        return $return;
    }

    public function exists_enquiry( $product_id, $variation_id = false ) {
        if ( $variation_id ) {
            $key_to_find = md5( $product_id . $variation_id );
        } else {
            $key_to_find = md5( $product_id );
        }
        if ( array_key_exists( $key_to_find, $this->enq_cart_content ) ) {
            $this->errors[] = __( 'Product already in Enquiry Cart.', 'woocommerce-catalog-enquiry-pro' );
            return true;
        }
        return false;
    }

    public function get_enquiry_data() {
        return $this->enq_cart_content;
    }

    public function get_enquiry_cart_page_url() {
        $woocommerce_catalog_cart_page_id = get_option( 'woocommerce_catalog_enq_cart_page_id' );
        $base_url     = get_the_permalink( $woocommerce_catalog_cart_page_id );

        return apply_filters( 'woocommerce_catalog_enquiry_cart_page_url', $base_url );
    }

    public function is_empty_enquiry() {
        return empty( $this->enq_cart_content );
    }

    public function remove_enquiry( $key ) {

        if ( isset( $this->enq_cart_content[$key] ) ) {
            unset( $this->enq_cart_content[$key] );
            $this->set_session( $this->enq_cart_content, true );
            return true;
        }
        else {
            return false;
        }
    }

    public function clear_enquiry_cart() {
        $this->enq_cart_content = array();
        $this->set_session( $this->enq_cart_content, true );
    }

    public function update_enquiry( $key, $field = false, $value = '' ) {
        if ( $field && isset( $this->enq_cart_content[$key][$field] ) ) {
            $this->enq_cart_content[$key][$field] = $value;
            $this->set_session( $this->enq_cart_content );
        }
        elseif ( isset( $this->enq_cart_content[$key] ) ) {
            $this->enq_cart_content[$key] = $value;
            $this->set_session( $this->enq_cart_content );
        }
        else {
            return false;
        }
        $this->set_session( $this->enq_cart_content );
        return true;
    }
}

endif;