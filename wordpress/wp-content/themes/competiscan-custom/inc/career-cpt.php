<?php
/**
 * Career custom post type + ACF fields for the Careers "Open roles" section.
 *
 * The Open Roles accordion is driven entirely by these posts, so job openings
 * can be added, edited, removed and reordered from the WP admin with no code
 * changes — the listing updates automatically. Ordering uses the numeric
 * "Display Order" field (ascending); the post title is the job title.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Career post type.
 */
function competiscan_register_career_cpt() {
	register_post_type(
		'career',
		array(
			'labels'       => array(
				'name'          => __( 'Careers', 'competiscan-custom' ),
				'singular_name' => __( 'Career', 'competiscan-custom' ),
				'menu_name'     => __( 'Careers', 'competiscan-custom' ),
				'add_new_item'  => __( 'Add Job Opening', 'competiscan-custom' ),
				'edit_item'     => __( 'Edit Job Opening', 'competiscan-custom' ),
				'all_items'     => __( 'All Job Openings', 'competiscan-custom' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_position'=> 25,
			'menu_icon'    => 'dashicons-businessperson',
			'has_archive'  => false,
			'rewrite'      => false,
			'supports'     => array( 'title', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'competiscan_register_career_cpt' );

/**
 * Register the ACF field group for Career openings.
 */
function competiscan_register_career_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_career',
			'title'    => 'Job Opening Details',
			'fields'   => array(
				array( 'key' => 'field_career_location', 'label' => 'Location', 'name' => 'career_location', 'type' => 'text', 'instructions' => 'e.g. "Chicago, IL · Hybrid (1–2 days in office)"' ),
				array( 'key' => 'field_career_department', 'label' => 'Department', 'name' => 'career_department', 'type' => 'text' ),
				array( 'key' => 'field_career_type', 'label' => 'Employment Type', 'name' => 'career_type', 'type' => 'text' ),
				array( 'key' => 'field_career_experience', 'label' => 'Experience', 'name' => 'career_experience', 'type' => 'text' ),
				array( 'key' => 'field_career_description', 'label' => 'Description', 'name' => 'career_description', 'type' => 'textarea', 'rows' => 4 ),
				array(
					'key' => 'field_career_duties', 'label' => 'Duties & Responsibilities', 'name' => 'career_duties', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add duty',
					'sub_fields' => array( array( 'key' => 'field_career_duty', 'label' => 'Item', 'name' => 'item', 'type' => 'text' ) ),
				),
				array(
					'key' => 'field_career_skills', 'label' => 'Skills & Experience', 'name' => 'career_skills', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add skill',
					'sub_fields' => array( array( 'key' => 'field_career_skill', 'label' => 'Item', 'name' => 'item', 'type' => 'text' ) ),
				),
				array( 'key' => 'field_career_apply_label', 'label' => 'Apply Button Text', 'name' => 'career_apply_label', 'type' => 'text', 'default_value' => 'Apply for this role' ),
				array( 'key' => 'field_career_apply_url', 'label' => 'Apply Button URL', 'name' => 'career_apply_url', 'type' => 'text', 'instructions' => 'Full URL or mailto: link. Leave blank to email contactus@competiscan.com.' ),
				array( 'key' => 'field_career_order', 'label' => 'Display Order', 'name' => 'career_order', 'type' => 'number', 'default_value' => 0 ),
			),
			'location' => array(
				array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'career' ) ),
			),
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_career_fields' );

/**
 * Fetch career openings ordered by Display Order (ascending).
 *
 * @return WP_Post[]
 */
function competiscan_get_careers() {
	return get_posts(
		array(
			'post_type'   => 'career',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_key'    => 'career_order',
			'orderby'     => array( 'meta_value_num' => 'ASC', 'menu_order' => 'ASC', 'date' => 'ASC' ),
		)
	);
}

/**
 * Seed the two openings from the source HTML (once).
 */
function competiscan_seed_careers() {
	if ( get_option( 'competiscan_careers_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	$existing = get_posts( array( 'post_type' => 'career', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( ! empty( $existing ) ) {
		update_option( 'competiscan_careers_seeded', '1' );
		return;
	}

	$loc  = 'Chicago, IL · Hybrid (1–2 days in office)';
	$desc = "Competiscan is a leading-edge competitive intelligence and market research company that gives clients real-time access to marketing and communication materials from their competitors. Our service-based model lets clients leverage our research team to access data, develop analysis, and present trends. We're seeking this role to join our financial services insights team.";

	$jobs = array(
		array(
			'title'  => 'Research Associate',
			'duties' => array(
				'Create and deliver impactful custom research reports that convey complex data clearly and concisely.',
				'Interpret qualitative and quantitative information to develop relevant insights and business implications.',
				'Research market trends using a variety of information services.',
				'Assess the reliability of research findings, maintaining high standards for accuracy, relevance, and quality.',
				'Manage multiple concurrent projects while meeting or exceeding client deadlines.',
			),
			'skills' => array(
				"Bachelor's degree (+) in Marketing, Market Research, Business Administration, Finance, or a related field preferred.",
				'Financial services experience preferred.',
				'Strong analytical and quantitative skills; excellent written and oral communication.',
				'Highly organized with strong attention to detail and problem-solving skills.',
				'Basic proficiency in Excel and PowerPoint (pivot tables, complex charts and graphs).',
			),
		),
		array(
			'title'  => 'Research Analyst',
			'duties' => array(
				'Create and deliver impactful custom research reports that convey complex data clearly and concisely.',
				'Interpret qualitative and quantitative information to develop relevant insights and business implications.',
				'Assess the reliability of research findings, maintaining high standards for accuracy and quality.',
				'Manage multiple concurrent projects while meeting or exceeding client deadlines.',
				'Formulate key takeaways and insights from research with strong analytical skills.',
				'Complete a high volume of ad-hoc and ongoing research requests, and compile content for trend and topical reports.',
				'Interact with clients on research requests and support client calls and presentations.',
			),
			'skills' => array(
				"Bachelor's degree (+) in Marketing, Market Research, Business Administration, Finance, or a related field preferred.",
				'Financial services experience required.',
				'Strong analytical and quantitative skills; excellent written and oral communication.',
				'Advanced knowledge of Excel and PowerPoint (pivot tables, complex charts and graphs).',
				'Familiarity with Power BI, Amazon QuickSight, and Power Automate is a plus.',
			),
		),
	);

	$order = 10;
	foreach ( $jobs as $job ) {
		$pid = wp_insert_post( array( 'post_type' => 'career', 'post_status' => 'publish', 'post_title' => $job['title'], 'menu_order' => $order ) );
		if ( ! $pid || is_wp_error( $pid ) ) {
			continue;
		}
		update_field( 'career_location', $loc, $pid );
		update_field( 'career_description', $desc, $pid );
		update_field( 'career_duties', array_map( function ( $d ) { return array( 'item' => $d ); }, $job['duties'] ), $pid );
		update_field( 'career_skills', array_map( function ( $s ) { return array( 'item' => $s ); }, $job['skills'] ), $pid );
		update_field( 'career_apply_label', 'Apply for this role', $pid );
		update_field( 'career_apply_url', 'mailto:contactus@competiscan.com?subject=Application: ' . $job['title'], $pid );
		update_field( 'career_order', $order, $pid );
		$order += 10;
	}

	update_option( 'competiscan_careers_seeded', '1' );
}
add_action( 'wp_loaded', 'competiscan_seed_careers', 26 );
