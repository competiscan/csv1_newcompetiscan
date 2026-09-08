<?php
/**
 * One-time bootstrap for the About Us build.
 *
 * Runs once (guarded by an option) and, without any shell/WP-CLI access:
 *   1. copies the decorative white logomark into the theme,
 *   2. sideloads the nine leadership headshots into the Media Library,
 *   3. creates the Team posts with their ACF content,
 *   4. creates (or adopts) the "About Us" page and assigns the About template.
 *
 * Safe to leave in place: every step is skipped once done. Delete the option
 * 'competiscan_about_bootstrap' to let it run again.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Absolute path to the shipped HTML source assets (inside the WP install root).
 */
function competiscan_html_assets_dir() {
	return ABSPATH . 'competiscan-html/assets/';
}

/**
 * Sideload a local image file into the Media Library and return its attachment ID.
 *
 * @param string $abs_path Absolute path to the source image.
 * @param string $title    Attachment title.
 * @return int Attachment ID or 0 on failure.
 */
function competiscan_sideload_local_image( $abs_path, $title ) {
	if ( ! file_exists( $abs_path ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $abs_path );
	if ( ! $tmp ) {
		return 0;
	}
	copy( $abs_path, $tmp );
	$file_array = array(
		'name'     => basename( $abs_path ),
		'tmp_name' => $tmp,
	);
	$id = media_handle_sideload( $file_array, 0, $title );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}
	return (int) $id;
}

/**
 * The nine leadership members, in the source order.
 *
 * @return array
 */
function competiscan_team_seed_data() {
	return array(
		array( 'file' => 'rg2.jpg', 'name' => 'Richard Goldman', 'role' => 'CEO', 'li' => 'https://www.linkedin.com/in/richgoldmancompetiscan', 'bio' => 'Rich is the leading and longest-tenured expert in collecting competitor direct-marketing communications and materials. He has redefined how companies track competitors while gauging the impact of their own materials in the marketplaces they serve.' ),
		array( 'file' => 'jf2.jpg', 'name' => 'Jim Frisch', 'role' => 'EVP, Business Development', 'li' => 'https://www.linkedin.com/in/jim-frisch-009', 'bio' => "Jim's expertise lies in facilitating the acquisition and retention of clients while driving cultural initiatives across our staff. He has worked with direct-response leaders like Precision Dialogue and Fiserv across financial, retail, insurance, and hospitality." ),
		array( 'file' => 'bv2.jpg', 'name' => 'Bujeta Vokshi', 'role' => 'VP, People & Strategy', 'li' => 'https://www.linkedin.com/in/bujeta-vokshi-567a8a30', 'bio' => 'With 10+ years as a strategic business leader, Bujeta enhances employee performance while building organizational culture that supports thriving teams. She leads human-capital initiatives and strategy, partnering with the executive team to attract, hire, and retain first-class talent.' ),
		array( 'file' => 'jr2.jpg', 'name' => 'Joe Radtke', 'role' => 'VP, Custom Research', 'li' => 'https://www.linkedin.com/in/joeradtke', 'bio' => 'Joe has over 15 years of experience in strategic consulting and market research across financial-services segments. He works closely with Fortune 1000 companies on product benchmarking, go-to-market strategies, competitive profiles, and communication strategies.' ),
		array( 'file' => 'sh2.jpg', 'name' => 'Scott Hoffman', 'role' => 'AVP, Research & Insights, Insurance', 'li' => 'https://www.linkedin.com/in/scotthhoffman05', 'bio' => 'Scott offers more than eight years of market-research experience and manages Health Insurance and Worksite/Voluntary projects at Competiscan, working directly with clients to ensure impactful results.' ),
		array( 'file' => 'mc2-1.jpg', 'name' => 'Megan Cipperly', 'role' => 'VP, Client Services', 'li' => 'https://www.linkedin.com/in/megan-cipperly-39043667', 'bio' => 'Megan has 10+ years researching direct and digital marketing trends. She draws insights from the activity of marketers reaching thousands of consumers across the U.S., leading a team of analysts reporting on trends across financial services and insurance.' ),
		array( 'file' => 'nh2.jpg', 'name' => 'Nate Hart', 'role' => 'SVP, Operations', 'li' => '', 'bio' => "Nate enjoys solving Competiscan's business challenges. He has designed and built multiple products while establishing time- and money-saving processes, and leads a large team driving growth through IT initiatives." ),
		array( 'file' => 'jd2.jpg', 'name' => 'Jessica Duncan', 'role' => 'AVP, Research & Insights, Financial Services', 'li' => 'https://www.linkedin.com/in/jessica-duncan-a372b04/', 'bio' => "With 15+ years in financial services and consumer marketing, Jessica brings deep expertise in marketing strategy and analytics. She previously led a team of Senior Marketing Analysts at PSCU's marketing and consulting division." ),
		array( 'file' => 'mr2.jpg', 'name' => 'Michael Ruffing', 'role' => 'Senior Sales Director', 'li' => 'https://www.linkedin.com/in/michaelruffing/', 'bio' => 'Michael is driven by a passion for market research and helping organizations revolutionize their marketing and product development. As Senior Sales Director he drives new-business growth and strategic partnerships across all industries.' ),
	);
}

/**
 * Run the one-time bootstrap.
 */
function competiscan_run_about_bootstrap() {
	if ( get_option( 'competiscan_about_bootstrap' ) ) {
		return;
	}
	// Needs ACF for update_field and only makes sense in a normal request.
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	// 1. Copy the decorative white logomark into the theme.
	$logo_src = competiscan_html_assets_dir() . 'logos/COM-logomark-white.png';
	$logo_dst = get_template_directory() . '/assets/images/logomark-white.png';
	if ( file_exists( $logo_src ) && ! file_exists( $logo_dst ) ) {
		@copy( $logo_src, $logo_dst ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	// 2 + 3. Seed team members (only if none exist yet).
	$existing_team = get_posts(
		array(
			'post_type'   => 'team',
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( empty( $existing_team ) ) {
		$order = 10;
		foreach ( competiscan_team_seed_data() as $member ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => 'team',
					'post_status' => 'publish',
					'post_title'  => $member['name'],
					'menu_order'  => $order,
				)
			);
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				continue;
			}
			$att_id = competiscan_sideload_local_image(
				competiscan_html_assets_dir() . 'team/' . $member['file'],
				$member['name']
			);
			if ( $att_id ) {
				update_field( 'team_photo', $att_id, $post_id );
			}
			update_field( 'team_full_name', $member['name'], $post_id );
			update_field( 'team_designation', $member['role'], $post_id );
			update_field( 'team_bio', $member['bio'], $post_id );
			update_field( 'team_linkedin', $member['li'], $post_id );
			update_field( 'team_order', $order, $post_id );
			$order += 10;
		}
	}

	// 4. Create or adopt the About Us page and assign the template.
	$about = get_page_by_path( 'about-us' );
	if ( ! $about ) {
		$about = get_page_by_title( 'About Us' );
	}
	if ( ! $about ) {
		$about_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'About Us',
				'post_name'   => 'about-us',
			)
		);
	} else {
		$about_id = $about->ID;
	}
	if ( $about_id && ! is_wp_error( $about_id ) ) {
		update_post_meta( $about_id, '_wp_page_template', 'template-about.php' );
	}

	update_option( 'competiscan_about_bootstrap', '1' );
}
add_action( 'wp_loaded', 'competiscan_run_about_bootstrap', 20 );

/**
 * Copy the original Inter woff2 files into the theme so the About page can
 * self-host the exact same font as the source HTML (identical metrics, and it
 * works without network access). Runs once.
 */
function competiscan_copy_inter_fonts() {
	if ( get_option( 'competiscan_inter_fonts' ) ) {
		return;
	}
	$src_dir = competiscan_html_assets_dir() . 'fonts/';
	$dst_dir = get_template_directory() . '/assets/fonts/';
	if ( ! is_dir( $src_dir ) ) {
		return;
	}
	if ( ! is_dir( $dst_dir ) ) {
		wp_mkdir_p( $dst_dir );
	}
	$fonts = array(
		'inter-705d6250.woff2',
		'inter-9f73b2f4.woff2',
		'inter-04fe624f.woff2',
		'inter-c68aff86.woff2',
		'inter-9c1cad86.woff2',
		'inter-cf3eb50f.woff2',
		'inter-f11d729b.woff2',
	);
	$copied = 0;
	foreach ( $fonts as $f ) {
		if ( file_exists( $src_dir . $f ) && ! file_exists( $dst_dir . $f ) ) {
			if ( @copy( $src_dir . $f, $dst_dir . $f ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$copied++;
			}
		} elseif ( file_exists( $dst_dir . $f ) ) {
			$copied++;
		}
	}
	if ( count( $fonts ) === $copied ) {
		update_option( 'competiscan_inter_fonts', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_copy_inter_fonts', 21 );
