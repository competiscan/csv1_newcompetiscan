<?php
/**
 * FAQ accordion — reusable layout (design + behaviour unchanged).
 *
 * Renders from ACF, in this priority order:
 *   1. Data passed via get_template_part( ..., array( 'title', 'description',
 *      'faqs', 'variant', 'section_id' ) ) — used by dedicated page templates
 *      such as template-about.php.
 *   2. The ACF Flexible-Content sub-fields of the "cs_faq_accordion" layout
 *      (Section Title, Description, FAQ repeater "faqs" of question/answer) when
 *      rendered inside template-cms.php's have_rows( 'cms_content' ) loop.
 *   3. Sensible defaults, so the section is never empty.
 *
 * Markup, classes and the accordion behaviour (assets/js/main.js keys off
 * .faq-item.active) are identical to the previous version — only the data
 * source is now ACF-driven, so the FAQ is fully editable from the admin.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = isset( $args['variant'] ) ? $args['variant'] : 'home';

// --- Title -------------------------------------------------------------------
$cs_faq_title = isset( $args['title'] ) ? $args['title'] : ( function_exists( 'get_sub_field' ) ? get_sub_field( 'title' ) : '' );
if ( ! $cs_faq_title ) {
	$cs_faq_title = 'Got Questions?';
}

// --- Description --------------------------------------------------------------
$cs_faq_desc = isset( $args['description'] ) ? $args['description'] : ( function_exists( 'get_sub_field' ) ? get_sub_field( 'description' ) : '' );

// --- Section id ---------------------------------------------------------------
$cs_faq_section_id = isset( $args['section_id'] ) ? $args['section_id'] : ( function_exists( 'get_sub_field' ) ? get_sub_field( 'section_id' ) : '' );
if ( ! $cs_faq_section_id ) {
	$cs_faq_section_id = 'faq';
}

// --- FAQ items → normalise to array( array( 'q' => , 'a' => ) ) ---------------
$faqs = array();
if ( isset( $args['faqs'] ) && is_array( $args['faqs'] ) ) {
	foreach ( $args['faqs'] as $row ) {
		$faqs[] = array(
			'q' => isset( $row['question'] ) ? $row['question'] : ( isset( $row['q'] ) ? $row['q'] : '' ),
			'a' => isset( $row['answer'] ) ? $row['answer'] : ( isset( $row['a'] ) ? $row['a'] : '' ),
		);
	}
} elseif ( function_exists( 'have_rows' ) && have_rows( 'faqs' ) ) {
	while ( have_rows( 'faqs' ) ) {
		the_row();
		$faqs[] = array(
			'q' => get_sub_field( 'question' ),
			'a' => get_sub_field( 'answer' ),
		);
	}
}

if ( empty( $faqs ) ) {
	$faqs = array(
		array( 'q' => 'Who is Competiscan?', 'a' => 'Competiscan is a leading-edge competitive intelligence and market research company, providing clients with best-in-class service.' ),
		array( 'q' => 'What services does Competiscan provide?', 'a' => 'We provide market intelligence databases, value proposition trackers, custom research and analysis, and an AI-powered toolkit.' ),
		array( 'q' => 'What channels does Competiscan monitor?', 'a' => 'Direct mail, email, digital, social media, and print channels across the marketplace.' ),
		array( 'q' => 'What industries does Competiscan cover?', 'a' => 'Banking, credit cards, insurance, investment &amp; wealth, mortgage &amp; loans, retail, telecoms, and more.' ),
		array( 'q' => 'What audiences does Competiscan cover?', 'a' => 'Consumers, business owners, and financial advisors/brokers across our omni-channel panels.' ),
		array( 'q' => 'What parts of the customer journey does Competiscan capture?', 'a' => 'From acquisition and onboarding through retention and loyalty stages of the customer journey.' ),
	);
}
?>
<!-- ============ FAQ ============ -->
<section class="section faq" id="<?php echo esc_attr( $cs_faq_section_id ); ?>">
  <div class="container faq-grid">
    <div class="faq-copy">
      <h2><span><?php echo esc_html( $cs_faq_title ); ?></span> <br> We've Got <br> Answers</h2>
      <?php if ( $cs_faq_desc ) : ?>
      <p><?php echo wp_kses_post( $cs_faq_desc ); ?></p>
      <?php endif; ?>
    </div>
    <div class="faq-list">
      <?php foreach ( $faqs as $i => $faq ) : ?>
      <div class="faq-item<?php echo 0 === $i ? ' active' : ''; ?>">
        <?php if ( 'insights' === $variant ) : ?>
        <button class="faq-q"><span><span class="dot">•</span><?php echo esc_html( $faq['q'] ); ?></span><span class="faq-toggle-ic"></span></button>
        <?php else : ?>
        <button class="faq-q">
          <span class="lft">
            <span class="dot">•</span> <span><?php echo esc_html( $faq['q'] ); ?></span>
          </span>
          <span class="faq-toggle-ic"></span>
        </button>
        <?php endif; ?>
        <div class="faq-a"><p><?php echo wp_kses_post( $faq['a'] ); ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
