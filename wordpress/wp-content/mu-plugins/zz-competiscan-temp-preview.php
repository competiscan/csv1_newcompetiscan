<?php
/**
 * Plugin Name: TEMPORARY — Competiscan theme preview (read-only). DELETE ME.
 * Renders competiscan-custom only for requests carrying the token. Writes nothing.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'muplugins_loaded', function () {
	if ( ( $_GET['cs_preview'] ?? '' ) !== 'cs-preview-9f3a71c2' ) { return; }

	$swap = function () { return 'competiscan-custom'; };
	add_filter( 'pre_option_template', $swap );
	add_filter( 'pre_option_stylesheet', $swap );

	// Optionally force a specific template file so it can be rendered without
	// assigning it to a page in the database.
	add_filter( 'template_include', function ( $t ) {
		$want = $_GET['cs_tpl'] ?? '';
		if ( ! $want ) { return $t; }
		$file = get_template_directory() . '/' . basename( $want );
		return file_exists( $file ) ? $file : $t;
	}, 999 );
}, 1 );
