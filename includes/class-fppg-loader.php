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
			add_action( 'wp_enqueue_scripts', array( $this, 'front_assets' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'mark_payment_complete' ), 10, 1 );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'frontpay_unset' ) );
			add_action('admin_init',array( $this, 'admin_init' ),99 );
		}

		public function admin_init(){

			// set fixed title and desc
			$fp_settings = get_option('woocommerce_frontpay_settings');
			$fp_settings['title'] = 'testing title';
			$fp_settings['description'] = 'testing desc';

			if( isset( $_GET['section'] ) && 'frontpay' == $_GET['section'] && !empty( $fp_settings['fp_merchant_id'] ) && !empty( $fp_settings['fp_merchant_secret'] ) ){
				$merchant_found = fppg_get_token( $fp_settings['fp_merchant_id'], $fp_settings['fp_merchant_secret'] );

				if(1 != $merchant_found->status ){
					add_action( 'admin_notices', 'fppg_is_not_cred_valid' );
					unset($fp_settings['fp_merchant_id']);
					unset($fp_settings['fp_merchant_secret']);
				}
				else{
					if( isset( $_POST['woocommerce_frontpay_fp_merchant_id'] ) && isset( $_POST['woocommerce_frontpay_fp_merchant_secret'] ) ){
						add_action( 'admin_notices', 'fppg_is_cred_valid' );
					}
				}
			}

			update_option('woocommerce_frontpay_settings',$fp_settings);

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
					WC()->cart->empty_cart();
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

		public function front_assets() {
			wp_enqueue_style( 'fppg-front-style', FPPG_ASSETS_DIR_URL . '/css/style.css' );
		}

		public function admin_assets() {
			if ( isset( $_GET['section'] ) && 'frontpay' == $_GET['section'] ) {
				wp_enqueue_script( 'fppg-admin-script', FPPG_ASSETS_DIR_URL . '/js/admin.js', array( 'jquery' ), rand() );
			}

			wp_localize_script(
				'fppg-admin-script',
				'fppg_object',
				array(
					'fppg_logo' => FPPG_ASSETS_DIR_URL . '/images/logo.png',
				)
			);
		}

	}

	new Fppg_Loader();
}
