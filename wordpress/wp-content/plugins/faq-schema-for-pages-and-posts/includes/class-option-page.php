<?php
/**
 * Option Page file
 *
 * @package WpFaqSchema
 */

namespace WpFaqSchema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option Page class
 */
class Option_Page {
	/**
	 * Constructor Method for Option Page
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register' ] );
		add_action( 'wp_ajax_wfs_export', [ $this, 'export' ] );
		add_action( 'wp_ajax_wfs_import', [ $this, 'import' ] );
	}

	/**
	 * Register Option Page
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register() {
		add_options_page( __( 'FAQ Schema For Pages And Posts', 'wp-faq-schema' ), __( 'FAQ Schema For Pages And Posts', 'wp-faq-schema' ), 'manage_options', 'wp-faq-schema', [ $this, 'view' ] );
	}

	/**
	 * Option Page view
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function view() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'FAQ Schema For Pages And Posts Settings', 'wp-faq-schema' ); ?></h1>
			<h2><?php esc_html_e( 'How the plugin works step by step:', 'wp-faq-schema' ); ?></h2>
			<p><a href="https://www.onlinemarketinggurus.com.au/faq-schema-plugin" target="_blank"><?php esc_html_e( 'Learn how the plugin works and get support', 'wp-faq-schema' ); ?></a></p>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/pRhXbu24lsQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			<ol>
				<li><?php esc_html_e( 'Just to insert JSON-LD in a given page, go to that page/post and you’ll find new fields at the bottom the editor in the WP backend.', 'wp-faq-schema' ); ?></li>
				<li><?php esc_html_e( 'If you’d like to also display your Questions and Answers via a FAQ, use the shortcode. Title defines the heading above the FAQs. If you’d like to use an accordion, add accordion=1 as in the example. Remove that part not to use an accordion and just show all FAQs open (ie. [wp-faq-schema title="Car FAQs" accordion=1] )', 'wp-faq-schema' ); ?></li>
				<li><?php esc_html_e( 'If you’d like to mass edit, use the Export section to export all of the pages and posts with their ids - and then edit the file and import it back. Note: For multiple FAQs for same post add more row at CSV file with same ID and title.', 'wp-faq-schema' ); ?></li>
			</ol>

			<h2><?php esc_html_e( 'How to make FAQ display on the front end:', 'wp-faq-schema' ); ?></h2>
			<p><?php echo wp_kses( __( 'Add the shortcode inside of the page or post content to display the FAQ. The shortcode tag is <code>wp-faq-schema</code>. It will take <code>title</code> and <code>accordion</code> attributes.', 'wp-faq-schema' ), [ 'code' => [] ] ); ?></p>
			<p><strong><?php esc_html_e( 'Example below:', 'wp-faq-schema' ); ?></strong></p>
			<p><code>[wp-faq-schema]</code> - <?php esc_html_e( 'This code will only show FAQs of that page or post.', 'wp-faq-schema' ); ?></p>
			<p><code>[wp-faq-schema title="Car FAQs"]</code> - <?php esc_html_e( 'This code will show FAQs with "Car FAQs" title.', 'wp-faq-schema' ); ?></p>
			<p><code>[wp-faq-schema accordion=1]</code> - <?php esc_html_e( 'This code will show FAQs as accordion.', 'wp-faq-schema' ); ?></p>
			<p><code>[wp-faq-schema title="Car FAQs" accordion=1]</code> - <?php esc_html_e( 'This code will show FAQs as accordion with "Car FAQs" title.', 'wp-faq-schema' ); ?></p>

			<h2><?php esc_html_e( 'Export for editing on multiple pages', 'wp-faq-schema' ) ?></h2>
			<p><?php esc_html_e( 'Export a CSV with your posts and pages to edit the CSV with your FAQs and re-upload it back to import all of them automatically.', 'wp-faq-schema' ); ?></p>
			<form method="post">
				<input type="hidden" id="wfs-nonce" name="wfs_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wfs_nonce' ) ); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="wp-faq-schema-export"><?php esc_html_e( 'Export', 'wp-faq-schema' ); ?></label>
							</th>
							<td>
								<button class="button button-primary" type="button" id="wp-faq-schema-export"><?php esc_html_e( 'Export as .csv', 'wp-faq-schema' ); ?></button>
								<div class="wfs-export-message" id="export-message"></div>
							</td>
						</tr>
						<tr>
							<th>
								<label for="wp-faq-schema-import-file"><?php esc_html_e( 'Import', 'wp-faq-schema' ); ?></label>
							</th>
							<td>
								<input type="file" id="wp-faq-schema-import-file">
								<button class="button button-primary" type="button" id="wp-faq-schema-import"><?php esc_html_e( 'Import', 'wp-faq-schema' ); ?></button>
								<div class="wfs-export-message" id="import-message"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
			

		</div>
		<?php
	}

	/**
	 * Export FAQs
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function export() {
		if ( ! isset( $_POST['wfs_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wfs_nonce'] ), 'wfs_nonce' ) ) {
			wp_send_json_error( __( 'Unauthorized access!', 'wp-faq-schema' ) );
		}

		$posts = get_posts(
			[
				'post_type'      => [ 'post', 'page', 'avada_portfolio' ],
				'posts_per_page' => -1,
				'post_status'    => [ 'publish', 'pending', 'draft', 'private', 'future' ],
			]
		);

		$faqs = [];

		foreach ( $posts as $post ) {
			$faq_items = get_post_meta( $post->ID, '_wp_faq_schema', true );
			if ( ! empty( $faq_items ) ) {
				foreach ( $faq_items as $faq_item ) {
					$faqs[] = [
						$post->ID,
						$post->post_title,
						$faq_item['q'],
						$faq_item['a'],
					];
				}
			} else {
				$faqs[] = [
					$post->ID,
					$post->post_title,
					'',
					'',
				];
			}
		}

		$output = fopen( 'php://output', 'w' ) or wp_send_json_error( __( 'Unable to export as .csv file!', 'wp-faq-schema' ) );

		fputcsv( $output, [ 'id', 'title', 'question', 'answer' ] );

		foreach ( $faqs as $faq ) {
			fputcsv( $output, $faq );
		}

		fclose( $output );
		die();
	}

	/**
	 * Import FAQs
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function import() {
		if ( ! isset( $_POST['wfs_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wfs_nonce'] ), 'wfs_nonce' ) ) {
			wp_send_json_error( __( 'Unauthorized access!', 'wp-faq-schema' ) );
		}

		$items = ! empty( $_POST['items'] ) ? $_POST['items'] : [];

		if ( empty( $items ) || ! is_array( $_POST['items'] ) ) {
			wp_send_json_error( __( 'Empty csv file.', 'wp-faq-schema' ) );
		}

		$items_to_import = [];

		foreach ( $items as $item ) {
			if ( empty( $item[0] ) ) {
				continue;
			}
			$id = (int) $item[0];
			if ( empty( $items_to_import[ $id ] ) ) {
				$items_to_import[ $id ] = [
					'title' => $item[1],
					'faqs'  => [],
				];
			}
			$items_to_import[ $id ]['faqs'][] = [
				'q' => $item[2],
				'a' => $item[3],
			];
		}

		foreach ( $items_to_import as $post_id => $item_to_import ) {
			$this->import_faq( $post_id, $item_to_import );
		}

		wp_send_json_success( __( 'All FAQs has been imported. Enjoy!', 'wp-faq-schema' ) );
	}

	/**
	 * Import Single Page/Post FAQs
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param int   $post_id   Post ID.
	 * @param array $post_data Post Data to import.
	 * @return bool
	 */
	public function import_faq( $post_id, $post_data ) {
		$post = get_post( $post_id );
		if ( is_null( $post ) || empty( $post_data['faqs'] ) ) {
			return false;
		}

		return update_post_meta( $post_id, '_wp_faq_schema', $post_data['faqs'] );
	}
}
