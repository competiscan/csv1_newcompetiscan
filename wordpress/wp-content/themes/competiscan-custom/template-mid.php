<?php
/**
 * Template Name: Solution — Market Intelligence Database
 * Template Post Type: page
 *
 * 1:1 replica of competiscan-html/Market Intelligence Database.dc.html.
 * Header and footer are the theme's own and reused unchanged. Every section is
 * ACF-editable with the original copy as fallback.
 *
 * @package Competiscan_Custom
 */

get_header();

// Shared "ACF value or default" helper (guarded so multiple page templates can define it).
if ( ! function_exists( 'competiscan_pg_f' ) ) {
	function competiscan_pg_f( $name, $default = '' ) {
		$v = get_field( $name );
		return ( '' === $v || null === $v || false === $v ) ? $default : $v;
	}
}

$logomark = get_template_directory_uri() . '/assets/images/logomark-white.png';

// Capability icons (kept in template; repeater overrides title/text by index).
$cap_icons = array(
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m7 15 4-4 3 3 5-6"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path><path d="M9 14l2 2 4-4"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14a9 3 0 0 0 18 0V5"></path><path d="M3 12a9 3 0 0 0 18 0"></path></svg>',
);

$caps = get_field( 'mid_caps' );
if ( empty( $caps ) ) {
	$caps = array(
		array( 'title' => 'Competitive & Market Insights', 'text' => 'Track estimated marketing volumes, audience targeting, promotional activity, product positioning, customer journeys, and creative trends to benchmark your campaigns and stay ahead.' ),
		array( 'title' => 'Expert Analysis & Support', 'text' => 'Leverage quarterly trend reports and on-demand insights support to uncover strategic insights and make data-driven marketing decisions.' ),
		array( 'title' => '24/7 Database Access', 'text' => 'Gain access to a platform of direct mail, email, and digital marketing materials, all in one streamlined platform that is updated daily with the latest communications.' ),
	);
}

$chan = get_field( 'mid_channels' );
$channels = ! empty( $chan ) ? wp_list_pluck( $chan, 'label' ) : array( 'Direct Mail', 'Email', 'Digital', 'Social Media', 'Print' );
$ind = get_field( 'mid_industries' );
$industries = ! empty( $ind ) ? wp_list_pluck( $ind, 'label' ) : array( 'Automotive', 'Banking', 'Payment Cards', 'Energy', 'Insurance', 'Investments', 'Mortgage & Loans', 'Non-Profit', 'Consumer Services', 'Retail', 'Travel & Leisure', 'Telecom' );
$aud = get_field( 'mid_audiences' );
$audiences = ! empty( $aud ) ? wp_list_pluck( $aud, 'label' ) : array( 'Consumers', 'Business Owners', 'Insurance Producers', 'Financial Advisors', 'Mortgage Brokers', 'Healthcare Providers' );

$faqs = get_field( 'mid_faq' );
if ( empty( $faqs ) ) {
	$faqs = array(
		array( 'question' => 'Who is Competiscan?', 'answer' => 'Competiscan is a leading-edge market intelligence company, providing clients with best-in-class service.' ),
		array( 'question' => 'What services does Competiscan provide?', 'answer' => "Competiscan delivers market and competitive intelligence that helps organizations understand how competitors engage with different audiences across channels. Our capabilities include value proposition tracking, creative trends, customer journeys and experiences, behind-the-login insights, marketing strategies, targeting insights, AI-driven analysis of marketing campaigns, and more. With access to the largest consumer panels in the market, we deliver real-time insight into competitors' activities." ),
		array( 'question' => 'What channels does Competiscan monitor?', 'answer' => 'We monitor a variety of media channels for a comprehensive view of how competitors communicate with customers and prospects, including direct mail, email, digital, social media, and print.' ),
		array( 'question' => 'What industries does Competiscan cover?', 'answer' => 'Competiscan covers key sectors, including Automotive, Banking, Payment Cards, Energy, Insurance, Investments, Mortgage & Loan, Retail, Travel & Leisure, and Telecom.' ),
		array( 'question' => 'What audiences does Competiscan cover?', 'answer' => 'Our panel includes a diverse range of audiences: Consumers, Business Owners, Insurance Producers, Financial Advisors, Mortgage Brokers, and Healthcare Providers.' ),
		array( 'question' => 'What parts of the customer journey does Competiscan capture?', 'answer' => 'We provide a 360-degree view of customer-journey communications, including acquisition, follow-up, loyalty, retention, win-back, statements, and upgrade & cross-sell.' ),
	);
}


$wp_pdf = competiscan_pg_f(
    'mid_wp_pdf',
    get_template_directory_uri() . '/assets/images/competiscan-whitepaper-credit-card-onboarding.pdf'
);
?>

<div class="cs-x1">

  <!-- HERO -->
  <section class="cs-x15" id="top">
    <div class="cs-x16">
      <span class="cs-x113"><?php echo esc_html( competiscan_pg_f( 'mid_hero_eyebrow', 'Solution 01' ) ); ?></span>
      <h1 class="cs-x19"><?php echo esc_html( competiscan_pg_f( 'mid_hero_title', 'Market Intelligence Database' ) ); ?></h1>
      <p class="cs-x20"><?php echo esc_html( competiscan_pg_f( 'mid_hero_text', 'Your source for the direct and digital marketing campaigns your competitors launch, powered by the largest longitudinal and omni-channel tracking panels in the marketplace. Get 24/7 on-demand access to over 200M omnichannel communications and unlimited access to a dedicated insights team.' ) ); ?></p>
      <div class="cs-x21">
        <a class="cs-btn-primary cs-x22" data-cs-calendly href="#learn"><?php echo esc_html( competiscan_pg_f( 'mid_hero_btn1', 'See it in action' ) ); ?> <span class="cs-x23">&rarr;</span></a>
        <a class="cs-btn-outline cs-x24" href="#capabilities"><?php echo esc_html( competiscan_pg_f( 'mid_hero_btn2', "See what's inside" ) ); ?></a>
      </div>
    </div>
  </section>

  <!-- KEY CAPABILITIES -->
  <section class="cs-x25" id="capabilities">
    <div class="cs-x26">
      <?php foreach ( $caps as $i => $cap ) : ?>
      <div class="cs-x27">
        <span class="cs-x185"><?php echo isset( $cap_icons[ $i ] ) ? $cap_icons[ $i ] : $cap_icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
        <h2 class="cs-x31"><?php echo esc_html( $cap['title'] ); ?></h2>
        <p class="cs-x32"><?php echo esc_html( $cap['text'] ); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- WHITE PAPER -->
  <section class="cs-x352">
    <div class="cs-x65">
      <img class="cs-x353" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true">
      <div class="cs-x67">
        <div>
          <span class="cs-x354"><?php echo esc_html( competiscan_pg_f( 'mid_wp_eyebrow', 'White paper · Explore a use case' ) ); ?></span>
          <h2 class="cs-x69"><?php echo esc_html( competiscan_pg_f( 'mid_wp_title', 'Turning Credit Card Onboarding into Continuous Growth' ) ); ?></h2>
          <p class="cs-x70"><?php echo esc_html( competiscan_pg_f( 'mid_wp_desc', "See the kind of analysis the database powers: how leading credit card issuers turn the first 30–60 days of a cardholder's lifecycle into lasting engagement, drawn from real journeys captured in our longitudinal panel." ) ); ?></p>
        </div>
        <div class="cs-x355">
            <?php
            // Existing CF7 form: "Turning Credit Card Onboarding into Continuous Growth".
            $wp_form_id = function_exists( 'competiscan_whitepaper_form_id' ) ? competiscan_whitepaper_form_id() : 0;

            if ( $wp_form_id ) {
                $pdf = get_template_directory_uri() . '/assets/images/competiscan-whitepaper-credit-card-onboarding.pdf';

                echo '<div class="cs-cf7" data-cs-pdf="' . esc_url( $pdf ) . '" data-cs-btn="Download the white paper">'
                    . do_shortcode( '[contact-form-7 id="' . $wp_form_id . '"]' )
                    . '</div>';
            }
            ?>
        </div>
      </div>
    </div>
  </section>

  <!-- COVERAGE -->
  <section class="cs-x33">
    <div class="cs-x34">
      <div class="cs-x356">
        <h3 class="cs-x357">Channels</h3>
        <div class="cs-x281">
          <?php foreach ( $channels as $chip ) : ?><span class="cs-x282"><?php echo esc_html( $chip ); ?></span><?php endforeach; ?>
        </div>
      </div>
      <div class="cs-x356">
        <h3 class="cs-x357">Industries</h3>
        <div class="cs-x281">
          <?php foreach ( $industries as $chip ) : ?><span class="cs-x282"><?php echo esc_html( $chip ); ?></span><?php endforeach; ?>
        </div>
      </div>
      <div>
        <h3 class="cs-x357">Audiences</h3>
        <div class="cs-x281">
          <?php foreach ( $audiences as $chip ) : ?><span class="cs-x282"><?php echo esc_html( $chip ); ?></span><?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- LEARN MORE / CTA -->
  <section class="cs-x64" id="learn">
    <div class="cs-x180">
      <img class="cs-x144" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true">
      <div class="cs-x181">
        <h2 class="cs-x77"><?php echo esc_html( competiscan_pg_f( 'mid_cta_title', 'See the database in action' ) ); ?></h2>
        <p class="cs-x183"><?php echo esc_html( competiscan_pg_f( 'mid_cta_text', 'Our team will walk you through 200M+ omnichannel communications and how to put them to work against your competitive set.' ) ); ?></p>
        <a class="cs-btn-primary cs-x79" data-cs-calendly href="#learn"><?php echo esc_html( competiscan_pg_f( 'mid_cta_btn', 'See it in action' ) ); ?> <span class="cs-x23">&rarr;</span></a>
      </div>
    </div>
  </section>

  <!-- FAQ — shared layout, consistent across all pages -->
  <?php
  get_template_part(
    'acf-layouts/cs_faq_accordion',
    null,
    array(
      'variant'     => 'home',
      'title'       => competiscan_pg_f( 'mid_faq_title', 'Got Questions?' ),
      'description' => competiscan_pg_f( 'mid_faq_intro', 'Find answers to common questions about Competiscan and how we work.' ),
      'faqs'        => get_field( 'mid_faq' ),
    )
  );
  ?>

</div>

<?php
get_footer();
