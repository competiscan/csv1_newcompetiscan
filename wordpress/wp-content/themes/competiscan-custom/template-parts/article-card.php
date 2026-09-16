<?php
/**
 * One article card, as used by the Insights grid, archives and search results.
 *
 * Expects to run inside the loop. The thumbnail is the post's WordPress Featured
 * Image (managed from the admin), falling back to the shared "No Image"
 * placeholder — see competiscan_article_thumbnail_url().
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thumb = competiscan_article_thumbnail_url();

// The pill label: first category name, matching the "Articles" tag in the source.
$terms = get_the_category();
$label = ! empty( $terms ) ? $terms[0]->name : __( 'Articles', 'competiscan-custom' );
$slug  = ! empty( $terms ) ? $terms[0]->slug : 'articles';
?>
<article class="article-card" data-type="<?php echo esc_attr( $slug ); ?>">
  <div class="article-thumb"><img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"></div>
  <div class="article-body">
    <span class="tag"><?php echo esc_html( $label ); ?></span>
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <a href="<?php the_permalink(); ?>" class="link-arrow">Read Now
      <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
    </a>
  </div>
</article>
