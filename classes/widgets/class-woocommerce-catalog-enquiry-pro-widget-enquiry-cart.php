<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WCCE Enquiry Cart Widget.
 *
 * Displays WCCE Enquiry cart widget.
 *
 * @author   WC Marketplace
 * @category Widgets
 * @package  WCCE/Widgets
 * @version  1.0.5
 * @extends  WC_Widget
 */
class Woocommerce_Catalog_Enquiry_Pro_Widget_Enquiry_Cart extends WC_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $Woocommerce_Catalog_Enquiry_Pro;
		$this->widget_cssclass    = 'woocommerce widget_woocommerce_catalog_enquiry_cart widget_shopping_cart';
		$this->widget_description = __( "Display the user's catalog enquiry cart in the sidebar.", 'woocommerce-catalog-enquiry-pro' );
		$this->widget_id          = 'woocommerce_catalog_widget_enquiry_cart';
		$this->widget_name        = __( 'Catalog Enquiry Cart', 'woocommerce-catalog-enquiry-pro' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Enquiry Cart', 'woocommerce-catalog-enquiry-pro' ),
				'label' => __( 'Title', 'woocommerce-catalog-enquiry-pro' ),
			),
			'hide_if_empty' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Hide if enquiry cart is empty', 'woocommerce-catalog-enquiry-pro' ),
			),
			/*'show_send_enq_btn' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show send enquiry button on widget', 'woocommerce-catalog-enquiry-pro' ),
			),*/
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		global $Woocommerce_Catalog_Enquiry_Pro, $Woocommerce_Catalog_Enquiry_Pro_Cart;
		if ( apply_filters( 'woocommerce_catalog_widget_enquiry_cart_is_hidden', is_enquiry_cart() ) ) {
			return;
		}

		$hide_if_empty = empty( $instance['hide_if_empty'] ) ? 0 : 1;
		$show_send_enq_btn = empty( $instance['show_send_enq_btn'] ) ? 0 : 1;
		if( $show_send_enq_btn )
			add_action( 'woocommerce_catalog_mini_enquiry_cart_buttons', array('MVX_Woocommerce_Catalog_Enquiry_Frontend', 'woocommerce_catalog_mini_enquiry_cart_send_enquiry_button'), 20 );

		$this->widget_start( $args, $instance );

		if ( $hide_if_empty ) {
			echo '<div class="hide_enquiry_cart_widget_if_empty">';
		}

		// Insert cart widget placeholder - code in woocommerce.js will update this on page load
		echo '<div class="widget_woocommerce_catalog_enquiry_content">';
		
		echo $Woocommerce_Catalog_Enquiry_Pro->template->get_template('woocommerce-catalog-enquiry-pro-mini-cart.php',array());

		echo '</div>';
		if ( $hide_if_empty ) {
			echo '</div>';
		}

		$this->widget_end( $args );
	}

}
