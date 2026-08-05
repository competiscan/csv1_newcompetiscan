<?php
/**
 * Plugin Name: FAQ Schema For Pages And Posts
 * Plugin URI: https://www.onlinemarketinggurus.com.au/faq-schema-plugin
 * Description: To get started with FAQ Schema go to one of your pages or posts and at the bottom you'll find new fields for adding FAQ Schema. Simply save the page to see them come through in the code in the HEAD.
 * Author: Krystian Szastok (SEO Consultant)
 * Version: 2.3.0
 * Author URI: https://krystianszastok.co.uk/
 * Text Domain: wp-faq-schema
 * Domain Path: /languages/
 * License: GPLv2 or later
 *
 * @package WpFaqSchema
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_FAQ_SCHEMA_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_FAQ_SCHEMA_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', 'wp_faq_schema_load_plugin_textdomain' );

/**
 * Load WP FAQ Schema textdomain.
 *
 * Load gettext translate for WP FAQ Schema text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function wp_faq_schema_load_plugin_textdomain() {
	load_plugin_textdomain( 'wp-faq-schema', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// Require the main Plugin class.
require WP_FAQ_SCHEMA_PATH . 'includes/class-plugin.php';
