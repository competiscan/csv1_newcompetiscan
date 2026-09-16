<?php
/**
 * Fallback template — WordPress uses this when no more specific template matches
 * (for example the blog posts index).
 *
 * @package Competiscan_Custom
 */

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	array( 'title' => is_home() && get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Insights from <br> Industry Experts', 'competiscan-custom' ) )
);
?>
<section class="section featured-articles" style="padding-top:var(--space-lg);">
  <div class="container">
    <?php if ( have_posts() ) : ?>
    <div class="articles-grid">
      <?php
      while ( have_posts() ) :
        the_post();
        get_template_part( 'template-parts/article-card' );
      endwhile;
      ?>
    </div>
    <?php competiscan_pagination(); ?>
    <?php else : ?>
    <p class="conferences-note"><?php esc_html_e( 'Nothing has been published here yet.', 'competiscan-custom' ); ?></p>
    <?php endif; ?>
  </div>
</section>
<?php
get_footer();
