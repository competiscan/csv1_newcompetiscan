<?php
/**
 * Plugin Name: Competiscan – Security Headers
 * Description: Conservative HTTP security headers for WordPress pages (front end, wp-admin, login). Really Simple Security's header module is Pro-only, so this sends the safe recommended set. The legacy PHP app in the site root is not affected. Delete this file to remove the headers.
 * Version: 1.0.0
 *
 * Deliberately NOT sent (could break legitimate features): Content-Security-Policy
 * (Calendly popup, Google Fonts, inline scripts), Cross-Origin-* policies, and HSTS
 * includeSubDomains/preload (subdomains such as demo1 may not all be HTTPS).
 *
 * @package Competiscan
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send the headers once per request.
 */
function competiscan_send_security_headers() {
	static $sent = false;
	if ( $sent || headers_sent() ) {
		return;
	}
	$sent = true;

	// Don't advertise the PHP version (Really Simple Security's free "Unset X-Powered-By" option has no effect here).
	header_remove( 'X-Powered-By' );

	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );

	// HSTS only over HTTPS (live). No includeSubDomains / preload on purpose.
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000' );
	}
}
add_action( 'send_headers', 'competiscan_send_security_headers' ); // Front end.
add_action( 'admin_init', 'competiscan_send_security_headers' );   // wp-admin.
add_action( 'login_init', 'competiscan_send_security_headers' );   // Login screen (incl. WPS Hide Login slug).
