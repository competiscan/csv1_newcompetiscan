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
		array( 'question' => 'Who is Competiscan?', 'answer' => 'Competiscan is a leading-edge market intelligence company, providing clients with best-in-class service.', 'order' => 1 ),
    array( 'question' => 'What services does Competiscan provide?', 'answer' => 'Competiscan delivers market and competitive intelligence that helps organizations understand how competitors engage with different audiences across channels. Our capabilities include value proposition tracking, creative trends, customer journeys and experiences, behind-the-login insights, marketing strategies, targeting insights, AI-driven analysis of marketing campaigns, and more. With access to the largest consumer panels in the market, we deliver real-time insight into competitors activities.', 'order' => 2 ),
    array( 'question' => 'What channels does Competiscan monitor?', 'answer' => 'We monitor a variety of media channels for a comprehensive view of how competitors communicate with customers and prospects, including direct mail, email, digital, social media, and print.', 'order' => 3 ),
    array( 'question' => 'What industries does Competiscan cover?', 'answer' => 'Competiscan covers key sectors, including Automotive, Banking, Payment Cards, Energy, Insurance, Investments, Mortgage & Loan, Retail, Travel & Leisure, and Telecom.', 'order' => 4 ),
    array( 'question' => 'What audiences does Competiscan cover?', 'answer' => 'Our panel includes a diverse range of audiences: Consumers, Business Owners, Insurance Producers, Financial Advisors, Mortgage Brokers, and Healthcare Providers.', 'order' => 5 ),
    array( 'question' => 'What parts of the customer journey does Competiscan capture?', 'answer' => 'We provide a 360-degree view of customer-journey communications, including acquisition, follow-up, loyalty, retention, win-back, statements, and upgrade & cross-sell.', 'order' => 6 ),
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
      <?php
      foreach ( $faqs as $i => $faq ) :
        // Accept either q/a or question/answer keys so every data source works.
        $cs_q = isset( $faq['q'] ) ? $faq['q'] : ( isset( $faq['question'] ) ? $faq['question'] : '' );
        $cs_a = isset( $faq['a'] ) ? $faq['a'] : ( isset( $faq['answer'] ) ? $faq['answer'] : '' );
        ?>
      <div class="faq-item<?php echo 0 === $i ? ' active' : ''; ?>">
        <?php if ( 'insights' === $variant ) : ?>
        <button class="faq-q"><span><span class="dot">•</span><?php echo esc_html( $cs_q ); ?></span><span class="faq-toggle-ic"></span></button>
        <?php else : ?>
        <button class="faq-q">
          <span class="lft">
            <span class="dot">•</span> 
            <span class="faq_que"><?php echo esc_html( $cs_q ); ?></span>
          </span>
          <span class="faq-toggle-ic"></span>
        </button>
        <?php endif; ?>
        <div class="faq-a"><p><?php echo wp_kses_post( $cs_a ); ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
