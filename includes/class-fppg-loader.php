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
			add_filter( 'woocommerce_payment_gateways', array( $this, 'wceshopspay_add_gateway' ), 10, 1 );
			add_action( 'plugins_loaded', array( $this, 'includes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		}

		/*
		 * This action hook registers our PHP class as a WooCommerce payment gateway.
		 */
		public function wceshopspay_add_gateway( $methods ) {
			if ( ! in_array( 'Fppg_Wc', $methods ) ) {
				$methods[] = 'Fppg_Wc';
			}
			return $methods;
		}

		public function includes() {
			require_once FPPG_PLUGIN_DIR . '/includes/class-fppg-wc.php';
		}

		public function admin_assets() {
			if( isset( $_GET['section'] ) && 'frontpay' == $_GET['section'] ){
				wp_enqueue_script( 'fppg-admin-script', FPPG_ASSETS_DIR_URL . '/js/admin.js', array( 'jquery' ), rand() );
			}
		}

	}

	new Fppg_Loader();
}
