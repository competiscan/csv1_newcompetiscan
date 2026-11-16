<?php
/**
 * LOCAL DEVELOPMENT ONLY – do not commit or deploy.
 *
 * On localhost this:
 *  1. Catches every outgoing WordPress email (incl. Contact Form 7) and saves it
 *     as a .eml file in wp-content/uploads/cs-local-mail/ instead of sending it.
 *     No real email ever leaves the machine; CF7 then shows its normal
 *     "Thank you for your message" result.
 *  2. Logs every CF7 submission result (status, spam reasons, reCAPTCHA score)
 *     to wp-content/uploads/cs-local-mail/cf7-submissions.log – no form values.
 *
 * Does nothing on any other host (e.g. competiscan.com).
 * The folder is git-ignored (uploads) and blocked from the browser.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cs_lmc_host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( (string) $_SERVER['HTTP_HOST'] ) : '';
$cs_lmc_host = preg_replace( '/:\d+$/', '', $cs_lmc_host );
if ( ! in_array( $cs_lmc_host, array( 'localhost', '127.0.0.1', '::1', '[::1]' ), true ) ) {
	return;
}

/**
 * Folder for caught mail + log (created on first use, browser access denied).
 *
 * @return string Absolute path with trailing slash.
 */
function cs_lmc_dir() {
	$dir = WP_CONTENT_DIR . '/uploads/cs-local-mail/';
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
		file_put_contents( $dir . '.htaccess', "Require all denied\n" );
		file_put_contents( $dir . 'index.php', "<?php // Silence.\n" );
	}
	return $dir;
}

// 1. Catch all mail.
add_filter(
	'pre_wp_mail',
	function ( $return, $atts ) {
		$to      = is_array( $atts['to'] ) ? implode( ', ', $atts['to'] ) : (string) $atts['to'];
		$headers = is_array( $atts['headers'] ) ? implode( "\r\n", $atts['headers'] ) : (string) $atts['headers'];
		$body    = "X-Caught-By: local mail catcher (not sent)\r\nDate: " . gmdate( 'r' ) . "\r\nTo: {$to}\r\nSubject: {$atts['subject']}\r\n" . trim( $headers ) . "\r\n\r\n" . $atts['message'];
		$file    = cs_lmc_dir() . gmdate( 'Ymd-His' ) . '-' . substr( md5( uniqid( '', true ) ), 0, 6 ) . '.eml';
		file_put_contents( $file, $body );
		return true; // Tell WordPress the mail was "sent".
	},
	10,
	2
);

// 2. Log CF7 results.
add_action(
	'wpcf7_submit',
	function ( $contact_form, $result ) {
		$line = array(
			'time'   => current_time( 'mysql' ),
			'form'   => is_object( $contact_form ) ? $contact_form->id() . ' ' . $contact_form->title() : '',
			'status' => isset( $result['status'] ) ? $result['status'] : '',
		);
		$sub = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;
		if ( $sub ) {
			$spam = $sub->get_spam_log();
			if ( $spam ) {
				$line['spam_reasons'] = wp_list_pluck( $spam, 'reason', 'agent' );
			}
			$invalid = $sub->get_invalid_fields();
			if ( $invalid ) {
				$line['invalid_fields'] = wp_list_pluck( $invalid, 'reason' );
			}
			$rc = $sub->pull( 'recaptcha' );
			if ( $rc && isset( $rc['response'] ) ) {
				$r                 = $rc['response'];
				$line['recaptcha'] = array(
					'success'  => $r['success'] ?? null,
					'score'    => $r['score'] ?? null,
					'action'   => $r['action'] ?? null,
					'hostname' => $r['hostname'] ?? null,
					'errors'   => $r['error-codes'] ?? null,
				);
			}
		}
		$line['honeypot_filled'] = ! empty( $_POST['cs-website'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$line['recaptcha_token'] = empty( $_POST['_wpcf7_recaptcha_response'] ) ? 'missing' : 'present'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		file_put_contents( cs_lmc_dir() . 'cf7-submissions.log', wp_json_encode( $line ) . "\n", FILE_APPEND );
	},
	99,
	2
);
