<?php
/**
 * Home hero. The site header renders inside this section, as in the HTML source.
 *
 * All text/images/links are editable from the admin (ACF "Home — Hero"). The markup,
 * classes and the fixed floating-card layout are unchanged — only the content is
 * ACF-driven, with the original copy as fallback so the section is never empty.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';
$f   = function_exists( 'get_field' );
$pid = (int) get_option( 'page_on_front' );

$get = static function ( $name, $fallback ) use ( $f, $pid ) {
	$v = $f ? get_field( $name, $pid ) : '';
	return ( '' === $v || null === $v || false === $v ) ? $fallback : $v;
};

$hero_heading = $get( 'hero_heading', 'Your Single Source for Market and Competitive Insights' );
$hero_text    = $get( 'hero_text', 'Competiscan transforms direct mail and digital marketing into actionable insights. Our best-in-class service leverages the largest longitudinal, omni-channel panels of consumers, business owners, insurance producers, financial advisors, and mortgage brokers in the marketplace.' );
$hero_btn     = $get( 'hero_button_text', 'See it in action' );
$hero_btn_url = $get( 'hero_button_url', '#learn' );

// Site convention: a "#learn" (or Calendly) URL opens the Calendly popup — handled
// site-wide by assets/js/contact.js via the data-cs-calendly attribute.
$hero_is_calendly = ( '#learn' === $hero_btn_url || false !== strpos( (string) $hero_btn_url, 'calendly.com' ) );

$hero_img     = $get( 'hero_image', $img . 'hero-image.jpg' );
$hero_alt     = $get( 'hero_image_alt', 'Financial advisor reviewing insights dashboard' );

$c_media_p = $get( 'hero_media_prefix', 'Media:' );
$c_media_v = $get( 'hero_media_value', 'Direct Mail' );
$c1_title  = $get( 'hero_card1_title', 'Banking' );
$c1_text   = $get( 'hero_card1_text', 'Increase loyalty from acquisition through onboarding and retention' );
$c1_tags   = array_filter( array_map( 'trim', explode( ',', (string) $get( 'hero_card1_tags', 'Acquisition, Loyalty' ) ) ) );
$c2_title  = $get( 'hero_card2_title', 'Insurance' );
$c2_text   = $get( 'hero_card2_text', 'Monitor for new policies, rate changes, and channel preference.' );
$c2_tags   = array_filter( array_map( 'trim', explode( ',', (string) $get( 'hero_card2_tags', 'Product Updates' ) ) ) );
$c3_title  = $get( 'hero_card3_title', 'Consumer Services' );
$c3_text   = $get( 'hero_card3_text', 'Discover marketing volume, digital impressions and campaign learning.' );
$c3_link   = $get( 'hero_card3_link_label', 'My Email' );
$ch_strong = $get( 'hero_channel_strong', 'Channel Utilization:' );
$ch_text   = $get( 'hero_channel_text', 'Direct Mail vs. Digital' );
$donut_img = $get( 'hero_donut_image', $img . 'donut-chart.svg' );
$aud_p     = $get( 'hero_audience_prefix', 'Audience:' );
$aud_v     = $get( 'hero_audience_value', 'Financial Advisors' );
?>
<!-- ============ HERO ============ -->
<section class="hero">
  <?php get_template_part( 'template-parts/site-header' ); ?>

  <div class="container hero-grid">
    <div class="hero-copy">
      <h1 class="hero-heading">
        <?php echo nl2br( esc_html( $hero_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- nl2br over esc_html. ?>
      </h1>
      <p><?php echo esc_html( $hero_text ); ?></p>
      <div class="hero-form">
        <a class="btn btn-primary"<?php echo $hero_is_calendly ? ' data-cs-calendly' : ''; ?> href="<?php echo esc_url( $hero_btn_url ); ?>"><?php echo esc_html( $hero_btn ); ?>
          <svg width="14" height="10" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
    </div>
    <div class="hero-media">
      <img src="<?php echo esc_url( $hero_img ); ?>" class="media-img" alt="<?php echo esc_attr( $hero_alt ); ?>">
      <div class="float-card card-top">
      <span style="display: flex; margin-right: 10px; align-items: center; justify-content: center; width: 34px; height: 26px; border-radius: 6px; background: rgb(0,75,129); flex: none;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="rgb(255,255,255)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 8-10 6L2 8"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>
      </span>
      <?php echo esc_html( $c_media_p ); ?> <strong><?php echo esc_html( $c_media_v ); ?></strong></div>
      <div class="float-card card-banking">
        <div class="card-dot blue"></div>
        <div class="cnt">
          <span class="arrow">›</span>
          <h4><?php echo esc_html( $c1_title ); ?></h4>
          <p>
            <?php echo esc_html( $c1_text ); ?>
          </p>
          <div class="tags">
              <?php foreach ( $c1_tags as $tag ) : ?>
              <span><?php echo esc_html( $tag ); ?></span>
              <?php endforeach; ?>
          </div>
        </div>
      </div><!---->

      <div class="float-card card-insurance">
          <div class="card-dot orange"></div>
          <div class="cnt">
            <span class="arrow">›</span>
            <h4><?php echo esc_html( $c2_title ); ?></h4>
            <p>
                <?php echo esc_html( $c2_text ); ?>
            </p>
            <div class="tags">
                <?php foreach ( $c2_tags as $tag ) : ?>
                <span><?php echo esc_html( $tag ); ?></span>
                <?php endforeach; ?>
            </div>
          </div>
      </div>

      <div class="float-card card-consumer">
        <div class="card-dot green"></div>
        <div class="cnt">
            <span class="arrow">›</span>
          <h4><?php echo esc_html( $c3_title ); ?></h4>
          <p>
              <?php echo esc_html( $c3_text ); ?>
          </p>
          <div class="tags">
              <a href="#"><?php echo esc_html( $c3_link ); ?></a>
          </div>
        </div>
      </div>
      <div class="float-card card-percent">
        <span class="ring">
          <img src="<?php echo esc_url( $donut_img ); ?>" alt="donut-chart">
        </span>
        <strong><?php echo esc_html( $ch_strong ); ?></strong>
        <?php echo esc_html( $ch_text ); ?>
      </div>
      <div class="float-card card-audience">
        <span style="display: flex; align-items: center; justify-content: center; width: 34px; height: 26px; border-radius: 6px; background: rgb(0,75,129); flex: none;"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="rgb(255,255,255)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path></svg></span>

       <?php echo esc_html( $aud_p ); ?> <strong><?php echo esc_html( $aud_v ); ?></strong>
     </div>
    </div>
  </div>
</section>
