<?php
/**
 * FAQ accordion — shared by the home and Insights pages.
 *
 * The two pages differ in the source: the home page wraps the question text in
 * <span class="lft"> and uses a line-broken heading, while Insights uses a plain
 * <span> and a single-line heading. Pass 'variant' => 'insights' for the latter.
 *
 * Accordion behaviour lives in assets/js/main.js and keys off .faq-item.active.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = isset( $args['variant'] ) ? $args['variant'] : 'home';

$faqs = array(
	array(
		'q' => 'Who is Competiscan?',
		'a' => 'Competiscan is a leading-edge competitive intelligence and market research company, providing clients with best-in-class service.',
	),
	array(
		'q' => 'What services does Competiscan provide?',
		'a' => 'We provide market intelligence databases, value proposition trackers, custom research and analysis, and an AI-powered toolkit.',
	),
	array(
		'q' => 'What channels does Competiscan monitor?',
		'a' => 'Direct mail, email, digital, social media, and print channels across the marketplace.',
	),
	array(
		'q' => 'What industries does Competiscan cover?',
		'a' => 'Banking, credit cards, insurance, investment &amp; wealth, mortgage &amp; loans, retail, telecoms, and more.',
	),
	array(
		'q' => 'What audiences does Competiscan cover?',
		'a' => 'Consumers, business owners, and financial advisors/brokers across our omni-channel panels.',
	),
	array(
		'q' => 'What parts of the customer journey does Competiscan capture?',
		'a' => 'From acquisition and onboarding through retention and loyalty stages of the customer journey.',
	),
);
?>
<!-- ============ FAQ ============ -->
<section class="section faq" id="faq">
  <div class="container faq-grid">
    <div class="faq-copy">
      <?php if ( 'insights' === $variant ) : ?>
      <h2><span>Got Questions?</span> <br> We've Got <br> Answers</h2>
      <?php else : ?>
      <h2><span>Got Questions?</span> <br> We've Got <br> Answers</h2>
      <?php endif; ?>
      <p>In hac habitasse platea dictumst. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
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
