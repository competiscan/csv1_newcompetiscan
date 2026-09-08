<?php
/**
 * Route the existing "Insights" page to the static Insights template.
 *
 * The site's Insights page (ID 183, slug "insights") stores the page template
 * "template-flexible.php", which belongs to the *previous* theme and is shared by
 * all nine main pages. We deliberately do NOT change that stored value: under the
 * old theme it still drives the page's ACF-built content, and rewriting it would
 * break the live page. Instead, only while THIS theme is active, we map that one
 * page onto template-insights.php so it renders the static replica.
 *
 * Scope is intentionally narrow — only the Insights page is affected, never the
 * other eight pages that share template-flexible.php.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is the current main query the Insights page?
 *
 * Matches by slug first (portable), then by the known ID as a fallback.
 *
 * @return bool
 */
function competiscan_is_insights_page() {
	if ( ! is_page() ) {
		return false;
	}
	return is_page( 'insights' ) || is_page( 183 );
}

/**
 * Force the Insights page to use template-insights.php, whatever template the page
 * has stored, but only when it would otherwise fall through to a generic template.
 *
 * Runs late so it wins over the normal page-template resolution.
 *
 * @param string $template The template WordPress resolved.
 * @return string
 */
function competiscan_insights_template( $template ) {
	if ( ! competiscan_is_insights_page() ) {
		return $template;
	}

	$insights = locate_template( 'template-insights.php' );
	return $insights ? $insights : $template;
}
add_filter( 'template_include', 'competiscan_insights_template', 99 );
