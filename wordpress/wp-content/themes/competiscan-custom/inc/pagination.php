<?php
/**
 * Pagination in the markup the Insights design expects.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Arrow SVGs used by the pager, matching insights.html.
 *
 * @param string $dir 'prev' or 'next'.
 * @return string
 */
function competiscan_pager_arrow( $dir ) {
	if ( 'prev' === $dir ) {
		return '<svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0.355469 5.60547L5.60547 0.355469C5.93359 0 6.50781 0 6.83594 0.355469C7.19141 0.683594 7.19141 1.25781 6.83594 1.58594L2.21484 6.20703L6.83594 10.8555C7.19141 11.1836 7.19141 11.7578 6.83594 12.0859C6.50781 12.4414 5.93359 12.4414 5.60547 12.0859L0.355469 6.83594C0 6.50781 0 5.93359 0.355469 5.60547Z" fill="#00ABAB"/>
        </svg>';
	}

	return '<svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M6.83594 5.60547C7.19141 5.93359 7.19141 6.50781 6.83594 6.83594L1.58594 12.0859C1.25781 12.4414 0.683594 12.4414 0.355469 12.0859C0 11.7578 0 11.1836 0.355469 10.8555L4.97656 6.20703L0.355469 1.58594C0 1.25781 0 0.683594 0.355469 0.355469C0.683594 0 1.25781 0 1.58594 0.355469L6.83594 5.60547Z" fill="#00ABAB"/>
</svg>';
}

/**
 * The exact static pager from insights.html.
 *
 * Used when the Insights page has no real posts behind it, so a freshly activated
 * theme renders identically to the HTML build. The controls are <button>s here and
 * are decorative, exactly as in the source.
 */
function competiscan_static_pagination() {
	?>
    <div class="text-center">
      <nav class="pagination" aria-label="Articles pagination">
        <button class="page-btn nav-arrow" aria-label="Previous page">
          <?php echo competiscan_pager_arrow( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

        </button>
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn">4</button>
        <button class="page-btn">5</button>
        <span class="page-dots">…</span>
        <button class="page-btn">23</button>
        <button class="page-btn nav-arrow" aria-label="Next page">
          <?php echo competiscan_pager_arrow( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

        </button>
      </nav>
    </div>
	<?php
}

/**
 * Working pagination styled to match the design.
 *
 * The markup is built directly rather than by post-processing paginate_links(),
 * so the .page-btn / .active / .nav-arrow / .page-dots classes are assigned from
 * real state instead of by sniffing substrings out of the generated HTML.
 *
 * Deliberate deviation from the source: real controls are <a> elements rather than
 * the source's decorative <button>s, because they have to navigate. Classes are
 * unchanged, so the styling is identical.
 *
 * On page 1 of 23 this renders "‹ 1 2 3 4 5 … 23 ›", matching insights.html.
 *
 * @param WP_Query $query The query to paginate. Defaults to the main query.
 */
function competiscan_pagination( $query = null ) {
	if ( null === $query ) {
		global $wp_query;
		$query = $wp_query;
	}

	$total = (int) $query->max_num_pages;
	if ( $total < 2 ) {
		return;
	}

	$current = max( 1, (int) $query->get( 'paged' ) );
	$current = min( $current, $total );

	$pages = competiscan_pagination_range( $current, $total );

	echo '<div class="text-center"><nav class="pagination" aria-label="' . esc_attr__( 'Articles pagination', 'competiscan-custom' ) . '">';

	// Previous arrow.
	if ( $current > 1 ) {
		printf(
			'<a class="page-btn nav-arrow" href="%s" aria-label="%s">%s</a>',
			esc_url( get_pagenum_link( $current - 1 ) ),
			esc_attr__( 'Previous page', 'competiscan-custom' ),
			competiscan_pager_arrow( 'prev' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	foreach ( $pages as $page ) {
		if ( '...' === $page ) {
			echo '<span class="page-dots">&hellip;</span>';
			continue;
		}

		if ( (int) $page === $current ) {
			printf( '<span class="page-btn active" aria-current="page">%d</span>', (int) $page );
			continue;
		}

		printf(
			'<a class="page-btn" href="%s">%d</a>',
			esc_url( get_pagenum_link( (int) $page ) ),
			(int) $page
		);
	}

	// Next arrow.
	if ( $current < $total ) {
		printf(
			'<a class="page-btn nav-arrow" href="%s" aria-label="%s">%s</a>',
			esc_url( get_pagenum_link( $current + 1 ) ),
			esc_attr__( 'Next page', 'competiscan-custom' ),
			competiscan_pager_arrow( 'next' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	echo '</nav></div>';
}

/**
 * Working pagination for a secondary query on a page template (e.g. the Insights
 * archive), driven by an `ipage` query-string param so it never collides with the
 * page's own routing. Reuses the shared range/arrow helpers and the same
 * .page-btn / .active / .nav-arrow / .page-dots classes, so styling is identical.
 *
 * @param int    $total    Total number of pages.
 * @param int    $current  Current page (1-based).
 * @param array  $add_args Extra query args to preserve on every link (e.g. search).
 * @param string $base_url The page permalink to build links from.
 */
function competiscan_render_insights_pagination( $total, $current, $add_args = array(), $base_url = '' ) {
	$total = (int) $total;
	if ( $total < 2 ) {
		return;
	}

	$current  = min( max( 1, (int) $current ), $total );
	$base_url = $base_url ? $base_url : get_permalink();
	$pages    = competiscan_pagination_range( $current, $total );

	$link = static function ( $page ) use ( $add_args, $base_url ) {
		$args = $add_args;
		if ( (int) $page <= 1 ) {
			unset( $args['ipage'] );
		} else {
			$args['ipage'] = (int) $page;
		}
		$url = empty( $args ) ? $base_url : add_query_arg( $args, $base_url );
		return esc_url( $url );
	};

	echo '<div class="text-center"><nav class="pagination" aria-label="' . esc_attr__( 'Articles pagination', 'competiscan-custom' ) . '">';

	if ( $current > 1 ) {
		printf(
			'<a class="page-btn nav-arrow" href="%s" aria-label="%s">%s</a>',
			$link( $current - 1 ),
			esc_attr__( 'Previous page', 'competiscan-custom' ),
			competiscan_pager_arrow( 'prev' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	foreach ( $pages as $page ) {
		if ( '...' === $page ) {
			echo '<span class="page-dots">&hellip;</span>';
			continue;
		}

		if ( (int) $page === $current ) {
			printf( '<span class="page-btn active" aria-current="page">%d</span>', (int) $page );
			continue;
		}

		printf( '<a class="page-btn" href="%s">%d</a>', $link( $page ), (int) $page );
	}

	if ( $current < $total ) {
		printf(
			'<a class="page-btn nav-arrow" href="%s" aria-label="%s">%s</a>',
			$link( $current + 1 ),
			esc_attr__( 'Next page', 'competiscan-custom' ),
			competiscan_pager_arrow( 'next' ) // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}

	echo '</nav></div>';
}

/**
 * Which page numbers to show, with '...' marking gaps.
 *
 * Mirrors the source design: a leading run of five near the start, a trailing run
 * near the end, and a tight window around the current page in between.
 *
 * @param int $current Current page.
 * @param int $total   Total pages.
 * @return array Mixed list of ints and '...' markers.
 */
function competiscan_pagination_range( $current, $total ) {
	if ( $total <= 7 ) {
		return range( 1, $total );
	}

	if ( $current <= 4 ) {
		return array_merge( range( 1, 5 ), array( '...' ), array( $total ) );
	}

	if ( $current >= $total - 3 ) {
		return array_merge( array( 1 ), array( '...' ), range( $total - 4, $total ) );
	}

	return array_merge(
		array( 1 ),
		array( '...' ),
		range( $current - 1, $current + 1 ),
		array( '...' ),
		array( $total )
	);
}
