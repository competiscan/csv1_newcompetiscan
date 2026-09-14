<?php
/**
 * Plugin file
 *
 * @package WpFaqSchema
 */

namespace WpFaqSchema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class
 */
class Plugin {
	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'wp-faq-schema' ), '1.0.0' );
	}

	/**
	 * Wakeup.
	 *
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'wp-faq-schema' ), '1.0.0' );
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor Method for plugin
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		$this->includes();
		$this->init();

		add_action( 'wp_head', [ $this, 'print_schema' ] );
		add_filter( 'init', [ $this, 'shortcode' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_links' ], 10, 2 );
	}

	/**
	 * Include all files
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function includes() {
		require_once WP_FAQ_SCHEMA_PATH . 'includes/class-option-page.php';
		require_once WP_FAQ_SCHEMA_PATH . 'includes/class-metabox.php';
	}

	/**
	 * Initialize
	 *
	 * Initialize some classes which is not require hook.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return  void
	 */
	public function init() {

		new Metabox();
		new Option_Page();
	}

	/**
	 * Print FAQ schema at Header
	 *
	 * @return  void
	 */
	public function print_schema() {
		$faqs = get_post_meta( get_the_ID(), '_wp_faq_schema', true );
		if ( ! is_singular() && ! is_tax() && ! is_category() && ! is_tag() ) {
			return;
		}
		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			$faqs = get_term_meta( $term->term_id, '_wp_faq_schema', true );
		}
		if ( empty( $faqs ) ) {
			return;
		}

		$schema_array = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => [],
		];

		if ( ! empty( $faqs ) ) {
			foreach ( $faqs as $faq ) {
				$schema_array['mainEntity'][] = [
					'@type'          => 'Question',
					'name'           => $faq['q'],
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => $faq['a'],
					],
				];
			}
		}
		?>
		<script type="application/ld+json">
			<?php echo wp_json_encode( $schema_array ); ?>
		</script>
		<?php
	}

	/**
	 * Shortcodes
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function shortcode() {
		add_shortcode( 'wp-faq-schema', [ $this, 'shortcode_view' ] );
	}

	/**
	 * Shortcode View
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $atts Shortcode Atts.
	 *
	 * @return string
	 */
	public function shortcode_view( $atts ) {
		$atts = shortcode_atts(
			array(
				'accordion' => 0,
				'title'     => '',
			),
			$atts
		);

		$faqs = get_post_meta( get_the_ID(), '_wp_faq_schema', true );

		if ( empty( $faqs ) ) {
			return '';
		}

		$is_accordion = isset( $atts['accordion'] ) ? intval( $atts['accordion'] ) : 0;

		$wrap_class = [ 'wp-faq-schema-wrap' ];

		if ( 1 === $is_accordion ) {
			$wrap_class[] = 'wp-faq-schema-accordion';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrap_class ) ); ?>">
			<?php if ( ! empty( $atts['title'] ) ) : ?>
				<h2><?php echo esc_html( $atts['title'] ); ?></h2>
			<?php endif; ?>
			<div class="wp-faq-schema-items">
				<?php foreach ( $faqs as $faq ) : ?>
					<h3><?php echo esc_html( $faq['q'] ); ?></h3>
					<div class="">
						<?php echo wp_kses_post( wpautop( $faq['a'] ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Frontend assets
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function frontend_assets() {
		wp_enqueue_style( 'wp-faq-schema-jquery-ui', WP_FAQ_SCHEMA_URL . '/css/jquery-ui.css', [], '2.0.0' );
		wp_enqueue_script( 'wp-faq-schema-frontend', WP_FAQ_SCHEMA_URL . '/js/frontend.js', [ 'jquery', 'jquery-ui-accordion' ], '2.0.0', true );
	}

	public function plugin_links( $plugin_meta, $plugin_file ) {
		if ( 'faq-schema-for-pages-and-posts/wp-faq-schema.php' === $plugin_file ) {
			$plugin_meta[] = '<a href="https://www.onlinemarketinggurus.com.au/faq-schema-plugin" target="_blank">Support</a>';
		}

		return $plugin_meta;
	}
}

Plugin::instance();
