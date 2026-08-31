<?php
/**
 * Static navigation fallbacks.
 *
 * These render the exact markup from the HTML source and are used whenever a menu
 * location has no menu assigned. That means a freshly activated theme is already
 * pixel-identical to the HTML build; assigning menus in Appearance → Menus then
 * takes over through the walkers without any markup change.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of the Insights page.
 *
 * Resolves to whichever page uses the Insights template, falling back to /insights/
 * so the link is never dead on a fresh install.
 *
 * @return string
 */
function competiscan_insights_url() {
	$cached = wp_cache_get( 'competiscan_insights_url' );
	if ( false !== $cached ) {
		return $cached;
	}

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'template-insights.php',
		)
	);

	$url = $pages ? get_permalink( $pages[0] ) : home_url( '/insights/' );
	wp_cache_set( 'competiscan_insights_url', $url );

	return $url;
}

/**
 * Desktop nav fallback — mirrors index.html.
 */
function competiscan_primary_nav_fallback() {
	$insights = esc_url( competiscan_insights_url() );
	$chevron  = competiscan_chevron_svg( 'desktop' );
	?>
	<nav class="main-nav">
		<div class="nav-item has-dropdown">
			<a href="#" class="nav-link">Solutions
				<?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<div class="mega-drop">
				<a href="#" class="mega-drop-link">
					<div class="title">Market Intelligence Database</div>
					<p>Lorem ipsum dolor sit amet consectetur adipiscing elit himenaeos sed.</p>
				</a>
				<a href="#" class="mega-drop-link">
					<div class="title">AI Toolkit <span class="badge-new">NEW</span></div>
					<p>Lorem ipsum dolor sit amet consectetur adipiscing elit himenaeos sed.</p>
				</a>
				<a href="#" class="mega-drop-link">
					<div class="title">Value Proposition Trackers</div>
					<p>Lorem ipsum dolor sit amet consectetur adipiscing elit himenaeos sed.</p>
				</a>
				<a href="#" class="mega-drop-link">
					<div class="title">Custom Research &amp; Analysis</div>
					<p>Lorem ipsum dolor sit amet consectetur adipiscing elit himenaeos sed.</p>
				</a>
			</div>
		</div>
		<a href="#" class="nav-link">Industries</a>
		<a href="<?php echo $insights; ?>" class="nav-link">Insights</a>
		<div class="nav-item has-dropdown">
			<a href="#" class="nav-link">About Us
				<?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<div class="mega-drop" style="grid-template-columns:1fr;width:16rem;">
				<a href="#" class="mega-drop-link"><div class="title">Our Story</div></a>
				<a href="#" class="mega-drop-link"><div class="title">Careers</div></a>
			</div>
		</div>
	</nav>
	<?php
}

/**
 * Mobile nav fallback — mirrors index.html.
 */
function competiscan_mobile_nav_fallback() {
	$insights = esc_url( competiscan_insights_url() );
	$chevron  = competiscan_chevron_svg( 'mobile' );
	?>
	<ul class="mobile-nav-list">
		<li class="mobile-nav-item has-sub">
			<a href="#" class="mobile-nav-link">Solutions
				<?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<ul class="mobile-submenu">
				<li><a href="#">Market Intelligence Database</a></li>
				<li><a href="#">AI Toolkit <span class="badge-new">NEW</span></a></li>
				<li><a href="#">Value Proposition Trackers</a></li>
				<li><a href="#">Custom Research &amp; Analysis</a></li>
			</ul>
		</li>
		<li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Industries</a></li>
		<li class="mobile-nav-item"><a href="<?php echo $insights; ?>" class="mobile-nav-link">Insights</a></li>
		<li class="mobile-nav-item has-sub">
			<a href="#" class="mobile-nav-link">About Us
				<?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<ul class="mobile-submenu">
				<li><a href="#">Our Story</a></li>
				<li><a href="#">Careers</a></li>
			</ul>
		</li>
	</ul>
	<?php
}

/**
 * Footer "Solutions" column fallback.
 */
function competiscan_footer_solutions_fallback() {
	?>
	<ul>
		<li><a href="#">Database</a></li>
		<li><a href="#">AI Toolkit <span class="badge-new">NEW</span></a></li>
		<li><a href="#">Tracker</a></li>
		<li><a href="#">Custom</a></li>
	</ul>
	<?php
}

/**
 * Footer "Company" column fallback.
 */
function competiscan_footer_company_fallback() {
	?>
	<ul>
		<li><a href="#">About Us</a></li>
		<li><a href="<?php echo esc_url( competiscan_insights_url() ); ?>">Insights</a></li>
		<li><a href="#">Client Login</a></li>
		<li><a href="#">Contact Us</a></li>
	</ul>
	<?php
}
