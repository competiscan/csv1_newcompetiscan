<?php
/**
 * Team custom post type + ACF fields for the About Us leadership grid.
 *
 * The About page's "Meet the leadership team" section is driven entirely by these
 * posts, so team members can be added, edited, removed and reordered from the WP
 * admin with no code changes. Ordering uses the numeric "Display Order" field
 * (ascending); the post title is the member's name.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Team post type.
 */
function competiscan_register_team_cpt() {
	$labels = array(
		'name'               => __( 'Team', 'competiscan-custom' ),
		'singular_name'      => __( 'Team Member', 'competiscan-custom' ),
		'menu_name'          => __( 'Team', 'competiscan-custom' ),
		'add_new'            => __( 'Add Member', 'competiscan-custom' ),
		'add_new_item'       => __( 'Add Team Member', 'competiscan-custom' ),
		'edit_item'          => __( 'Edit Team Member', 'competiscan-custom' ),
		'new_item'           => __( 'New Team Member', 'competiscan-custom' ),
		'view_item'          => __( 'View Team Member', 'competiscan-custom' ),
		'search_items'       => __( 'Search Team', 'competiscan-custom' ),
		'not_found'          => __( 'No team members found', 'competiscan-custom' ),
		'not_found_in_trash' => __( 'No team members found in Trash', 'competiscan-custom' ),
		'all_items'          => __( 'All Team Members', 'competiscan-custom' ),
	);

	register_post_type(
		'team',
		array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_position'   => 24,
			'menu_icon'       => 'dashicons-groups',
			'hierarchical'    => false,
			'has_archive'     => false,
			'rewrite'         => false,
			'query_var'       => false,
			'supports'        => array( 'title', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'competiscan_register_team_cpt' );

/**
 * Register the ACF field group for Team members.
 *
 * Registered in PHP so the fields exist as soon as the theme is active — no
 * manual field creation and nothing to sync. Every layout/style property of the
 * card is in CSS; these fields hold content only.
 */
function competiscan_register_team_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_competiscan_team',
			'title'                 => 'Team Member Details',
			'fields'                => array(
				array(
					'key'           => 'field_team_photo',
					'label'         => 'Team Photo',
					'name'          => 'team_photo',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'instructions'  => 'Square headshot. Displayed at a 1:1 aspect ratio, focused near the top of the image.',
				),
				array(
					'key'          => 'field_team_full_name',
					'label'        => 'Name',
					'name'         => 'team_full_name',
					'type'         => 'text',
					'instructions' => 'Optional. Leave blank to use the title above as the name.',
				),
				array(
					'key'          => 'field_team_designation',
					'label'        => 'Designation',
					'name'         => 'team_designation',
					'type'         => 'text',
					'instructions' => 'Job title, e.g. "VP, Client Services".',
				),
				array(
					'key'          => 'field_team_bio',
					'label'        => 'Short Description',
					'name'         => 'team_bio',
					'type'         => 'textarea',
					'rows'         => 4,
					'new_lines'    => '',
				),
				array(
					'key'          => 'field_team_linkedin',
					'label'        => 'LinkedIn URL',
					'name'         => 'team_linkedin',
					'type'         => 'url',
					'instructions' => 'Optional. When set, a LinkedIn link shows on the card.',
				),
				array(
					'key'           => 'field_team_order',
					'label'         => 'Display Order',
					'name'          => 'team_order',
					'type'          => 'number',
					'default_value' => 0,
					'instructions'  => 'Lower numbers appear first.',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'team',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'active'                => true,
			'show_in_rest'          => 1,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_team_fields' );

/**
 * Fetch team members ordered by Display Order (ascending), then menu order.
 *
 * @return WP_Post[]
 */
function competiscan_get_team_members() {
	return get_posts(
		array(
			'post_type'      => 'team',
			'post_status'    => 'publish',
			'numberposts'    => -1,
			'meta_key'       => 'team_order',
			'orderby'        => array(
				'meta_value_num' => 'ASC',
				'menu_order'     => 'ASC',
				'date'           => 'ASC',
			),
		)
	);
}
