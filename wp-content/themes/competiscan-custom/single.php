<?php
/**
 * Single Insight (post) detail.
 *
 * No dedicated source HTML exists for the article detail view, so this is built
 * from the site's existing design system: it reuses the .insights-hero band, the
 * .article-card design for Related Insights, the Noptin newsletter section, and the
 * shared SVG/arrow helpers. Every piece of content is dynamic — nothing hardcoded.
 *
 * @package Competiscan_Custom
 */

get_header();

while ( have_posts() ) :
	the_post();

	$competiscan_pid  = get_the_ID();
	$competiscan_cats = get_the_category();
	$competiscan_cat  = ! empty( $competiscan_cats ) ? $competiscan_cats[0] : null;

	// Reading time (≈200 wpm) from the post body.
	$competiscan_words   = str_word_count( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ) );
	$competiscan_rtime   = max( 1, (int) ceil( $competiscan_words / 200 ) );

	// Share targets — the current post URL + title.
	$competiscan_url     = get_permalink();
	$competiscan_title   = get_the_title();
	$competiscan_share   = array(
		'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $competiscan_url ),
		'x'        => 'https://twitter.com/intent/tweet?url=' . rawurlencode( $competiscan_url ) . '&text=' . rawurlencode( $competiscan_title ),
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $competiscan_url ),
		'email'    => 'mailto:?subject=' . rawurlencode( $competiscan_title ) . '&body=' . rawurlencode( $competiscan_url ),
	);
	?>

<!-- ============ INSIGHT HERO ============ -->
<section class="insights-hero insights-hero--single">
  <?php get_template_part( 'template-parts/site-header' ); ?>
  <div class="container">
    <?php if ( $competiscan_cat ) : ?>
    <a class="insight-eyebrow" href="<?php echo esc_url( get_category_link( $competiscan_cat->term_id ) ); ?>"><?php echo esc_html( $competiscan_cat->name ); ?></a>
    <?php endif; ?>
    <h1><?php echo esc_html( get_the_title() ); ?></h1>
    <div class="insight-meta">
      <span class="insight-meta__item"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
      <?php $competiscan_author = get_the_author(); ?>
      <?php if ( $competiscan_author ) : ?>
      <span class="insight-meta__sep" aria-hidden="true">&bull;</span>
      <span class="insight-meta__item"><?php echo esc_html( sprintf( /* translators: %s: author name. */ __( 'By %s', 'competiscan-custom' ), $competiscan_author ) ); ?></span>
      <?php endif; ?>
      <?php if ( $competiscan_words > 0 ) : ?>
      <span class="insight-meta__sep" aria-hidden="true">&bull;</span>
      <span class="insight-meta__item"><?php echo esc_html( sprintf( /* translators: %d: minutes. */ _n( '%d min read', '%d min read', $competiscan_rtime, 'competiscan-custom' ), $competiscan_rtime ) ); ?></span>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ============ INSIGHT BODY ============ -->
<section class="insight-single">
  <div class="container">
    <div class="insight-narrow">

      <?php if ( has_post_thumbnail() ) : ?>
      <figure class="insight-featured">
        <?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
      </figure>
      <?php endif; ?>

      <article <?php post_class( 'entry-content insight-content' ); ?>>
        <?php
        the_content();

        wp_link_pages(
          array(
            'before' => '<div class="page-links">',
            'after'  => '</div>',
          )
        );
        ?>
      </article>

      <!-- Social sharing -->
      <div class="insight-share">
        <span class="insight-share__label"><?php esc_html_e( 'Share', 'competiscan-custom' ); ?></span>
        <a class="insight-share__link" href="<?php echo esc_url( $competiscan_share['linkedin'] ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'competiscan-custom' ); ?>"><?php echo competiscan_social_svg( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
        <a class="insight-share__link" href="<?php echo esc_url( $competiscan_share['x'] ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on X', 'competiscan-custom' ); ?>"><?php echo competiscan_social_svg( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
        <a class="insight-share__link" href="<?php echo esc_url( $competiscan_share['facebook'] ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on Facebook', 'competiscan-custom' ); ?>"><?php echo competiscan_social_svg( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
        <a class="insight-share__link" href="<?php echo esc_url( $competiscan_share['email'] ); ?>" aria-label="<?php esc_attr_e( 'Share via email', 'competiscan-custom' ); ?>"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00ABAB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 6-10 7L2 6"></path></svg></a>
      </div>

      <!-- Previous / Next -->
      <?php
      $competiscan_prev = get_previous_post();
      $competiscan_next = get_next_post();
      if ( $competiscan_prev || $competiscan_next ) :
        ?>
      <nav class="insight-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'competiscan-custom' ); ?>">
        <?php if ( $competiscan_prev ) : ?>
        <a class="insight-nav__link insight-nav__prev" href="<?php echo esc_url( get_permalink( $competiscan_prev ) ); ?>" rel="prev">
          <span class="insight-nav__dir"><?php echo competiscan_pager_arrow( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php esc_html_e( 'Previous', 'competiscan-custom' ); ?></span>
          <span class="insight-nav__title"><?php echo esc_html( get_the_title( $competiscan_prev ) ); ?></span>
        </a>
        <?php else : ?>
        <span class="insight-nav__spacer" aria-hidden="true"></span>
        <?php endif; ?>

        <?php if ( $competiscan_next ) : ?>
        <a class="insight-nav__link insight-nav__next" href="<?php echo esc_url( get_permalink( $competiscan_next ) ); ?>" rel="next">
          <span class="insight-nav__dir"><?php esc_html_e( 'Next', 'competiscan-custom' ); ?> <?php echo competiscan_pager_arrow( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
          <span class="insight-nav__title"><?php echo esc_html( get_the_title( $competiscan_next ) ); ?></span>
        </a>
        <?php endif; ?>
      </nav>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php
	// ---- Related Insights (same category, newest first, current post excluded) ----
	$competiscan_related_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'post__not_in'        => array( $competiscan_pid ),
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'no_found_rows'       => true,
	);
	if ( $competiscan_cat ) {
		$competiscan_related_args['cat'] = $competiscan_cat->term_id;
	}
	$competiscan_related = new WP_Query( $competiscan_related_args );

	// Fall back to the newest posts if this category has no other posts.
	if ( ! $competiscan_related->have_posts() && $competiscan_cat ) {
		unset( $competiscan_related_args['cat'] );
		$competiscan_related = new WP_Query( $competiscan_related_args );
	}

	if ( $competiscan_related->have_posts() ) :
		?>
<!-- ============ RELATED INSIGHTS ============ -->
<section class="section related-insights pb-0">
  <div class="container">
    <h2 class="section-heading"><?php esc_html_e( 'Related Insights', 'competiscan-custom' ); ?></h2>
    <div class="articles-grid">
      <?php
      $competiscan_ri = 0;
      while ( $competiscan_related->have_posts() ) :
        $competiscan_related->the_post();
        $competiscan_ri++;
        $competiscan_thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
        if ( ! $competiscan_thumb ) {
          $competiscan_thumb = get_template_directory_uri() . '/assets/images/pic-' . ( ( ( $competiscan_ri - 1 ) % 9 ) + 1 ) . '.png';
        }
        $competiscan_rcats = get_the_category();
        $competiscan_rtag  = ! empty( $competiscan_rcats ) ? $competiscan_rcats[0]->name : __( 'Articles', 'competiscan-custom' );
        ?>
      <article class="article-card">
        <div class="article-thumb"><img src="<?php echo esc_url( $competiscan_thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"></div>
        <div class="article-body">
          <span class="tag"><?php echo esc_html( $competiscan_rtag ); ?></span>
          <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
          <a href="<?php the_permalink(); ?>" class="link-arrow">Read Now
            <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
          </a>
        </div>
      </article>
        <?php
      endwhile;
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============ NEWSLETTER (reused Simple Newsletter / Noptin) ============ -->
<section class="free-subscribe">
  <div class="container">
    <div class="newsletter">
      <div>
        <h2>Subscribe to our <br> free newsletter</h2>
        <p>Get the latest insights straight to your inbox every month</p>
      </div>
      <?php
      // Same Noptin form as the Insights archive — do not create a new form.
      echo do_shortcode( '[noptin fields="first_name,last_name,email" labels="hide" styles="none" template="normal" submit="Submit" first_name_placeholder="First name" last_name_placeholder="Last name" email_placeholder="Enter work email" html_class="newsletter-form cs-noptin-newsletter"]' ); // phpcs:ignore WordPress.Security.EscapeOutput
      ?>
    </div>
  </div>
</section>

<?php
endwhile;

get_footer();
