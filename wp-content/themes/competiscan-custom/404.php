<?php
/**
 * 404.
 *
 * @package Competiscan_Custom
 */

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	array( 'title' => __( 'Page not <br> found', 'competiscan-custom' ) )
);
?>
<section class="section">
  <div class="container">
    <p class="conferences-note"><?php esc_html_e( 'The page you were looking for has moved or no longer exists.', 'competiscan-custom' ); ?></p>
    <p>
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
        <?php esc_html_e( 'Back to home', 'competiscan-custom' ); ?>
        <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
      </a>
    </p>
  </div>
</section>
<?php
get_footer();
