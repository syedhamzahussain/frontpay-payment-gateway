<?php
/**
 * Functions File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fppg_is_woo_active() {
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	if ( true == ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) ) {
		return true;
	}

	return false;
}

function fppg_get_token( $fp_merchant_id, $fp_merchant_secret ) {
	$data['fp_merchant_id']     = $fp_merchant_id;
	$data['fp_merchant_secret'] = $fp_merchant_secret;
	$url                        = 'https://portal.frontpay.pk/api/create-token';
	$ch                         = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true ); // this should be set to true in production
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$responseData = curl_exec( $ch );
	if ( curl_errno( $ch ) ) {
		return curl_error( $ch );
	}

	curl_close( $ch );

	$response = json_decode( $responseData );

	return $response->token;
}

function fppg_create_order( $order_id, $bearer_token, $return_url, $mode ) {

	global $woocommerce;
	$customer_order = wc_get_order( $order_id );

	$data['amount']                = $customer_order->get_total();
	$data['transaction_reference'] = $order_id;
	$data['currency']              = get_woocommerce_currency();
	$data['mode']                  = $mode;
	$data['success_url']           = $return_url;
	$data['failure_url']           = $customer_order->get_cancel_order_url();

	// failure_url

	$url = 'https://portal.frontpay.pk/api/create-order';
	$ch  = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization:Bearer ' . $bearer_token ) );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true ); // this should be set to true in production
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$responseData = curl_exec( $ch );
	if ( curl_errno( $ch ) ) {
		return curl_error( $ch );
	}

	curl_close( $ch );
	$response = json_decode( $responseData );
	return $response;
}
