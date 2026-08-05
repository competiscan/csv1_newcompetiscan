<?php
/**
 * Login & Support page — flexible-content wiring.
 *
 * Registers ONE new layout (cs_login) for the client-login form, creates the
 * "Login" page on template-cms.php and seeds it. The form posts to WordPress'
 * native login handler (wp-login.php) so the Client Login is functional without
 * a custom PHP handler — it is an authentication form, not a contact form, so
 * Contact Form 7 (which would email credentials) is intentionally not used.
 * Every label, placeholder, button and link is editable from the admin.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_login_layouts( $field ) {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}
	$t = function ( $key, $name, $label, $type = 'text', $extra = array() ) {
		$base = array( 'key' => $key, 'label' => $label, 'name' => $name, '_name' => $name, 'type' => $type );
		if ( 'textarea' === $type ) {
			$base['new_lines'] = '';
		}
		return array_merge( $base, $extra );
	};

	$field['layouts']['layout_cs_login'] = array(
		'key'        => 'layout_cs_login',
		'name'       => 'cs_login',
		'label'      => '🔐 Login – Client Access Form',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_login_eyebrow', 'eyebrow', 'Eyebrow' ),
			$t( 'field_login_heading', 'heading', 'Heading' ),
			$t( 'field_login_email_label', 'email_label', 'Email — label' ),
			$t( 'field_login_email_ph', 'email_placeholder', 'Email — placeholder' ),
			$t( 'field_login_pass_label', 'password_label', 'Password — label' ),
			$t( 'field_login_pass_ph', 'password_placeholder', 'Password — placeholder' ),
			$t( 'field_login_remember', 'remember_label', 'Remember-me label' ),
			$t( 'field_login_forgot_l', 'forgot_label', 'Forgot-password link text' ),
			$t( 'field_login_forgot_u', 'forgot_url', 'Forgot-password URL (blank = WP reset)' ),
			$t( 'field_login_forgot_t', 'forgot_target', 'Forgot-password link target', 'select', array( 'choices' => array( '_self' => 'Same tab (_self)', '_blank' => 'New tab (_blank)' ), 'default_value' => '_self', 'ui' => 0, 'ajax' => 0, 'allow_null' => 0, 'multiple' => 0, 'return_format' => 'value', 'placeholder' => '' ) ),
			$t( 'field_login_forgot_r', 'forgot_rel', 'Forgot-password link rel (optional)' ),
			$t( 'field_login_submit', 'submit_label', 'Submit button label' ),
			$t( 'field_login_help', 'help_text', 'Support / help text (HTML allowed)', 'textarea', array( 'rows' => 2 ) ),
		),
	);
	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_register_login_layouts' );

/**
 * One-time: create the Login page (if missing), assign template-cms.php, seed it.
 */
function competiscan_bootstrap_login_page() {
	if ( get_option( 'competiscan_login_page_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$page = get_page_by_path( 'client-login' );
	if ( ! $page ) {
		$new_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Login',
				'post_name'   => 'client-login',
			)
		);
		if ( ! $new_id || is_wp_error( $new_id ) ) {
			return;
		}
		$id = $new_id;
	} else {
		$id = $page->ID;
	}

	update_post_meta( $id, '_wp_page_template', 'template-cms.php' );

	$ok = update_field( 'field_cs_flexible_content', array( array( 'acf_fc_layout' => 'cs_login' ) ), $id );

	if ( false !== $ok ) {
		update_option( 'competiscan_login_page_seeded', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_bootstrap_login_page', 30 );
