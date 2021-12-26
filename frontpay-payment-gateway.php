<?php
/*
 * Plugin Name: FrontPay Payment Gateway
 * Description: Provides you FrontPay Payment Gateway Integration with Woocommerce.
 * Author: FrontPay
 * Author URI: https://www.frontpay.pk
 * Version: 1.1.1.1
 * Text Domain: fppg
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FPPG_PLUGIN_DIR', __DIR__ );
define( 'FPPG_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'FPPG_ASSETS_DIR_URL', FPPG_PLUGIN_DIR_URL . 'assets' );
define( 'FPPG_ABSPATH', dirname( __FILE__ ) );

require_once FPPG_PLUGIN_DIR . '/includes/helpers.php';

/**
 * Check if WooCommerce is activated.
 */
if ( true == fppg_is_woo_active() ) {
	require_once FPPG_PLUGIN_DIR . '/includes/class-fppg-loader.php';
}
