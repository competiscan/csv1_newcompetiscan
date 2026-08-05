<?php
/**
 * Metabox file
 *
 * @package WpFaqSchema
 */

namespace WpFaqSchema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Metabox class
 */
class Metabox {
	/**
	 * Constructor Method for Metabox
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'metabox_init' ] );
		add_action( 'save_post', [ $this, 'save' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );

		add_action( 'init', [ $this, 'taxonomy_meta' ], 999 );

	}

	/**
	 * Initialize metabox
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function metabox_init() {
		$post_types = get_post_types( [
			'public' => true,
		] );
		unset( $post_types['attachment'] );
		unset( $post_types['elementor_library'] );
		/* translators: icon */
		add_meta_box( 'wp-faq-schema', sprintf( __( 'FAQ Schema For Pages And Posts %s', 'wp-faq-schema' ), ' <span class="dashicons dashicons-info" title="' . __( 'To save just update your page, this text saves automatically on page update.', 'wp-faq-schema' ) . '"></span>' ), [ $this, 'view' ], array_keys( $post_types ) );
	}

	/**
	 * Metabox view
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function view( $post ) {
		$faqs = get_post_meta( $post->ID, '_wp_faq_schema', true );

		if ( empty( $faqs ) ) {
			$faqs = [
				[
					'q' => '',
					'a' => '',
				],
			];
		}

		?>
		<div class="wp-faq-schema-container">
			<input type="hidden" name="wp-faq-schema-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-faq-schema' ) ); ?>">
			<div class="wp-faq-schema-items">
				<?php foreach ( $faqs as $index => $faq ) : ?>
					<div class="wp-faq-schema-item">
						<div class="wp-faq-schema-header">
							<span>
								<?php
									/* translators: index */
									echo esc_html( ! empty( $faq['q'] ) ? $faq['q'] : sprintf( __( 'Question #%s', 'wp-faq-schema' ), $index + 1 ) );
								?>
							</span>
							<button class="wp-faq-schema-delete" type="button" data-confirm="<?php esc_html_e( 'Are you sure you want to delete this question and answer?', 'wp-faq-schema' ); ?>"><span class="dashicons dashicons-trash"></span></button>
						</div>
						<div class="wp-faq-schema">
							<p>
								<label for="wp-faq-schema-q-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Question', 'wp-faq-schema' ); ?></label>
								<input type="text" id="wp-faq-schema-q-<?php echo esc_attr( $index ); ?>" name="wp-faq-schema[<?php echo esc_attr( $index ); ?>][q]" class="widefat" value="<?php echo esc_attr( ! empty( $faq['q'] ) ? $faq['q'] : '' ); ?>">
							</p>
							<p>
								<label for="wp-faq-schema-a-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Answer', 'wp-faq-schema' ); ?></label>
								<textarea name="wp-faq-schema[<?php echo esc_attr( $index ); ?>][a]" id="wp-faq-schema-a-<?php echo esc_attr( $index ); ?>" rows="3" class="widefat"><?php echo esc_html( ! empty( $faq['a'] ) ? $faq['a'] : '' ); ?></textarea>
							</p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php /* translators: index */ ?>
			<button type="button" class="button button-primary wp-faq-schema-add" data-index="<?php echo count( $faqs ); ?>" data-title="<?php esc_attr_e( 'Question #%s', 'wp-faq-schema' ); ?>">
				<?php echo esc_html_e( 'Add Another Question', 'wp-faq-schema' ); ?>
			</button>
		</div>
		<script type="text/html" id="tmpl-faq-schema-form">
			<div class="wp-faq-schema-item">
				<div class="wp-faq-schema-header">
					<span>{TITLE}</span>
					<button class="wp-faq-schema-delete" type="button" data-confirm="<?php esc_attr_e( 'Are you sure? You want to delete this.', 'wp-faq-schema' ); ?>"><span class="dashicons dashicons-trash"></span></button>
				</div>
				<div class="wp-faq-schema">
					<p>
						<label for="wp-faq-schema-q-{INDEX}"><?php esc_html_e( 'Question', 'wp-faq-schema' ); ?></label>
						<input type="text" id="wp-faq-schema-q-{INDEX}" name="wp-faq-schema[{INDEX}][q]" class="widefat">
					</p>
					<p>
						<label for="wp-faq-schema-a-{INDEX}"><?php esc_html_e( 'Answer', 'wp-faq-schema' ); ?></label>
						<textarea id="wp-faq-schema-a-{INDEX}" name="wp-faq-schema[{INDEX}][a]" rows="3" class="widefat"></textarea>
					</p>
				</div>
			</div>
		</script>
		<?php
	}

	/**
	 * Save meta values
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! isset( $_POST['wp-faq-schema-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wp-faq-schema-nonce'] ), 'wp-faq-schema' ) ) {
			return;
		}

		update_post_meta( $post_id, '_wp_faq_schema', ! isset( $_POST['wp-faq-schema'] ) ? [] : $this->sanitize( $_POST['wp-faq-schema'] ) ); // phpcs:ignore
	}

	/**
	 * Metabox assets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return  void
	 */
	public function assets() {
		wp_enqueue_style( 'wp-faq-schema-style', WP_FAQ_SCHEMA_URL . '/css/wp-faq-schema.css', false, '1.0.0' );

		wp_enqueue_script( 'sweetalert2', WP_FAQ_SCHEMA_URL . '/js/sweetalert2.all.min.js', [ 'jquery' ], '1.0.0', true );
		wp_enqueue_script( 'papaparse', WP_FAQ_SCHEMA_URL . '/js/papaparse.min.js', [ 'jquery' ], '1.0.0', true );
		wp_enqueue_script( 'wp-faq-schema-scripts', WP_FAQ_SCHEMA_URL . '/js/wp-faq-schema.js', [ 'jquery', 'papaparse', 'sweetalert2' ], '1.0.0', true );

		$data = 'var wfs_ajax="' . admin_url( 'admin-ajax.php' ) . '";';

		wp_add_inline_script( 'wp-faq-schema-scripts', $data, 'before' );
	}

	/**
	 * Sanitize full array
	 *
	 * @param array $faqs Array.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return  array
	 */
	public function sanitize( $faqs ) {
		$new_faqs = [];

		if ( ! empty( $faqs ) && is_array( $faqs ) ) {
			foreach ( $faqs as $faq ) {
				$new_faq = [];

				if ( ! empty( $faq['q'] ) ) {
					$new_faq['q'] = sanitize_text_field( $faq['q'] );
				}
				if ( ! empty( $faq['a'] ) ) {
					$new_faq['a'] = wp_kses_post( $faq['a'] );
				}

				if ( ! empty( $new_faq ) ) {
					$new_faqs[] = $new_faq;
				}
			}
		}

		return $new_faqs;
	}

	public function taxonomy_meta() {
		$taxonomies = get_taxonomies( ['publicly_queryable' => true] );
		$taxonomies = array_keys( $taxonomies );

		foreach( $taxonomies as $taxonomy ) {
			add_filter( $taxonomy . '_add_form_fields', array( $this, 'tax_meta' ) );
			add_filter( $taxonomy . '_edit_form_fields', array( $this, 'tax_meta_edit' ) );

			add_action( 'edit_' . $taxonomy, array( $this, 'save_tax_meta' ) );
			add_action( 'create_' . $taxonomy, array( $this, 'save_tax_meta' ) );
		}

	}

	public function tax_meta() {
		?>
		<div class="form-field is-tax">
			<?php $this->tax_meta_label(); ?>
			<?php $this->tax_meta_view( false ); ?>
		</div>
		<?php
	}
	public function tax_meta_edit( $term ) {
		?>
		<tr class="form-field store-logo-wrap">
			<th scope="row">
				<?php $this->tax_meta_label(); ?>
			</th>
			<td>
				<div class="is-tax">
					<?php $this->tax_meta_view( $term->term_id ); ?>
				</div>
			</td>
		</tr>
		<?php
	}

	public function tax_meta_label() {
		?>
		<label class="is-tax-label"><?php printf( __( 'FAQ Schema For Pages And Posts %s', 'wp-faq-schema' ), ' <span class="dashicons dashicons-info" title="' . __( 'To save just update your page, this text saves automatically on page update.', 'wp-faq-schema' ) . '"></span>' ) ?></label>
		<?php
	}

	public function tax_meta_view( $term_id ) {
		$faqs = $term_id ? get_term_meta( $term_id, '_wp_faq_schema', true ) : '';

		if ( empty( $faqs ) ) {
			$faqs = [
				[
					'q' => '',
					'a' => '',
				],
			];
		}
		?>
		<div class="wp-faq-schema-container">
			<input type="hidden" name="wp-faq-schema-nonce" value="<?php echo esc_attr( wp_create_nonce( 'wp-faq-schema' ) ); ?>">
			<div class="wp-faq-schema-items">
				<?php foreach ( $faqs as $index => $faq ) : ?>
					<div class="wp-faq-schema-item">
						<div class="wp-faq-schema-header">
							<span>
								<?php
									/* translators: index */
									echo esc_html( ! empty( $faq['q'] ) ? $faq['q'] : sprintf( __( 'Question #%s', 'wp-faq-schema' ), $index + 1 ) );
								?>
							</span>
							<button class="wp-faq-schema-delete" type="button" data-confirm="<?php esc_html_e( 'Are you sure you want to delete this question and answer?', 'wp-faq-schema' ); ?>"><span class="dashicons dashicons-trash"></span></button>
						</div>
						<div class="wp-faq-schema">
							<p>
								<label for="wp-faq-schema-q-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Question', 'wp-faq-schema' ); ?></label>
								<input type="text" id="wp-faq-schema-q-<?php echo esc_attr( $index ); ?>" name="wp-faq-schema[<?php echo esc_attr( $index ); ?>][q]" class="widefat" value="<?php echo esc_attr( ! empty( $faq['q'] ) ? $faq['q'] : '' ); ?>">
							</p>
							<p>
								<label for="wp-faq-schema-a-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Answer', 'wp-faq-schema' ); ?></label>
								<textarea name="wp-faq-schema[<?php echo esc_attr( $index ); ?>][a]" id="wp-faq-schema-a-<?php echo esc_attr( $index ); ?>" rows="3" class="widefat"><?php echo esc_html( ! empty( $faq['a'] ) ? $faq['a'] : '' ); ?></textarea>
							</p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php /* translators: index */ ?>
			<button type="button" class="button button-primary wp-faq-schema-add" data-index="<?php echo count( $faqs ); ?>" data-title="<?php esc_attr_e( 'Question #%s', 'wp-faq-schema' ); ?>">
				<?php echo esc_html_e( 'Add Another Question', 'wp-faq-schema' ); ?>
			</button>
		</div>
		<script type="text/html" id="tmpl-faq-schema-form">
			<div class="wp-faq-schema-item">
				<div class="wp-faq-schema-header">
					<span>{TITLE}</span>
					<button class="wp-faq-schema-delete" type="button" data-confirm="<?php esc_attr_e( 'Are you sure? You want to delete this.', 'wp-faq-schema' ); ?>"><span class="dashicons dashicons-trash"></span></button>
				</div>
				<div class="wp-faq-schema">
					<p>
						<label for="wp-faq-schema-q-{INDEX}"><?php esc_html_e( 'Question', 'wp-faq-schema' ); ?></label>
						<input type="text" id="wp-faq-schema-q-{INDEX}" name="wp-faq-schema[{INDEX}][q]" class="widefat">
					</p>
					<p>
						<label for="wp-faq-schema-a-{INDEX}"><?php esc_html_e( 'Answer', 'wp-faq-schema' ); ?></label>
						<textarea id="wp-faq-schema-a-{INDEX}" name="wp-faq-schema[{INDEX}][a]" rows="3" class="widefat"></textarea>
					</p>
				</div>
			</div>
		</script>
		<?php
	}

	public function save_tax_meta( $term_id ) {
		if ( ! isset( $_POST['wp-faq-schema-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wp-faq-schema-nonce'] ), 'wp-faq-schema' ) ) {
			return;
		}

		update_term_meta( $term_id, '_wp_faq_schema', ! isset( $_POST['wp-faq-schema'] ) ? [] : $this->sanitize( $_POST['wp-faq-schema'] ) ); // phpcs:ignore
	}
}
