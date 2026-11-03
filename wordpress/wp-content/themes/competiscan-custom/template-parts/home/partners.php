<?php
/**
 * Partner logo marquee ("Our Trusted Partners").
 *
 * Logos come from the Home page ACF repeater "Partner Logos" (Pages → Home →
 * "Home — Our Trusted Partners"): add, delete and drag to reorder there. Every row
 * is printed, so there is no limit. If the admin removes every logo the section is
 * hidden. The bundled theme logos are only used when ACF is not active.
 *
 * main.js duplicates .marquee-row's innerHTML on load to make the loop seamless,
 * so only the single set is printed here — same as the HTML source.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img     = get_template_directory_uri() . '/assets/images/';
$pid     = (int) get_option( 'page_on_front' );
$has_acf = function_exists( 'get_field' );

$eyebrow = $has_acf ? get_field( 'partners_eyebrow', $pid ) : '';
if ( ! $eyebrow ) {
	$eyebrow = 'Our Trusted Partners';
}

// Each logo: array( 'url' => …, 'alt' => … ).
$logos = array();
if ( $has_acf ) {
	$logo_rows = get_field( 'partners_logos', $pid );
	if ( is_array( $logo_rows ) ) {
		foreach ( $logo_rows as $r ) {
			$logo = isset( $r['logo'] ) ? $r['logo'] : null;
			if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
				// ACF image array (current return format).
				$logos[] = array(
					'url' => $logo['url'],
					'alt' => isset( $logo['alt'] ) ? trim( (string) $logo['alt'] ) : '',
				);
			} elseif ( is_numeric( $logo ) && (int) $logo > 0 ) {
				// Attachment ID (if the return format is ever switched to "ID").
				$url = wp_get_attachment_image_url( (int) $logo, 'full' );
				if ( $url ) {
					$logos[] = array(
						'url' => $url,
						'alt' => trim( (string) get_post_meta( (int) $logo, '_wp_attachment_image_alt', true ) ),
					);
				}
			} elseif ( is_string( $logo ) && '' !== $logo ) {
				// Plain URL (if the return format is ever switched to "URL").
				$logos[] = array(
					'url' => $logo,
					'alt' => '',
				);
			}
		}
	}
} else {
	foreach ( competiscan_home_partner_logos() as $fn ) {
		$logos[] = array(
			'url' => $img . 'companylogos/' . $fn,
			'alt' => '',
		);
	}
}

// All logos removed in WP Admin → hide the whole section.
if ( empty( $logos ) ) {
	return;
}
?>
<!-- ============ PARTNERS ============ -->
<section class="partners">
  <div class="container">
    <span class="eyebrow-pill"><?php echo esc_html( $eyebrow ); ?></span>
    </div>
    <div class="partners-track container">
      <div class="marquee-row">
        <?php foreach ( $logos as $logo ) : ?>
        <span class="logo-chip"><img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( '' !== $logo['alt'] ? $logo['alt'] : 'logo' ); ?>"></span>
        <?php endforeach; ?>
        <!-- duplicate set for seamless infinite loop -->
      </div>
    </div>

</section>
