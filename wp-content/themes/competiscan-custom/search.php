<?php
/**
 * Search results.
 *
 * @package Competiscan_Custom
 */

get_header();

get_template_part(
	'template-parts/page-hero',
	null,
	array(
		/* translators: %s: search query. */
		'title' => sprintf( esc_html__( 'Search results for %s', 'competiscan-custom' ), '&ldquo;' . esc_html( get_search_query() ) . '&rdquo;' ),
	)
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
    <p class="conferences-note"><?php esc_html_e( 'No results matched your search. Try a different term.', 'competiscan-custom' ); ?></p>
    <div class="search-box">
      <?php get_search_form(); ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php
get_footer();
