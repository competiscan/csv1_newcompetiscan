<?php
/**
 * Reusable contact form wiring.
 *
 * Uses the site's EXISTING Contact Form 7 forms (created by the team) — it does
 * not create new ones. The site-wide "Get In Touch" modal renders the existing
 * "Competiscan – Contact Us" form (slug: competiscan-contact-us). Any "Contact
 * Us" trigger opens it (see assets/js/contact.js).
 *
 * Contact Form 7 only — no custom PHP form handler, no other form plugin.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an existing CF7 form ID by slug (post_name), cached per request.
 *
 * @param string $slug CF7 form slug.
 * @return int Form ID, or 0 if not found.
 */
function competiscan_cf7_id_by_slug( $slug ) {
	static $cache = array();
	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}
	$id  = 0;
	$cf7 = get_posts(
		array(
			'post_type'      => 'wpcf7_contact_form',
			'name'           => $slug,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! empty( $cf7 ) ) {
		$id = (int) $cf7[0];
	}
	$cache[ $slug ] = $id;
	return $id;
}

/**
 * The form used by the site-wide contact modal.
 *
 * @return int
 */
function competiscan_contact_form_id() {
	return competiscan_cf7_id_by_slug( 'competiscan-contact-us' );
}

/**
 * The white-paper lead form used in the Market Intelligence Database page.
 *
 * @return int
 */
function competiscan_whitepaper_form_id() {
	return competiscan_cf7_id_by_slug( 'turning-credit-card-onboarding-into-continuous-growth' );
}

/**
 * Create the "Turning Credit Card Onboarding into Continuous Growth" CF7 form
 * once, matching the field structure / validation / mail of the existing
 * "Learn More" form. Guarded by an option so it is created a single time and is
 * not resurrected if the team later deletes it.
 */
function competiscan_create_whitepaper_form() {
	if ( get_option( 'competiscan_wp_form_created' ) ) {
		return;
	}
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return;
	}
	$existing = competiscan_whitepaper_form_id();
	if ( $existing ) {
		update_option( 'competiscan_wp_form_created', $existing );
		return;
	}

	$form_body =
		'<div class="form-row">' . "\n" .
		'    <div class="form-col">' . "\n" .
		'        [text* your-first-name placeholder "First Name"]' . "\n" .
		'    </div>' . "\n" .
		'    <div class="form-col">' . "\n" .
		'        [text* your-last-name placeholder "Last Name"]' . "\n" .
		'    </div>' . "\n" .
		'</div>' . "\n\n" .
		'[email* your-email placeholder "Work Email"]' . "\n" .
		'[text* your-company placeholder "Company Name"]' . "\n" .
		'[text your-role placeholder "Role"]' . "\n" .
		'[text your-industry placeholder "Industry"]' . "\n\n" .
		'[submit "Download the white paper"]';

	$form = WPCF7_ContactForm::get_template();
	$form->set_title( 'Turning Credit Card Onboarding into Continuous Growth' );

	$props                               = $form->get_properties();
	$props['form']                       = $form_body;
	$props['mail']['active']             = true;
	$props['mail']['subject']            = 'White Paper Request: [your-first-name] [your-last-name]';
	$props['mail']['sender']             = 'competiscan <preeti.mittal@nmgtechnologies.com>';
	$props['mail']['recipient']          = 'preeti.mittal@nmgtechnologies.com';
	$props['mail']['additional_headers'] = 'Reply-To: [your-email]';
	$props['mail']['body']               = "White paper requested: Turning Credit Card Onboarding into Continuous Growth\n\nFrom: [your-first-name] [your-last-name]\nEmail: [your-email]\nCompany: [your-company]\nRole: [your-role]\nIndustry: [your-industry]";

	$form->set_properties( $props );
	$id = $form->save();

	if ( $id && ! is_wp_error( $id ) ) {
		update_option( 'competiscan_wp_form_created', (int) $id );
	}
}
add_action( 'init', 'competiscan_create_whitepaper_form', 21 );

/**
 * Reconfigure the white-paper CF7 form to match the source HTML exactly:
 * three required fields (Full name / Business email / Company) with the same
 * labels, placeholders and field names. Email submission is preserved. Runs once.
 */
function competiscan_configure_whitepaper_form() {
	if ( get_option( 'competiscan_wp_form_v2' ) ) {
		return;
	}
	if ( ! function_exists( 'wpcf7_contact_form' ) ) {
		return;
	}
	$id = competiscan_whitepaper_form_id();
	if ( ! $id ) {
		return;
	}
	$form = wpcf7_contact_form( $id );
	if ( ! $form ) {
		return;
	}

	$body =
		'<label class="wp-l" for="wp-firstname">Full name</label>' . "\n" .
		'[text* firstname id:wp-firstname placeholder "Jane Smith"]' . "\n\n" .
		'<label class="wp-l" for="wp-email">Business email</label>' . "\n" .
		'[email* email id:wp-email placeholder "you@company.com"]' . "\n\n" .
		'<label class="wp-l" for="wp-company">Company</label>' . "\n" .
		'[text* company id:wp-company placeholder "Company name"]' . "\n\n" .
		'[submit "Download the white paper"]';

	$props                    = $form->get_properties();
	$props['form']            = $body;
	$props['mail']['subject'] = 'White Paper Request: [firstname]';
	$props['mail']['body']    = "White paper request\n\nName: [firstname]\nEmail: [email]\nCompany: [company]";
	$props['mail']['additional_headers'] = 'Reply-To: [email]';
	$form->set_properties( $props );
	$form->save();

	update_option( 'competiscan_wp_form_v2', '1' );
}
add_action( 'init', 'competiscan_configure_whitepaper_form', 22 );

/**
 * Output the shared contact modal (with the existing CF7 form) in the footer.
 */
function competiscan_render_contact_modal() {
	$id = competiscan_contact_form_id();
	if ( ! $id || ! function_exists( 'wpcf7_contact_form' ) ) {
		return;
	}
	?>
	<div class="cs-ctm-overlay" data-cs-contact-modal role="dialog" aria-modal="true" aria-label="Contact us">
		<div class="cs-ctm">
			<button type="button" class="cs-ctm-close" aria-label="Close" data-cs-contact-close>
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"></path></svg>
			</button>
			<h2 class="cs-ctm-title">Get In Touch</h2>
			<p class="cs-ctm-sub">Please feel free to contact us any time. We will get back to you as soon as possible.</p>
			<div class="cs-ctm-divider">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 8-10 6L2 8"></path></svg>
			</div>
			<?php echo do_shortcode( '[contact-form-7 id="' . $id . '"]' ); ?>
		</div>
	</div>
	<?php
}
// Priority 5 so the modal markup exists in the DOM before footer scripts run.
add_action( 'wp_footer', 'competiscan_render_contact_modal', 5 );
