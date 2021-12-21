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
				'title'              => array(
					'title'    => __( 'Title', 'fppg' ),
					'type'     => 'text',
					'desc_tip' => __( 'Payment title of checkout process.', 'fppg' ),
					'default'  => __( 'Credit card', 'fppg' ),
				),
				'description'        => array(
					'title'    => __( 'Description', 'fppg' ),
					'type'     => 'textarea',
					'desc_tip' => __( 'Payment description of checkout process.', 'fppg' ),
					'default'  => __( 'Successfully payment through credit card.', 'fppg' ),
					'css'      => 'max-width:450px;',
				),
				'fp_merchant_id'     => array(
					'title' => __( 'Merchant ID', 'eshopspay' ),
					'type'  => 'text',
				),
				'fp_merchant_secret' => array(
					'title' => __( 'Merchant Secret', 'eshopspay' ),
					'type'  => 'password',
				),
			);
		}

		public function process_payment( $order_id ) {

		}

	}

	new Fppg_Wc();
}
