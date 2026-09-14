<?php
/**
 * Plugin Name: TEMPORARY — Competiscan theme-switch logger. DELETE ME.
 * Description: Passive. Records who/what changes the active theme option so the
 *              recurring switch to competiscan-custom can be traced to its source.
 *              Blocks nothing and changes nothing. Delete once diagnosed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( array( 'template', 'stylesheet' ) as $cs_opt ) {
	add_action(
		"update_option_{$cs_opt}",
		function ( $old, $new ) use ( $cs_opt ) {
			if ( $old === $new ) {
				return;
			}
			$uid  = function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0;
			$bt   = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 25 );
			$body = "\n### " . gmdate( 'c' ) . "  update_option_{$cs_opt}: '{$old}' -> '{$new}'\n"
				. '  URI=' . ( $_SERVER['REQUEST_URI'] ?? 'cli' )
				. '  user=' . $uid
				. '  ip=' . ( $_SERVER['REMOTE_ADDR'] ?? '-' )
				. '  ua=' . ( $_SERVER['HTTP_USER_AGENT'] ?? '-' ) . "\n";
			foreach ( $bt as $f ) {
				$body .= '    ' . ( isset( $f['file'] ) ? str_replace( ABSPATH, '', $f['file'] ) . ':' . $f['line'] : '?' )
					. ' ' . ( $f['function'] ?? '' ) . "()\n";
			}
			@file_put_contents( WP_CONTENT_DIR . '/cs-theme-switch.log', $body, FILE_APPEND );
		},
		10,
		2
	);
}
