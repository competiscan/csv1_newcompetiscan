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
          <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 3.26153C0 1.47805 1.42678 0.000307679 3.26122 0.000307679H22.8285C24.612 0.000307679 26.0898 1.47805 26.0898 3.26153V19.5676C26.0898 21.4021 24.612 22.8289 22.8285 22.8289H3.26122C1.42678 22.8289 0 21.4021 0 19.5676V3.26153ZM3.26122 4.89214C3.26122 5.80936 3.97461 6.52275 4.89183 6.52275C5.75809 6.52275 6.52244 5.80936 6.52244 4.89214C6.52244 4.02588 5.75809 3.26153 4.89183 3.26153C3.97461 3.26153 3.26122 4.02588 3.26122 4.89214ZM22.8285 4.89214C22.8285 4.2297 22.268 3.66918 21.6056 3.66918H9.37601C8.66262 3.66918 8.15305 4.2297 8.15305 4.89214C8.15305 5.60553 8.66262 6.1151 9.37601 6.1151H21.6056C22.268 6.1151 22.8285 5.60553 22.8285 4.89214Z" fill="#004B81"/>
          </svg>
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
        <svg width="33" height="27" viewBox="0 0 33 27" fill="none" xmlns=" http://www.w3.org/2000/svg">
          <path d="M7.33775 -0.000419855C8.76453 -0.000419855 10.0894 0.814885 10.8538 2.03784C11.5671 3.31176 11.5671 4.89141 10.8538 6.11437C10.0894 7.38828 8.76453 8.15263 7.33775 8.15263C5.86001 8.15263 4.53514 7.38828 3.77079 6.11437C3.05739 4.89141 3.05739 3.31176 3.77079 2.03784C4.53514 0.814885 5.86001 -0.000419855 7.33775 -0.000419855ZM26.0898 -0.000419855C27.5165 -0.000419855 28.8414 0.814885 29.6058 2.03784C30.3192 3.31176 30.3192 4.89141 29.6058 6.11437C28.8414 7.38828 27.5165 8.15263 26.0898 8.15263C24.612 8.15263 23.2872 7.38828 22.5228 6.11437C21.8094 4.89141 21.8094 3.31176 22.5228 2.03784C23.2872 0.814885 24.612 -0.000419855 26.0898 -0.000419855ZM0 15.2356C0 12.2292 2.39496 9.78324 5.4014 9.78324H7.59253C8.40783 9.78324 9.17218 9.98707 9.88558 10.2928C9.78366 10.6495 9.78366 11.0572 9.78366 11.4139C9.78366 13.4012 10.599 15.1337 11.9748 16.3057C11.9748 16.3057 11.9748 16.3057 11.9238 16.3057H1.07009C0.458609 16.3057 0 15.8471 0 15.2356ZM20.6374 16.3057H20.5865C21.9623 15.1337 22.7776 13.4012 22.7776 11.4139C22.7776 11.0572 22.7776 10.6495 22.7266 10.2928C23.3891 9.98707 24.1534 9.78324 24.9687 9.78324H27.1599C30.1663 9.78324 32.6122 12.2292 32.6122 15.2356C32.6122 15.8471 32.1026 16.3057 31.4912 16.3057H20.6374ZM11.4143 11.4139C11.4143 9.68133 12.3315 8.10168 13.8602 7.18446C15.3379 6.3182 17.2233 6.3182 18.752 7.18446C20.2298 8.10168 21.1979 9.68133 21.1979 11.4139C21.1979 13.1973 20.2298 14.777 18.752 15.6942C17.2233 16.5605 15.3379 16.5605 13.8602 15.6942C12.3315 14.777 11.4143 13.1973 11.4143 11.4139ZM6.52244 24.7645C6.52244 20.9937 9.52888 17.9363 13.2997 17.9363H19.2616C23.0324 17.9363 26.0898 20.9937 26.0898 24.7645C26.0898 25.4779 25.4783 26.0893 24.7139 26.0893H7.84731C7.13392 26.0893 6.52244 25.5288 6.52244 24.7645Z" fill="#004B81"/>
        </svg>

       <?php echo esc_html( $aud_p ); ?> <strong><?php echo esc_html( $aud_v ); ?></strong></div>
    </div>
  </div>
</section>
