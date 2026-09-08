<?php
/**
 * One article card, as used by the Insights grid, archives and search results.
 *
 * Expects to run inside the loop. Falls back to pic-1.png when a post has no
 * featured image so the grid never renders a hole.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$thumb = has_post_thumbnail()
	? get_the_post_thumbnail_url( get_the_ID(), 'large' )
	: get_template_directory_uri() . '/assets/images/pic-1.png';

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
