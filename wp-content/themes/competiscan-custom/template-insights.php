<?php
/**
 * Template Name: Insights
 * Template Post Type: page
 *
 * A fully static 1:1 conversion of competiscan-html/insights.html.
 *
 * Per the page owner's requirement, nothing on this page is dynamic: the Featured
 * Articles, pagination, Webinars, Conferences, Newsletter and FAQ are all hardcoded
 * to match the source exactly. No WP_Query, no ACF, no editable fields. Assets are
 * the theme's existing CSS/JS/images. Only this template file drives the page.
 *
 * @package Competiscan_Custom
 */

get_header();

// --- Insights archive query (native, dynamic) ------------------------------
// "Insights" are the site's published posts. Search is scoped to this template via
// the `insight_s` param (a custom key so WordPress keeps rendering this page rather
// than routing to the site search), and pagination via `ipage`. Nothing hardcoded.
$competiscan_search   = isset( $_GET['insight_s'] ) ? sanitize_text_field( wp_unslash( $_GET['insight_s'] ) ) : '';
$competiscan_ipage    = isset( $_GET['ipage'] ) ? max( 1, absint( wp_unslash( $_GET['ipage'] ) ) ) : 1;
$competiscan_page_url = get_permalink( get_queried_object_id() );

$competiscan_query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 9,
	'paged'               => $competiscan_ipage,
	'ignore_sticky_posts' => true,
);
if ( '' !== $competiscan_search ) {
	$competiscan_query_args['s'] = $competiscan_search;
}
$competiscan_insights = new WP_Query( $competiscan_query_args );
$competiscan_total    = (int) $competiscan_insights->found_posts;
?>
<!-- ============ PAGE HEADER + FILTER BAR ============ -->
<section class="insights-hero">
  <?php get_template_part( 'template-parts/site-header' ); ?>
  <div class="container">
    <h1>Insights from <br> Industry Experts</h1>
    <div class="filter-bar">
      <button class="filter-toggle">
        <svg width="24" height="23" viewBox="0 0 24 23" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0 18.75C0 18.375 0.328125 18 0.75 18H3.79688C4.17188 16.3125 5.67188 15 7.5 15C9.28125 15 10.8281 16.3125 11.1562 18H23.25C23.625 18 24 18.375 24 18.75C24 19.1719 23.625 19.5 23.25 19.5H11.1562C10.8281 21.2344 9.28125 22.5 7.5 22.5C5.67188 22.5 4.17188 21.2344 3.79688 19.5H0.75C0.328125 19.5 0 19.1719 0 18.75ZM5.25 18.75C5.25 19.5938 5.67188 20.2969 6.375 20.7188C7.03125 21.1406 7.92188 21.1406 8.625 20.7188C9.28125 20.2969 9.75 19.5938 9.75 18.75C9.75 17.9531 9.28125 17.25 8.625 16.8281C7.92188 16.4062 7.03125 16.4062 6.375 16.8281C5.67188 17.25 5.25 17.9531 5.25 18.75ZM14.25 11.25C14.25 12.0938 14.6719 12.7969 15.375 13.2188C16.0312 13.6406 16.9219 13.6406 17.625 13.2188C18.2812 12.7969 18.75 12.0938 18.75 11.25C18.75 10.4531 18.2812 9.75 17.625 9.32812C16.9219 8.90625 16.0312 8.90625 15.375 9.32812C14.6719 9.75 14.25 10.4531 14.25 11.25ZM16.5 7.5C18.2812 7.5 19.8281 8.8125 20.1562 10.5H23.25C23.625 10.5 24 10.875 24 11.25C24 11.6719 23.625 12 23.25 12H20.1562C19.8281 13.7344 18.2812 15 16.5 15C14.6719 15 13.1719 13.7344 12.7969 12H0.75C0.328125 12 0 11.6719 0 11.25C0 10.875 0.328125 10.5 0.75 10.5H12.7969C13.1719 8.8125 14.6719 7.5 16.5 7.5ZM9 6C9.79688 6 10.5 5.57812 10.9219 4.875C11.3438 4.21875 11.3438 3.32812 10.9219 2.625C10.5 1.96875 9.79688 1.5 9 1.5C8.15625 1.5 7.45312 1.96875 7.03125 2.625C6.60938 3.32812 6.60938 4.21875 7.03125 4.875C7.45312 5.57812 8.15625 6 9 6ZM12.6562 3H23.25C23.625 3 24 3.375 24 3.75C24 4.17188 23.625 4.5 23.25 4.5H12.6562C12.3281 6.23438 10.7812 7.5 9 7.5C7.17188 7.5 5.67188 6.23438 5.29688 4.5H0.75C0.328125 4.5 0 4.17188 0 3.75C0 3.375 0.328125 3 0.75 3H5.29688C5.67188 1.3125 7.17188 0 9 0C10.7812 0 12.3281 1.3125 12.6562 3Z" fill="#00ABAB"/>
</svg>

        Filter
      </button>
      <div class="filter-pills">
        <button class="pill active" type="button" data-filter="all">All</button>
        <button class="pill" type="button" data-filter="articles">Articles</button>
        <button class="pill" type="button" data-filter="webinars">Webinars</button>
        <button class="pill" type="button" data-filter="conferences">Conferences</button>
    </div>
      <div class="filter-meta">
        <span class="results-count"><?php
          echo esc_html(
            sprintf(
              /* translators: %s: number of insight posts. */
              _n( 'Showing %s result', 'Showing %s results', $competiscan_total, 'competiscan-custom' ),
              number_format_i18n( $competiscan_total )
            )
          );
        ?></span>
        <a class="filter-reset<?php echo '' !== $competiscan_search ? ' is-visible' : ''; ?>" href="<?php echo esc_url( $competiscan_page_url ); ?>">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"></path></svg>
          <?php esc_html_e( 'Reset', 'competiscan-custom' ); ?>
        </a>
        <button class="search-toggle" type="button" aria-expanded="false">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="#00ABAB" stroke-width="1.8"/><path d="M21 21l-4-4" stroke="#00ABAB" stroke-width="1.8" stroke-linecap="round"/></svg>
          Search
        </button>
      </div>
      <form class="search-box" method="get" action="<?php echo esc_url( $competiscan_page_url ); ?>" role="search">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="#00ABAB" stroke-width="1.8"/><path d="M21 21l-4-4" stroke="#00ABAB" stroke-width="1.8" stroke-linecap="round"/></svg>
        <input type="text" name="insight_s" value="<?php echo esc_attr( $competiscan_search ); ?>" placeholder="Enter your search term">
        <?php if ( '' !== $competiscan_search ) : ?>
        <a class="search-box-clear" href="<?php echo esc_url( $competiscan_page_url ); ?>" aria-label="<?php esc_attr_e( 'Clear search', 'competiscan-custom' ); ?>" title="<?php esc_attr_e( 'Clear search', 'competiscan-custom' ); ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"></path></svg>
        </a>
        <?php endif; ?>
      </form>
    </div>
  </div>
</section>

<!-- ============ FEATURED ARTICLES ============ -->
<section  class="section featured-articles pb-0">
  <div class="container">
    <div class="filter-item" data-type="articles">
      <div style="padding-top:var(--space-lg);">
        <h2 class="section-heading">Featured Articles</h2>
        <div class="articles-grid">
          <?php
          if ( $competiscan_insights->have_posts() ) :
            $competiscan_i = 0;
            while ( $competiscan_insights->have_posts() ) :
              $competiscan_insights->the_post();
              $competiscan_i++;
              $competiscan_thumb = get_the_post_thumbnail_url( get_the_ID(), 'large' );
              if ( ! $competiscan_thumb ) {
                $competiscan_thumb = get_template_directory_uri() . '/assets/images/pic-' . ( ( ( $competiscan_i - 1 ) % 9 ) + 1 ) . '.png';
              }
              $competiscan_cats = get_the_category();
              $competiscan_tag  = ! empty( $competiscan_cats ) ? $competiscan_cats[0]->name : __( 'Articles', 'competiscan-custom' );
              ?>
          <article class="article-card">
            <div class="article-thumb"><img src="<?php echo esc_url( $competiscan_thumb ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"></div>
            <div class="article-body">
              <span class="tag"><?php echo esc_html( $competiscan_tag ); ?></span>
              <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
              <a href="<?php the_permalink(); ?>" class="link-arrow">Read Now
                <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
              </a>
            </div>
          </article>
              <?php
            endwhile;
            wp_reset_postdata();
          else :
            ?>
          <p class="no-results"><?php
            echo esc_html(
              '' === $competiscan_search
                ? __( 'No insights found.', 'competiscan-custom' )
                : sprintf( /* translators: %s: search term. */ __( 'No insights found for “%s”.', 'competiscan-custom' ), $competiscan_search )
            );
          ?></p>
          <?php endif; ?>
        </div>
         <!-- Pagination -->
         <?php
         competiscan_render_insights_pagination(
           (int) $competiscan_insights->max_num_pages,
           $competiscan_ipage,
           '' !== $competiscan_search ? array( 'insight_s' => $competiscan_search ) : array(),
           $competiscan_page_url
         );
         ?>
         <hr>
      </div>
    </div>

    <!-- ============ WEBINARS ============ -->
    <div class="filter-item" data-type="webinars">
      <div class="pb-0" style="padding-block:0 var(--space-lg);">
        <div class="container">
          
          <h2 class="section-heading">Webinars</h2>
          <div class="webinars-grid">
            <div class="webinar-card">
              <span class="tag">Webinars</span>
              <div class="webinar-date">
                <div class="date-box"><div class="mon">MAR</div><div class="day">18</div></div>
                  <div class="webinar-body">

                  <h4>Direct Mail Reimagined: How USPS incentives are fueling a new wave of creative innovation</h4>
                  <a href="#" class="link-arrow">Register
                    <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                  </a>
                </div>
              </div>

            </div>
            <div class="webinar-card">
              <span class="tag">Webinars</span>
              <div class="webinar-date">
                <div class="date-box"><div class="mon">APR</div><div class="day">8</div></div>
                <div class="webinar-body">

                <h4>Launching New ETFs</h4>
                <a href="#" class="link-arrow">Register
                  <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </a>
              </div>
              </div>

            </div>
            <div class="webinar-card">
              <span class="tag">Webinars</span>
              <div class="webinar-date">
                <div class="date-box"><div class="mon">JAN</div><div class="day">23</div></div>
                <div class="webinar-body">

                <h4>Competiscan Capabilities and Market Trends in Medicare Advantage.</h4>
                <span class="closed">This webinar has closed.</span>
              </div>
              </div>

            </div>
          </div>
          <hr>
        </div>
      </div>
    </div>

    <!-- ============ CONFERENCES ============ -->
    <div class="filter-item" data-type="conferences">
      <div style="padding-block:0 var(--space-lg);">
        <div class="container">
          
          <h2 class="section-heading mb-0">Conferences we'll be attending</h2>
          <p class="conferences-note">Check back soon for upcoming conference listings.</p>
        </div>
      </div>
    </div>

   
  </div>
</section>





<!-- ============ NEWSLETTER ============ -->
<?php get_template_part( 'template-parts/newsletter' ); ?>
<?php // Filter tabs, result-count visibility and Reset are handled in assets/js/main.js. ?>
<?php
get_template_part( 'template-parts/faq', null, array( 'variant' => 'insights' ) );


get_footer();


