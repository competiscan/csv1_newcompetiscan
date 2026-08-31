<?php
/**
 * Default page template.
 *
 * @package Competiscan_Custom
 */

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/page-hero', null, array( 'title' => get_the_title() ) );
	?>
<section class="section">
  <div class="container">
    <div class="entry-content">
      <?php
      the_content();

      wp_link_pages(
        array(
          'before' => '<div class="page-links">',
          'after'  => '</div>',
        )
      );
      ?>
    </div>
  </div>
</section>
	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<section class="section"><div class="container">';
		comments_template();
		echo '</div></section>';
	}

endwhile;

get_footer();
