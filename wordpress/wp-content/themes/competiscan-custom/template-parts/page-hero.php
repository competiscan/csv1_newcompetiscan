<?php
/**
 * Inner-page hero — the .insights-hero band from the source, reused by page.php,
 * single.php, archive.php, search.php and 404.php so every page carries the same
 * header treatment as Insights.
 *
 * Accepts: 'title' (string, may contain inline HTML), 'subtitle' (string).
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = isset( $args['title'] ) ? $args['title'] : '';
$subtitle = isset( $args['subtitle'] ) ? $args['subtitle'] : '';
?>
<section class="insights-hero">
  <?php get_template_part( 'template-parts/site-header' ); ?>
  <div class="container">
    <h1><?php echo wp_kses_post( $title ); ?></h1>
    <?php if ( $subtitle ) : ?>
    <p><?php echo wp_kses_post( $subtitle ); ?></p>
    <?php endif; ?>
  </div>
</section>
