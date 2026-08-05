<?php
/**
 * Home page — a 1:1 conversion of index.html.
 *
 * Section order and the .industrie-section wrapper around Industries + Testimonials
 * are preserved from the source; the wrapper carries the shared background treatment.
 *
 * @package Competiscan_Custom
 */

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/partners' );
get_template_part( 'template-parts/home/tracking' );
get_template_part( 'template-parts/home/stats' );
?>
<div class="industrie-section">
  <?php
  get_template_part( 'template-parts/home/industries' );
  get_template_part( 'template-parts/home/testimonials' );
  ?>
</div>
<?php
get_template_part( 'template-parts/faq', null, array( 'variant' => 'home' ) );

get_footer();
