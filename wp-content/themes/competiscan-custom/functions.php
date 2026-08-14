<?php
/**
 * Competiscan Custom — theme functions.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COMPETISCAN_VERSION', '1.0.0' );

/**
 * Theme supports and menu locations.
 */
function competiscan_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary'          => __( 'Primary Navigation (header + mobile)', 'competiscan-custom' ),
			'footer_solutions' => __( 'Footer — Solutions column', 'competiscan-custom' ),
			'footer_company'   => __( 'Footer — Company column', 'competiscan-custom' ),
		)
	);
}
add_action( 'after_setup_theme', 'competiscan_setup' );

/**
 * Styles and scripts.
 *
 * Load order mirrors the original HTML exactly: Google Fonts, Slick CSS, style.css,
 * responsive.css in the head; jQuery, Slick JS, main.js at the end of the body.
 */
function competiscan_assets() {
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	// --- Styles -------------------------------------------------------------
	wp_enqueue_style(
		'competiscan-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'competiscan-slick',
		'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css',
		array(),
		'1.8.1'
	);

	wp_enqueue_style(
		'competiscan-style',
		$uri . '/assets/css/style.css',
		array( 'competiscan-fonts', 'competiscan-slick' ),
		competiscan_asset_version( $dir . '/assets/css/style.css' )
	);

	// responsive.css must always win over style.css, hence the explicit dependency.
	wp_enqueue_style(
		'competiscan-responsive',
		$uri . '/assets/css/responsive.css',
		array( 'competiscan-style' ),
		competiscan_asset_version( $dir . '/assets/css/responsive.css' )
	);

	// --- Scripts ------------------------------------------------------------
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script(
		'competiscan-slick',
		'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
		array( 'jquery' ),
		'1.8.1',
		true
	);

	/*
	 * main.js is the untouched original and calls jQuery as the bare `$` global.
	 * Core loads jQuery in noConflict mode, where `$` is undefined, so expose it
	 * the same way the standalone jQuery CDN build does before main.js runs.
	 * This mirrors the HTML build rather than editing main.js or deregistering
	 * core jQuery (which would risk breaking plugins).
	 */
	wp_add_inline_script( 'competiscan-slick', 'window.$ = window.$ || window.jQuery;', 'before' );

	wp_enqueue_script(
		'competiscan-main',
		$uri . '/assets/js/main.js',
		array( 'jquery', 'competiscan-slick' ),
		competiscan_asset_version( $dir . '/assets/js/main.js' ),
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'competiscan_assets' );

/**
 * Cache-bust on file mtime so edits show up without manual version bumps.
 *
 * @param string $path Absolute path to the asset.
 * @return string
 */
function competiscan_asset_version( $path ) {
	return file_exists( $path ) ? (string) filemtime( $path ) : COMPETISCAN_VERSION;
}

/**
 * The chevron used by the nav triggers, kept identical to the HTML source.
 *
 * @param string $context 'desktop' or 'mobile'.
 * @return string
 */
function competiscan_chevron_svg( $context = 'desktop' ) {
	if ( 'mobile' === $context ) {
		return '<svg width="14" height="9" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
	}

	return '<svg width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
}

/**
 * The right-pointing arrow used by buttons and "Read Now" links.
 *
 * @return string
 */
function competiscan_arrow_svg() {
	return '<svg width="14" height="10" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

require_once get_template_directory() . '/inc/class-competiscan-nav-walker.php';
require_once get_template_directory() . '/inc/class-competiscan-mobile-nav-walker.php';
require_once get_template_directory() . '/inc/nav-fallbacks.php';
require_once get_template_directory() . '/inc/nav-menu-bootstrap.php';
require_once get_template_directory() . '/inc/footer-options.php';
require_once get_template_directory() . '/inc/faq-fields.php';
require_once get_template_directory() . '/inc/home-fields.php';
require_once get_template_directory() . '/inc/pagination.php';
require_once get_template_directory() . '/inc/insights-page.php';
require_once get_template_directory() . '/inc/team-cpt.php';
require_once get_template_directory() . '/inc/about-fields.php';
require_once get_template_directory() . '/inc/about-bootstrap.php';
require_once get_template_directory() . '/inc/mid-fields.php';
require_once get_template_directory() . '/inc/contact-form.php';
require_once get_template_directory() . '/inc/toolkit-layouts.php';
require_once get_template_directory() . '/inc/career-cpt.php';
require_once get_template_directory() . '/inc/careers-layouts.php';
require_once get_template_directory() . '/inc/custom-research-layouts.php';
require_once get_template_directory() . '/inc/industries-layouts.php';
require_once get_template_directory() . '/inc/login-layouts.php';
require_once get_template_directory() . '/inc/vpt-layouts.php';

/**
 * Contact modal assets — loaded site-wide so the CF7 "Get In Touch" form is
 * reusable on every page. The CF7 plugin enqueues its own scripts/styles.
 */
function competiscan_contact_assets() {
	$uri = get_template_directory_uri();
	$dir = get_template_directory();
	wp_enqueue_style(
		'competiscan-contact',
		$uri . '/assets/css/contact.css',
		array(),
		competiscan_asset_version( $dir . '/assets/css/contact.css' )
	);
	wp_enqueue_script(
		'competiscan-contact',
		$uri . '/assets/js/contact.js',
		array(),
		competiscan_asset_version( $dir . '/assets/js/contact.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'competiscan_contact_assets', 20 );

/**
 * Templates (besides About) that use the shared cs-pages.css / cs-site.js port.
 *
 * @return string[]
 */
function competiscan_cs_page_templates() {
	return array(
		'template-mid.php',
		'template-cms.php',
	);
}

/**
 * Shared assets for the rebuilt inner pages — loaded only on those templates.
 */
function competiscan_cs_pages_assets() {
	if ( ! is_page_template( competiscan_cs_page_templates() ) ) {
		return;
	}
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	wp_enqueue_style(
		'competiscan-cs-pages',
		$uri . '/assets/css/cs-pages.css',
		array( 'competiscan-style', 'competiscan-responsive' ),
		competiscan_asset_version( $dir . '/assets/css/cs-pages.css' )
	);
	wp_enqueue_script(
		'competiscan-cs-site',
		$uri . '/assets/js/cs-site.js',
		array(),
		competiscan_asset_version( $dir . '/assets/js/cs-site.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'competiscan_cs_pages_assets', 20 );

/**
 * About page assets — loaded only on the About template so the ported HTML
 * styles/behaviour never affect the rest of the site.
 */
function competiscan_about_assets() {
	if ( ! is_page_template( 'template-about.php' ) ) {
		return;
	}
	$uri = get_template_directory_uri();
	$dir = get_template_directory();

	wp_enqueue_style(
		'competiscan-about',
		$uri . '/assets/css/about.css',
		array( 'competiscan-style', 'competiscan-responsive' ),
		competiscan_asset_version( $dir . '/assets/css/about.css' )
	);

	wp_enqueue_script(
		'competiscan-about',
		$uri . '/assets/js/about.js',
		array(),
		competiscan_asset_version( $dir . '/assets/js/about.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'competiscan_about_assets', 20 );

/**
 * Body classes: keep the markup clean and predictable.
 *
 * @param array $classes Body classes.
 * @return array
 */
function competiscan_body_class( $classes ) {
	if ( ! is_front_page() ) {
		$classes[] = 'inner-page';
	}
	return $classes;
}
add_filter( 'body_class', 'competiscan_body_class' );

/**
 * Allow the "NEW" badge markup authors type into a menu label to render.
 *
 * WordPress stores nav labels with HTML intact; this simply keeps our badge
 * span from being stripped when the label passes through the title filters.
 *
 * @param string $title Menu item title.
 * @return string
 */
function competiscan_allow_menu_badge( $title ) {
	return $title;
}
add_filter( 'nav_menu_item_title', 'competiscan_allow_menu_badge' );

/**
 * Excerpt tweaks used by the archive/search card markup.
 */
function competiscan_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'competiscan_excerpt_more' );

function my_login_logo() { ?>
<style type="text/css">
body.login {background: linear-gradient(135deg, #f4f9fc 0%, #e3f1f8 100%); font-family: Arial, sans-serif;}
#login {width: 420px;}
.login form {background: #fff; border: 0; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,75,129,.15); padding: 35px;}
#login h1 a,.login h1 a {    background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-primary-color.png');
    background-size: contain; background-repeat: no-repeat; background-position: center; width: 100%; height: 80px; margin-bottom: 25px;}
.wp-login-logo {background: transparent;}
.login label { color: #004b81; font-weight: 600;}
.login input[type="text"],.login input[type="password"],.login input[type="email"] { border: 1px solid #d4e3ee;border-radius: 8px; padding: 12px; font-size: 15px;
    box-shadow: none;}
.login input[type="text"]:focus,.login input[type="password"]:focus,.login input[type="email"]:focus {border-color: #004b81; box-shadow: 0 0 0 3px rgba(0,75,129,.15);}
.wp-core-ui .button-primary {background: #004b81 !important; border-color: #004b81 !important;  border-radius: 8px; box-shadow: none !important;
    text-shadow: none !important; padding: 4px 20px !important; transition: .3s;}
.wp-core-ui .button-primary:hover,.wp-core-ui .button-primary:focus {  background: #001e33 !important; border-color: #001e33 !important;
    color: #fff !important;  box-shadow: none !important;}
.login #nav a,.login #backtoblog a,.login .forgetmenot label {color: #004b81 !important;}
.login #nav a:hover,.login #backtoblog a:hover {color: #0063aa !important;}
input[type="checkbox"]:checked::before {color: #004b81;}
.language-switcher,.privacy-policy-page-link {display: none;}
</style>
<?php
}
add_action('login_enqueue_scripts', 'my_login_logo');

function custom_loginlogo_url() {
    return home_url();
}
add_filter('login_headerurl', 'custom_loginlogo_url');