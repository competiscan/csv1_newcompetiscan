<?php
/**
 * Contact Form 7 spam protection (applies to every CF7 form on the site).
 *
 * 1. Email validation  – blocks disposable / temporary / reserved email domains
 *                        and domains that cannot receive mail (no MX / A record).
 *                        Runs on required AND optional email fields when a value
 *                        is entered. Gmail, Outlook, Yahoo, iCloud etc. are never
 *                        blocked.
 * 2. Honeypot          – an off-screen text field humans never see; a submission
 *                        that fills it is marked as spam.
 * 3. Google reCAPTCHA v3 – Contact Form 7's own built-in integration. Keys are
 *                        read from WPCF7_RECAPTCHA_SITEKEY / WPCF7_RECAPTCHA_SECRET
 *                        in wordpress/wp-config.php (git-ignored), so no keys are in
 *                        Git or the database. Without keys reCAPTCHA stays off and
 *                        the forms keep working. This file only adds an extra
 *                        "action" check on top of CF7's score check.
 * 4. Removes the old Really Simple CAPTCHA image code from the forms at load time
 *    (the stored forms in the database are not modified).
 *
 * Nothing in this file writes to the database.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. Email validation
 * ---------------------------------------------------------------------- */

/**
 * Disposable / temporary / reserved email domains. Sub-domains are matched too
 * (e.g. anything.mailinator.com). Extend with the
 * `competiscan_cf7_blocked_email_domains` filter.
 *
 * @return string[]
 */
function competiscan_cf7_blocked_email_domains() {
	$domains = array(
		// Reserved / test domains (RFC 2606) and domains the team flagged.
		'example.com', 'example.net', 'example.org', 'test.com', 'a2.com',

		// Mailinator and its alias domains.
		'mailinator.com', 'mailinator.net', 'mailinator2.com', 'mailinater.com',
		'binkmail.com', 'bobmail.info', 'chammy.info', 'devnullmail.com',
		'letthemeatspam.com', 'mailismagic.com', 'notmailinator.com',
		'reallymymail.com', 'reconmail.com', 'safetymail.info', 'sendspamhere.com',
		'sogetthis.com', 'spambooger.com', 'spamherelots.com', 'spamhereplease.com',
		'spamthisplease.com', 'streetwisemail.com', 'suremail.info',
		'thisisnotmyrealemail.com', 'tradermail.info', 'veryrealemail.com',
		'zippymail.info',

		// Guerrilla Mail family.
		'guerrillamail.com', 'guerrillamail.net', 'guerrillamail.org',
		'guerrillamail.biz', 'guerrillamail.de', 'guerrillamail.info',
		'guerrillamailblock.com', 'sharklasers.com', 'grr.la', 'pokemail.net',
		'spam4.me',

		// Temp-mail / 10-minute style services.
		'tempmail.com', 'tempmail.net', 'tempmail.plus', 'tempmailo.com',
		'temp-mail.org', 'temp-mail.io', 'tempail.com', 'tempr.email',
		'tempinbox.com', 'tempmailaddress.com', 'temporary-mail.net',
		'10minutemail.com', '10minutemail.net', '10minemail.com',
		'20minutemail.com', 'minuteinbox.com', 'mytemp.email', 'tmpmail.org',
		'tmpmail.net', 'emltmp.com', '1secmail.com', '1secmail.net',
		'1secmail.org',

		// Throwaway / trash inboxes.
		'yopmail.com', 'yopmail.fr', 'yopmail.net', 'throwawaymail.com',
		'trashmail.com', 'trashmail.de', 'trashmail.net', 'trashmail.me',
		'mytrashmail.com', 'mailmetrash.com', 'trbvm.com', 'wegwerfmail.de',
		'wegwerfmail.net', 'byom.de', 'jetable.org', 'mail-temporaire.fr',
		'getnada.com', 'dispostable.com', 'maildrop.cc', 'mailnesia.com',
		'mintemail.com', 'mohmal.com', 'mailcatch.com', 'fakeinbox.com',
		'fakemail.net', 'emailondeck.com', 'spamgourmet.com', 'moakt.com',
		'burnermail.io', 'discard.email', 'discardmail.com', 'spambox.us',
		'mailpoof.com', 'dropmail.me', 'inboxkitten.com', 'mail.tm',
		'mailsac.com', 'harakirimail.com', 'mailforspam.com', 'mailnull.com',
		'spamfree24.org', 'emailfake.com', 'luxusmail.org',

		// Fake Mail Generator domains.
		'armyspy.com', 'cuvox.de', 'dayrep.com', 'einrot.com', 'fleckens.hu',
		'gustr.com', 'jourrapide.com', 'rhyta.com', 'superrito.com',
		'teleworm.us',
	);

	$domains = apply_filters( 'competiscan_cf7_blocked_email_domains', $domains );

	return array_values( array_unique( array_map( 'strtolower', (array) $domains ) ) );
}

/**
 * Large mailbox providers that are always allowed and never DNS-checked.
 *
 * @return string[]
 */
function competiscan_cf7_trusted_email_domains() {
	return apply_filters(
		'competiscan_cf7_trusted_email_domains',
		array(
			'gmail.com', 'googlemail.com', 'outlook.com', 'hotmail.com',
			'hotmail.co.uk', 'live.com', 'msn.com', 'yahoo.com', 'yahoo.co.uk',
			'yahoo.co.in', 'ymail.com', 'rocketmail.com', 'icloud.com', 'me.com',
			'mac.com', 'aol.com', 'proton.me', 'protonmail.com', 'zoho.com',
			'gmx.com', 'gmx.net', 'gmx.de', 'web.de', 'mail.com', 'rediffmail.com',
		)
	);
}

/**
 * True if the domain (or a parent domain) is on the blocked list.
 *
 * @param string $domain Lower-case domain.
 * @return bool
 */
function competiscan_cf7_is_blocked_domain( $domain ) {
	// Reserved top-level domains that can never receive real mail (RFC 2606 / 6761).
	if ( preg_match( '/\.(test|example|invalid|localhost|local)$/', $domain ) ) {
		return true;
	}
	foreach ( competiscan_cf7_blocked_email_domains() as $blocked ) {
		if ( $domain === $blocked || substr( $domain, -strlen( '.' . $blocked ) ) === '.' . $blocked ) {
			return true;
		}
	}
	return false;
}

/**
 * True if the domain can receive mail (MX record, or A/AAAA fallback as per
 * RFC 5321). Fails OPEN: if DNS itself is unavailable, the address is allowed
 * so real visitors are never blocked by a network problem.
 *
 * @param string $domain Lower-case domain.
 * @return bool
 */
function competiscan_cf7_domain_accepts_mail( $domain ) {
	static $cache = array();

	if ( isset( $cache[ $domain ] ) ) {
		return $cache[ $domain ];
	}
	if ( ! apply_filters( 'competiscan_cf7_check_email_dns', true, $domain ) || ! function_exists( 'checkdnsrr' ) ) {
		return $cache[ $domain ] = true;
	}

	// null = DNS could not be checked → do not block anyone.
	$ok = competiscan_cf7_dns_mail_check( $domain );

	return $cache[ $domain ] = ( false !== $ok );
}

/**
 * Name servers of domain-parking / for-sale services. A domain parked there
 * with no real mail server cannot receive email. Extend with the
 * `competiscan_cf7_parking_nameservers` filter.
 *
 * @return string[]
 */
function competiscan_cf7_parking_nameservers() {
	return apply_filters(
		'competiscan_cf7_parking_nameservers',
		array( 'afternic.com', 'sedoparking.com', 'bodis.com', 'parkingcrew.net', 'above.com', 'dan.com', 'undeveloped.com', 'hugedomains.com' )
	);
}

/**
 * Public DNS lookup (DNS-over-HTTPS: Google, then Cloudflare). Used instead of
 * the server's own resolver so results are the same on localhost (office DNS
 * hides some public records, e.g. nmgtechnologies.com MX) and on live.
 *
 * @param string $name Host name.
 * @param string $type MX, A, AAAA or NS.
 * @return array|null  Array of answer data strings ([] = none / domain does
 *                     not exist), or null when no DNS service could be reached.
 */
function competiscan_dns_query( $name, $type ) {
	static $cache = array();
	$key = strtolower( $name ) . '|' . $type;
	if ( array_key_exists( $key, $cache ) ) {
		return $cache[ $key ];
	}
	$codes     = array( 'A' => 1, 'NS' => 2, 'MX' => 15, 'AAAA' => 28 );
	$endpoints = array(
		'https://dns.google/resolve',
		'https://cloudflare-dns.com/dns-query',
	);
	foreach ( $endpoints as $endpoint ) {
		$response = wp_remote_get(
			add_query_arg( array( 'name' => rawurlencode( $name ), 'type' => $type ), $endpoint ),
			array(
				'timeout' => 4,
				'headers' => array( 'Accept' => 'application/dns-json' ),
			)
		);
		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			continue;
		}
		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || ! isset( $body['Status'] ) ) {
			continue;
		}
		$status = (int) $body['Status'];
		if ( 0 !== $status && 3 !== $status ) {
			continue; // SERVFAIL etc. – try the next service.
		}
		$answers = array();
		foreach ( (array) ( $body['Answer'] ?? array() ) as $answer ) {
			if ( isset( $answer['type'], $answer['data'] ) && (int) $answer['type'] === $codes[ $type ] ) {
				$answers[] = (string) $answer['data'];
			}
		}
		return $cache[ $key ] = $answers; // NXDOMAIN (3) → [].
	}
	return $cache[ $key ] = null;
}

/**
 * DNS test used by competiscan_cf7_domain_accepts_mail(). A domain can receive
 * email only when ALL of these hold:
 * - it exists;
 * - it is not parked at a domain-parking / for-sale service;
 * - it has at least one real MX (mail server) record – "Null MX" (".", RFC 7505)
 *   and "localhost" do not count, and website-only domains with no MX are
 *   rejected;
 * - at least one of those mail servers has an address (A/AAAA).
 *
 * @param string $domain Lower-case domain.
 * @return bool|null True = can receive mail, false = cannot, null = DNS unavailable.
 */
function competiscan_cf7_dns_mail_check( $domain ) {
	$mx = competiscan_dns_query( $domain, 'MX' );

	if ( null === $mx ) {
		// Public DNS unreachable – fall back to the server's resolver (MX only).
		if ( ! function_exists( 'checkdnsrr' ) || ! checkdnsrr( 'gmail.com', 'MX' ) ) {
			return null;
		}
		return checkdnsrr( $domain, 'MX' );
	}

	// Parked / for-sale domains never have a real mailbox.
	$ns = competiscan_dns_query( $domain, 'NS' );
	foreach ( (array) $ns as $host ) {
		$host = strtolower( rtrim( $host, '.' ) );
		foreach ( competiscan_cf7_parking_nameservers() as $parking ) {
			if ( $host === $parking || substr( $host, -strlen( '.' . $parking ) ) === '.' . $parking ) {
				return false;
			}
		}
	}

	// MX data looks like "10 mx1.example.com."
	$hosts = array();
	foreach ( $mx as $record ) {
		$parts  = preg_split( '/\s+/', trim( $record ) );
		$target = strtolower( trim( (string) end( $parts ), ". \t" ) );
		if ( '' === $target || 'localhost' === $target || '.localhost' === substr( $target, -10 ) ) {
			continue;
		}
		$hosts[] = $target;
	}
	if ( ! $hosts ) {
		return false; // No MX, Null MX only, or fake targets.
	}

	foreach ( array_slice( $hosts, 0, 3 ) as $host ) {
		$a = competiscan_dns_query( $host, 'A' );
		if ( null === $a || $a ) {
			return true; // Resolves (or could not be checked – allow).
		}
		$aaaa = competiscan_dns_query( $host, 'AAAA' );
		if ( null === $aaaa || $aaaa ) {
			return true;
		}
	}

	return false; // Mail servers listed but none of them exists.
}

/**
 * Validate CF7 email fields (required and optional).
 *
 * CF7 itself (priority 10) already handles "required" and "invalid format";
 * this only runs when the field has a value that CF7 accepted.
 *
 * @param WPCF7_Validation $result Validation result.
 * @param WPCF7_FormTag    $tag    Form tag.
 * @return WPCF7_Validation
 */
function competiscan_cf7_validate_email_domain( $result, $tag ) {
	$name = $tag->name;

	if ( '' === $name || ! isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- CF7 handles the request.
		return $result;
	}
	if ( method_exists( $result, 'is_valid' ) && ! $result->is_valid( $name ) ) {
		return $result; // CF7 already reported an error for this field.
	}

	$value = trim( (string) wp_unslash( $_POST[ $name ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( '' === $value || ! wpcf7_is_email( $value ) ) {
		return $result;
	}

	$error = competiscan_email_domain_error( $value );
	if ( '' !== $error ) {
		$result->invalidate( $tag, $error );
	}

	return $result;
}

/**
 * Shared email-domain rule used by every form on the site (Contact Form 7 and
 * the Noptin newsletter form): returns an error message, or '' when the
 * address may be accepted.
 *
 * @param string $email Email address (already format-checked by the form).
 * @return string
 */
function competiscan_email_domain_error( $email ) {
	$email  = sanitize_email( (string) $email );
	$domain = strtolower( rtrim( (string) substr( (string) strrchr( $email, '@' ), 1 ), '.' ) );

	if ( '' === $domain ) {
		return '';
	}
	if ( ! in_array( $domain, competiscan_cf7_trusted_email_domains(), true ) ) {
		if ( competiscan_cf7_is_blocked_domain( $domain ) ) {
			return __( 'Please use a permanent email address. Temporary or disposable email addresses are not accepted.', 'competiscan-custom' );
		}
		if ( ! competiscan_cf7_domain_accepts_mail( $domain ) ) {
			return __( 'This email domain cannot receive email. Please check the address and try again.', 'competiscan-custom' );
		}
	}

	// Domain is fine – now check that the mailbox itself exists (Gmail etc. included).
	return competiscan_email_mailbox_error( $email );
}

/**
 * Mailbox check with ZeroBounce (https://www.zerobounce.net/docs/email-validation-api-quickstart/).
 *
 * Only active when CS_ZEROBOUNCE_API_KEY is defined in wordpress/wp-config.php
 * (git-ignored). Fails OPEN: if the API is unreachable, slow, out of credits or
 * answers "unknown" / "catch-all", the address is accepted so real leads are
 * never lost. One API credit per checked address; results are cached for the
 * current request only (no database writes).
 *
 * Blocked: status "invalid" (mailbox does not exist), "spamtrap", and
 * "do_not_mail" with sub-status "disposable" or "toxic". Role addresses such as
 * info@ are NOT blocked.
 *
 * @param string $email Sanitized email address.
 * @return string Error message, or ''.
 */
function competiscan_email_mailbox_error( $email ) {
	static $cache = array();

	if ( ! defined( 'CS_ZEROBOUNCE_API_KEY' ) || '' === (string) CS_ZEROBOUNCE_API_KEY || '' === $email ) {
		return '';
	}
	$key = strtolower( $email );
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$response = wp_remote_get(
		add_query_arg(
			array(
				'api_key'    => rawurlencode( CS_ZEROBOUNCE_API_KEY ),
				'email'      => rawurlencode( $email ),
				'ip_address' => '',
			),
			'https://api.zerobounce.net/v2/validate'
		),
		array( 'timeout' => 8 )
	);

	$error = '';
	if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
		$body   = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		$status = is_array( $body ) && isset( $body['status'] ) ? strtolower( (string) $body['status'] ) : '';
		$sub    = is_array( $body ) && isset( $body['sub_status'] ) ? strtolower( (string) $body['sub_status'] ) : '';

		if ( 'invalid' === $status || 'spamtrap' === $status ) {
			$error = __( 'This email address does not exist. Please check it and try again.', 'competiscan-custom' );
		} elseif ( 'do_not_mail' === $status && in_array( $sub, array( 'disposable', 'toxic' ), true ) ) {
			$error = __( 'Please use a permanent email address. Temporary or disposable email addresses are not accepted.', 'competiscan-custom' );
		}
	}

	return $cache[ $key ] = $error;
}

/**
 * Noptin newsletter form (Insights page): same email-domain rule.
 * Only for sign-ups – unsubscribe requests are left alone.
 *
 * @param object $listener Noptin\Forms\Listener.
 */
function competiscan_noptin_email_domain_check( $listener ) {
	if ( ! is_object( $listener ) || ! method_exists( $listener, 'get_field_value' ) || empty( $listener->error ) ) {
		return;
	}
	if ( method_exists( $listener, 'get_submitted' ) && 'unsubscribe' === $listener->get_submitted( 'form_action', 'subscribe' ) ) {
		return;
	}

	$error = competiscan_email_domain_error( $listener->get_field_value( 'email' ) );
	if ( '' !== $error ) {
		$listener->error->add( 'cs_email_domain', $error, array( 'selector' => 'input.noptin-form-field__email' ) );
	}
}
add_action( 'noptin_form_errors', 'competiscan_noptin_email_domain_check', 10, 1 );
add_filter( 'wpcf7_validate_email', 'competiscan_cf7_validate_email_domain', 20, 2 );
add_filter( 'wpcf7_validate_email*', 'competiscan_cf7_validate_email_domain', 20, 2 );

/* -------------------------------------------------------------------------
 * 2. Honeypot
 * ---------------------------------------------------------------------- */

/**
 * Honeypot field name. Looks like a normal "website" field to bots.
 *
 * @return string
 */
function competiscan_cf7_honeypot_name() {
	return 'cs-website';
}

/**
 * Add the off-screen honeypot field to every CF7 form (not visible, not
 * focusable, ignored by screen readers and password managers).
 *
 * @param string $content Rendered form elements.
 * @return string
 */
function competiscan_cf7_add_honeypot( $content ) {
	$name = competiscan_cf7_honeypot_name();

	$field = sprintf(
		'<div class="cs-hp" aria-hidden="true" style="position:absolute !important;left:-10000px !important;top:auto !important;width:1px !important;height:1px !important;overflow:hidden !important;">' .
		'<label for="%1$s-%2$s">Leave this field empty</label>' .
		'<input type="text" id="%1$s-%2$s" name="%1$s" value="" tabindex="-1" autocomplete="off" data-1p-ignore data-lpignore="true" />' .
		'</div>',
		esc_attr( $name ),
		esc_attr( wp_unique_id() )
	);

	return $field . $content;
}
add_filter( 'wpcf7_form_elements', 'competiscan_cf7_add_honeypot', 20, 1 );

/**
 * Reject a submission whose honeypot field was filled in. Runs before
 * reCAPTCHA (priority 9) so obvious bots don't cost a Google check.
 *
 * @param bool             $spam       Spam status so far.
 * @param WPCF7_Submission $submission Submission.
 * @return bool
 */
function competiscan_cf7_honeypot_check( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	$name  = competiscan_cf7_honeypot_name();
	$value = isset( $_POST[ $name ] ) ? trim( (string) wp_unslash( $_POST[ $name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( '' !== $value ) {
		if ( is_object( $submission ) && method_exists( $submission, 'add_spam_log' ) ) {
			$submission->add_spam_log(
				array(
					'agent'  => 'honeypot',
					'reason' => 'Honeypot field was filled in.',
				)
			);
		}
		return true;
	}

	return $spam;
}
add_filter( 'wpcf7_spam', 'competiscan_cf7_honeypot_check', 8, 2 );

/* -------------------------------------------------------------------------
 * 3. reCAPTCHA v3 (CF7 built-in) – extra action check
 * ---------------------------------------------------------------------- */

/**
 * CF7 only checks the score. Also require Google's "success" flag and the
 * "contactform" action that CF7's script uses on submit, so a token generated
 * for another action (e.g. the page-load "homepage" token) is not accepted.
 *
 * @param bool  $is_human      Result of CF7's score check.
 * @param array $response_body Google siteverify response.
 * @return bool
 */
function competiscan_cf7_recaptcha_strict( $is_human, $response_body ) {
	if ( ! $is_human || ! is_array( $response_body ) ) {
		return $is_human;
	}
	if ( empty( $response_body['success'] ) ) {
		return false;
	}
	if ( isset( $response_body['action'] ) && 'contactform' !== $response_body['action'] ) {
		return false;
	}
	return $is_human;
}
add_filter( 'wpcf7_recaptcha_verify_response', 'competiscan_cf7_recaptcha_strict', 10, 2 );

/* -------------------------------------------------------------------------
 * 4. Remove the old Really Simple CAPTCHA image code
 * ---------------------------------------------------------------------- */

/**
 * Strip [captchac] / [captchar] tags (and their .cs-captcha-row wrapper) from
 * every form at load time. The forms stored in the database are not changed;
 * if someone re-saves a form in WP Admin it is simply saved without them.
 *
 * @param array $properties CF7 form properties.
 * @return array
 */
function competiscan_cf7_remove_image_captcha( $properties ) {
	if ( empty( $properties['form'] ) || false === strpos( $properties['form'], '[captcha' ) ) {
		return $properties;
	}

	$form = preg_replace( '#<div class="cs-captcha-row">\s*(?:\[captcha[cr][^\]]*\]\s*)+</div>\s*#', '', $properties['form'] );
	$form = preg_replace( '#\[captcha[cr]\s[^\]]*\]\s*#', '', $form );

	if ( is_string( $form ) ) {
		$properties['form'] = $form;
	}

	return $properties;
}
add_filter( 'wpcf7_contact_form_properties', 'competiscan_cf7_remove_image_captcha', 10, 1 );

/* -------------------------------------------------------------------------
 * 5. reCAPTCHA notice inside the forms (instead of the floating badge)
 * ---------------------------------------------------------------------- */

/**
 * True when CF7's reCAPTCHA v3 integration has keys and is running.
 *
 * @return bool
 */
function competiscan_cf7_recaptcha_active() {
	return class_exists( 'WPCF7_RECAPTCHA' ) && WPCF7_RECAPTCHA::get_instance()->is_active();
}

/**
 * Show Google's required reCAPTCHA notice under the Submit button of every
 * CF7 form. Google allows hiding the floating badge when this text is shown
 * in the user flow: https://developers.google.com/recaptcha/docs/faq
 *
 * @param string $content Rendered form elements.
 * @return string
 */
function competiscan_cf7_recaptcha_notice( $content ) {
	if ( ! competiscan_cf7_recaptcha_active() ) {
		return $content;
	}

	return $content .
		'<p class="cs-recaptcha-notice">This site is protected by reCAPTCHA and the Google ' .
		'<a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a> and ' .
		'<a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">Terms of Service</a> apply.</p>';
}
add_filter( 'wpcf7_form_elements', 'competiscan_cf7_recaptcha_notice', 30, 1 );

/**
 * Notice styling + keep Google's floating badge above the contact modal
 * (only while reCAPTCHA is active).
 */
function competiscan_cf7_recaptcha_notice_css() {
	if ( ! competiscan_cf7_recaptcha_active() ) {
		return;
	}
	?>
	<style id="cs-recaptcha-notice-css">
		/* Badge stays visible (bottom-right) and above the contact modal (z-index 9999). */
		.grecaptcha-badge { z-index: 10000 !important; }
		.wpcf7-form .cs-recaptcha-notice { margin: 12px 0 0; font-size: 12px; line-height: 1.5; color: rgb(110,122,132); text-align: center; }
		.wpcf7-form .cs-recaptcha-notice a { color: inherit; text-decoration: underline; }
		.wpcf7-form .cs-recaptcha-notice a:hover { color: rgb(0,171,171); }
	</style>
	<?php
}
add_action( 'wp_head', 'competiscan_cf7_recaptcha_notice_css', 20 );

/* -------------------------------------------------------------------------
 * 6. Google reCAPTCHA v2 "I'm not a robot" checkbox (before the Submit button)
 *
 * Contact Form 7 dropped v2 in version 5.1, so it is added here as a custom
 * form tag. Keys come from wordpress/wp-config.php (git-ignored):
 *   define( 'CS_RECAPTCHA_V2_SITEKEY', '...' );
 *   define( 'CS_RECAPTCHA_V2_SECRET',  '...' );
 * Without both keys nothing is added and the forms work as before.
 * While v2 is on, CF7's own reCAPTCHA v3 is switched off so the two Google
 * scripts never load together.
 * ---------------------------------------------------------------------- */

/**
 * True when v2 keys are configured.
 *
 * @return bool
 */
function competiscan_recaptcha_v2_active() {
	return defined( 'CS_RECAPTCHA_V2_SITEKEY' ) && defined( 'CS_RECAPTCHA_V2_SECRET' )
		&& '' !== (string) CS_RECAPTCHA_V2_SITEKEY && '' !== (string) CS_RECAPTCHA_V2_SECRET;
}

if ( competiscan_recaptcha_v2_active() ) {
	// Turn CF7's built-in v3 off (even if its keys are still defined).
	add_filter( 'wpcf7_recaptcha_sitekey', '__return_empty_string', 99 );
	add_filter( 'wpcf7_recaptcha_secret', '__return_empty_string', 99 );
}

/**
 * Register the [cs_recaptcha] form tag.
 */
function competiscan_recaptcha_v2_register_tag() {
	if ( ! function_exists( 'wpcf7_add_form_tag' ) ) {
		return;
	}
	wpcf7_add_form_tag(
		'cs_recaptcha',
		'competiscan_recaptcha_v2_tag_html',
		array(
			'name-attr'     => true,
			'display-block' => true,
		)
	);
}
add_action( 'wpcf7_init', 'competiscan_recaptcha_v2_register_tag', 20, 0 );

/**
 * Tag HTML: CF7's standard wrapper (so its field error appears under the box)
 * around the widget container. Rendered by the script in section 6 below.
 *
 * @param WPCF7_FormTag $tag Form tag.
 * @return string
 */
function competiscan_recaptcha_v2_tag_html( $tag ) {
	if ( ! competiscan_recaptcha_v2_active() || empty( $tag->name ) ) {
		return '';
	}
	return sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><span class="cs-g-recaptcha" data-sitekey="%2$s"></span></span>',
		esc_attr( $tag->name ),
		esc_attr( CS_RECAPTCHA_V2_SITEKEY )
	);
}

/**
 * Put the checkbox just above the Submit button of every CF7 form, at load
 * time (stored forms are not changed). Forms with a <div class="submit-btn">
 * wrapper get it before that wrapper, others before [submit].
 *
 * @param array $properties CF7 form properties.
 * @return array
 */
function competiscan_recaptcha_v2_add_to_forms( $properties ) {
	if ( ! competiscan_recaptcha_v2_active() || empty( $properties['form'] ) ) {
		return $properties;
	}
	$form = $properties['form'];
	if ( false !== strpos( $form, '[cs_recaptcha' ) || false === strpos( $form, '[submit' ) ) {
		return $properties;
	}

	$box = '<div class="cs-recaptcha-row">[cs_recaptcha cs-recaptcha]</div>' . "\n";

	if ( preg_match( '#<div class="submit-btn">#', $form ) ) {
		$form = preg_replace( '#<div class="submit-btn">#', $box . '$0', $form, 1 );
	} else {
		$form = preg_replace( '#\[submit#', $box . '$0', $form, 1 );
	}

	$properties['form'] = $form;
	return $properties;
}
add_filter( 'wpcf7_contact_form_properties', 'competiscan_recaptcha_v2_add_to_forms', 20, 1 );

/**
 * Server-side check: the box must be ticked and Google must confirm it.
 *
 * @param WPCF7_Validation $result Validation result.
 * @param WPCF7_FormTag    $tag    Form tag.
 * @return WPCF7_Validation
 */
function competiscan_recaptcha_v2_validate( $result, $tag ) {
	if ( ! competiscan_recaptcha_v2_active() ) {
		return $result;
	}

	$token = isset( $_POST['g-recaptcha-response'] ) ? trim( (string) wp_unslash( $_POST['g-recaptcha-response'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	if ( '' === $token ) {
		$result->invalidate( $tag, __( 'Please tick "I\'m not a robot" before submitting.', 'competiscan-custom' ) );
		return $result;
	}

	$response = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => CS_RECAPTCHA_V2_SECRET,
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);
	$body = is_wp_error( $response ) ? array() : json_decode( (string) wp_remote_retrieve_body( $response ), true );

	if ( WPCF7_Submission::get_instance() ) {
		WPCF7_Submission::get_instance()->push( 'recaptcha', array( 'version' => '2.0', 'response' => $body ) );
	}

	if ( empty( $body['success'] ) ) {
		$result->invalidate( $tag, __( 'reCAPTCHA verification failed. Please tick "I\'m not a robot" again.', 'competiscan-custom' ) );
	}

	return $result;
}
add_filter( 'wpcf7_validate_cs_recaptcha', 'competiscan_recaptcha_v2_validate', 10, 2 );

/**
 * Load Google's v2 script (explicit render, so every form on the page —
 * including the hidden contact modal — gets its own widget that is reset
 * after each submit).
 */
function competiscan_recaptcha_v2_scripts() {
	if ( ! competiscan_recaptcha_v2_active() ) {
		return;
	}
	wp_register_script( 'cs-recaptcha-v2-init', false, array(), '1.0', true );
	wp_enqueue_script( 'cs-recaptcha-v2-init' );
	wp_add_inline_script(
		'cs-recaptcha-v2-init',
		'window.csRecaptchaV2Init=function(){document.querySelectorAll(".cs-g-recaptcha:not([data-widget-id])").forEach(function(el){el.setAttribute("data-widget-id",grecaptcha.render(el,{sitekey:el.getAttribute("data-sitekey")}));});};' .
		'document.addEventListener("wpcf7submit",function(e){var el=e.target.querySelector(".cs-g-recaptcha[data-widget-id]");if(el&&window.grecaptcha){grecaptcha.reset(parseInt(el.getAttribute("data-widget-id"),10));}});'
	);
	wp_enqueue_script(
		'google-recaptcha-v2',
		'https://www.google.com/recaptcha/api.js?onload=csRecaptchaV2Init&render=explicit',
		array( 'cs-recaptcha-v2-init' ),
		null,
		array(
			'in_footer' => true,
			'strategy'  => 'async',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'competiscan_recaptcha_v2_scripts', 30 );

/**
 * Spacing for the checkbox row (fits the 390px mobile modal; scaled down on
 * very narrow screens).
 */
function competiscan_recaptcha_v2_css() {
	if ( ! competiscan_recaptcha_v2_active() ) {
		return;
	}
	?>
	<style id="cs-recaptcha-v2-css">
		.wpcf7-form .cs-recaptcha-row { margin: 0 0 16px; }
		.wpcf7-form .cs-recaptcha-row .cs-g-recaptcha { display: block; min-height: 78px; }
		.wpcf7-form .cs-recaptcha-row .wpcf7-not-valid-tip { margin-top: 6px; }
		@media (max-width: 359px) {
			.wpcf7-form .cs-recaptcha-row .cs-g-recaptcha { transform: scale(0.88); transform-origin: 0 0; }
		}
		
	</style>
	<?php
}
add_action( 'wp_head', 'competiscan_recaptcha_v2_css', 21 );
