<?php
/**
 * fppg Wc Class File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;

}

if ( ! class_exists( 'Fppg_Wc' ) ) {

	/**
	 * Saw class.
	 */
	class Fppg_Wc extends WC_Payment_Gateway {

		var $ipn_url;


		/**
		 * Function Constructor.
		 */
		public function __construct() {

			global $woocommerce;

			$this->id                 = 'frontpay';
			$this->method_title       = __( 'FrontPay', 'fppg' );
			$this->method_description = __( 'Payment Via FrontPay', 'fppg' );
			$this->title              = __( 'FrontPay', 'fppg' );
			$this->has_fields         = true;
			$this->icon               = FPPG_ASSETS_DIR_URL . '/images/logo.png';
			$this->init_form_fields();
			$this->init_settings();
			$this->ipn_url = add_query_arg( 'wc-api', 'Fppg_Wc', home_url( '/' ) );

			foreach ( $this->settings as $setting_key => $value ) {
				$this->$setting_key = $value;
			}

			if ( is_admin() ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}

		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable / Disable', 'fppg' ),
					'label'   => __( 'Enable this payment gateway', 'fppg' ),
					'type'    => 'checkbox',
					'default' => 'no',
				),
				'fp_cancel_url'              => array(
					'title'    => __( 'Cancel Url', 'fppg' ),
					'type'     => 'text',
					'desc_tip' => __( 'Custom Cancel Page Url.', 'fppg' ),
				),
				'fp_merchant_id'     => array(
					'title' => __( 'Merchant ID', 'eshopspay' ),
					'type'  => 'text',
				),
				'fp_merchant_secret' => array(
					'title' => __( 'Merchant Secret', 'eshopspay' ),
					'type'  => 'password',
				),
				'fp_mode'            => array(
					'title'   => __( 'Mode', 'eshopspay' ),
					'type'    => 'select',
					'options' => array(
						'TEST' => 'TEST',
						'LIVE' => 'LIVE',
					),
					'css'     => 'max-width:20%;',
					'default' => 'TEST',
				),
			);
		}

		public function process_payment( $order_id ) {
			$gateway_options = get_option( 'woocommerce_frontpay_settings' );
			$token           = fppg_get_token( $gateway_options['fp_merchant_id'], $gateway_options['fp_merchant_secret'] );

			if ( $token->token ) {
				global $woocommerce;
				$customer_order = wc_get_order( $order_id );
				$url            = fppg_create_order( $order_id, $token->token, $this->get_return_url( $customer_order ), $gateway_options['fp_mode'] );
			}
			if ( $url ) {
				return array(
					'result'   => 'success',
					'redirect' => $url->result->payment_url,
				);
			} else {
				wc_add_notice( 'Something Went Wrong.Please Try later.', 'error' );
				return;
			}

		}

	}

	new Fppg_Wc();
}
