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
