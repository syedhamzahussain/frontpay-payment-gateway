<?php
/**
 * fppg loader Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'Fppg_Loader' ) ) {

	/**
	 * Saw class.
	 */
	class Fppg_Loader {


		/**
		 * Function Constructor.
		 */
		public function __construct() {
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_frontpay_gateway' ), 10, 1 );
			add_action( 'plugins_loaded', array( $this, 'includes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'mark_payment_complete' ), 10, 1 );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'frontpay_unset' ) );
		}

		public function frontpay_unset( $available_gateways ) {
			if ( isset( $available_gateways['frontpay'] ) ) {
				$gateway_options = get_option( 'woocommerce_frontpay_settings' );
				if ( empty( $gateway_options['fp_merchant_id'] ) || empty( $gateway_options['fp_merchant_secret'] ) ) {
					unset( $available_gateways['frontpay'] );
				}
			}
			return $available_gateways;
		}


		public function mark_payment_complete( $order_id ) {
			global $wp;
			$order = wc_get_order( $order_id );

			if ( $order->needs_payment() ) {
				if ( 'frontpay' === $order->get_payment_method() ) {
					$note = 'Successfully Paid using frontpay. ';
					$order->add_order_note( $note );
					$order->payment_complete();
				}
			}
		}

		/*
		 * This action hook registers our PHP class as a WooCommerce payment gateway.
		 */
		public function add_frontpay_gateway( $methods ) {

			if ( ! in_array( 'Fppg_Wc', $methods ) ) {
				$methods[] = 'Fppg_Wc';
			}

			return $methods;
		}

		public function includes() {
			require_once FPPG_PLUGIN_DIR . '/includes/class-fppg-wc.php';
		}

		public function admin_assets() {
			if ( isset( $_GET['section'] ) && 'frontpay' == $_GET['section'] ) {
				wp_enqueue_script( 'fppg-admin-script', FPPG_ASSETS_DIR_URL . '/js/admin.js', array( 'jquery' ), rand() );
			}
		}

	}

	new Fppg_Loader();
}
