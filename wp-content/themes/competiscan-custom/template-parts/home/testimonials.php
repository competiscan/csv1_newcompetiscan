<?php
/**
 * Testimonial slider (Slick, variableWidth — initialised in assets/js/main.js).
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';

$quote_svg = '<svg width="121" height="103" viewBox="0 0 121 103" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0 51.6123C0 42.0303 1.95996 33.3919 5.87988 25.6973C9.94499 18.0026 15.8249 11.9049 23.5195 7.4043C31.2142 2.75846 40.4333 0.290365 51.1768 0V22.6484C42.0303 22.6484 35.2067 25.1891 30.7061 30.2705C26.3506 35.3519 24.1729 42.4658 24.1729 51.6123V56.8389H48.999V103.007H0V51.6123ZM120.646 22.6484C111.5 22.6484 104.676 25.1891 100.176 30.2705C95.8203 35.3519 93.6426 42.4658 93.6426 51.6123V56.8389H118.469V103.007H69.4697V51.6123C69.4697 42.0303 71.4297 33.3919 75.3496 25.6973C79.4147 18.0026 85.2946 11.9049 92.9893 7.4043C100.684 2.75846 109.903 0.290365 120.646 0V22.6484Z" fill="white" fill-opacity="0.1"/>
                  </svg>';

$testimonials = array(
	array(
		'img'   => 'leaders-1.png',
		'alt'   => 'Team meeting',
		'icon'  => 'icon1.svg',
		'quote' => '“Competiscan is a reliable source to receive timely competitor intelligence with national and local perspectives. We are very highly pleased with the results and relationship with Competiscan.”',
		'name'  => 'Head of Sales',
		'role'  => 'Health Insurance Client',
	),
	array(
		'img'   => 'leaders-2.png',
		'alt'   => 'Executive portrait',
		'icon'  => 'icon2.svg',
		'quote' => '“Competiscan’s database is thorough and easy to search, but what really stands out is their research. The insights team goes the extra mile to understand and respond to our needs.”',
		'name'  => 'Director of Marketing',
		'role'  => 'Direct Marketing Client',
	),
	array(
		'img'   => 'leaders-3.png',
		'alt'   => 'Analyst at desk',
		'icon'  => 'icon3.svg',
		'quote' => '"The team has been phenomenal – they are super responsive to queries and are willing to work with us on our requests outside of the self-servicing platform. We also appreciate the fast turnaround as well. The Competiscan team are stars!!”',
		'name'  => 'Investor',
		'role'  => 'Investment Client',
	),
	array(
		'img'   => 'leaders-4.png',
		'alt'   => 'Analyst at desk',
		'icon'  => 'icon4.svg',
		'quote' => '“The Competiscan team is amazing to work with. From helping solve problems and account coordination to presenting, the team is nailing it with me and my business partners. I appreciate all of Competiscan’s support!”',
		'name'  => 'Financial Manager',
		'role'  => 'Financial Services Client',
	),
);
?>
<!-- ============ TESTIMONIALS ============ -->
<section class="section testimonials">
  <div class="container">
    <div class="testi-head">
      <h2>Market Leaders Trust Competiscan</h2>
      <div class="slick-arrow-group">
        <button class="carousel-arrow prev" aria-label="Previous">
          <svg width="16" height="12" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="carousel-arrow next" aria-label="Next">
          <svg width="16" height="12" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>
    <div class="testi-wrapper">
      <div class="testi-track">
        <?php foreach ( $testimonials as $t ) : ?>
        <div class="testi-card">
          <div class="slide-card">
            <img src="<?php echo esc_url( $img . $t['img'] ); ?>" alt="<?php echo esc_attr( $t['alt'] ); ?>">
            <div class="testi-quote">
              <div class="quote">
                <?php echo $quote_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?>
              </div>
              <p><?php echo esc_html( $t['quote'] ); ?></p>
              <div class="testi-person">
                <span class="av">
                  <img src="<?php echo esc_url( $img . $t['icon'] ); ?>" alt="icon">
                </span>
                <div class="designation">
                  <strong><?php echo esc_html( $t['name'] ); ?></strong><span><?php echo esc_html( $t['role'] ); ?></span></div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
