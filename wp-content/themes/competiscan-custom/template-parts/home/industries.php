<?php
/**
 * Industries we serve.
 *
 * Note: the first item is a <div class="industry-item" href="#"> in the HTML source,
 * not an <a>. That is reproduced verbatim rather than corrected, so the rendered DOM
 * matches the original exactly.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';

// Every item after the first renders as an <a>.
$industries = array(
	array( 'icon' => 'house.svg', 'label' => 'Mortgage &amp; Loans' ),
	array( 'icon' => 'credit-card.svg', 'label' => 'Credit Cards' ),
	array( 'icon' => 'retail.svg', 'label' => 'Retail' ),
	array( 'icon' => 'insurance.svg', 'label' => 'Insurance' ),
	array( 'icon' => 'telecoms.svg', 'label' => 'Telecoms' ),
	array( 'icon' => 'plane.svg', 'label' => 'Investment &amp; Wealth' ),
	array( 'icon' => 'more.svg', 'label' => 'And more...' ),
);
?>
<!-- ============ INDUSTRIES ============ -->
<section class="section">
  <div class="container industries-grid">
    <div class="industries-copy">
      <h2>Industries <br>We Serve</h2>
      <p>Etiam accumsan urna a mauris dapibus, nec aliquet nunc convallis. Phasellus eget justo et libero ultrices posuere.</p>
      <a href="#" class="btn btn-primary for_desktop">Learn More</a>
    </div>
    <div class="industry-list">
      <div class="industry-item" href="#">
        <div class="ic">
          <img src="<?php echo esc_url( $img . 'banking.svg' ); ?>" alt="icon">
        </div>
        <span>Banking</span>
      </div>
      <?php foreach ( $industries as $industry ) : ?>
      <a class="industry-item" href="#">
        <div class="ic">
          <img src="<?php echo esc_url( $img . $industry['icon'] ); ?>" alt="icon">
        </div>
        <span><?php echo $industry['label']; // phpcs:ignore WordPress.Security.EscapeOutput -- entities are intentional. ?></span>
      </a>
      <?php endforeach; ?>
      <a href="#" class="btn btn-primary for_mobile">Learn More</a>

    </div>
  </div>
</section>
