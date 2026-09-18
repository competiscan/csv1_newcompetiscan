<?php
/**
 * TEMPORARY session diagnostic — safe to run, delete when done.
 *
 * WHAT IT DOES: shows whether WordPress can see the SAME PHP session that the
 * demo login (login.php) creates, and which key holds the logged-in user. It
 * prints only session KEYS and a few settings — never the values — so no
 * personal data is exposed.
 *
 * HOW TO USE ON demo1.competiscan.com:
 *   1. Upload this file to the site ROOT (next to login.php):
 *        https://demo1.competiscan.com/session-check.php
 *   2. In the same browser, first log in at https://demo1.competiscan.com/login.php
 *   3. Then open https://demo1.competiscan.com/session-check.php
 *   4. Send me what it prints.
 *   5. DELETE this file afterwards.
 */

if ( session_status() !== PHP_SESSION_ACTIVE ) {
	session_start();
}

header( 'Content-Type: text/plain; charset=utf-8' );

echo "session.name       : " . session_name() . "\n";
echo "session id present : " . ( session_id() ? 'yes' : 'no' ) . "\n";
echo "session.save_path  : " . ini_get( 'session.save_path' ) . "\n";
echo "cookie path        : " . ( session_get_cookie_params()['path'] ?? '' ) . "\n";
echo "\n";

if ( empty( $_SESSION ) ) {
	echo "\$_SESSION is EMPTY — WordPress/this file is NOT seeing the login session.\n";
	echo "(Log in first in the same browser, then reload this page.)\n";
} else {
	echo "\$_SESSION keys currently set:\n";
	foreach ( array_keys( $_SESSION ) as $k ) {
		$v = $_SESSION[ $k ];
		$type = is_scalar( $v ) ? ( is_int( $v ) || ctype_digit( (string) $v ) ? 'number' : 'text' ) : gettype( $v );
		echo "   - " . $k . "  (" . $type . ")\n";
	}
	echo "\n";
	echo "user_id set?  : " . ( ! empty( $_SESSION['user_id'] ) ? 'YES' : 'no' ) . "\n";
}
