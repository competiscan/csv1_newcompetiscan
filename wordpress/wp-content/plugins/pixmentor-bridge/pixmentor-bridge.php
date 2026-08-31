<?php
/**
 * Plugin Name: Pixmentor Bridge
 * Description: REST bridge that lets the Pixmentor AI plugin inspect Elementor's live widget schemas and create/update Elementor pages, templates, media, menus, the global kit, and site settings. Install on the target WordPress site. Deactivate on production once the build is signed off.
 * Version: 1.4.0
 * Author: NMG Digital
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Allow Application Passwords on local/non-HTTPS dev environments.
add_filter( 'wp_is_application_passwords_available', '__return_true' );

class Pixmentor_Bridge {

	const NS  = 'pixmentor/v1';
	const VER = '1.4.0';

	public static function init() {
		add_action( 'rest_api_init', [ __CLASS__, 'routes' ] );
		add_action( 'admin_menu', [ __CLASS__, 'admin_menu' ] );
	}

	public static function can() {
		return current_user_can( 'manage_options' );
	}

	public static function routes() {
		$r = function ( $path, $methods, $cb ) {
			register_rest_route( self::NS, $path, [
				'methods'             => $methods,
				'callback'            => [ __CLASS__, $cb ],
				'permission_callback' => [ __CLASS__, 'can' ],
			] );
		};
		$r( '/status', 'GET', 'status' );
		$r( '/page', 'POST', 'save_page' );
		$r( '/page/(?P<id>\d+)', 'GET', 'get_page' );
		$r( '/template', 'POST', 'save_template' );
		$r( '/media', 'POST', 'save_media' );
		$r( '/kit', 'GET', 'get_kit' );
		$r( '/kit', 'POST', 'save_kit' );
		$r( '/menu', 'POST', 'save_menu' );
		$r( '/flush-css', 'POST', 'flush_css' );
		$r( '/batch', 'POST', 'batch' );
		$r( '/settings', 'POST', 'save_settings' );
		$r( '/widget-schema', 'GET', 'widget_schema' );
		$r( '/render/(?P<id>\d+)', 'GET', 'render_page' );
		$r( '/validate/(?P<id>\d+)', 'GET', 'validate_page' );
		$r( '/php', 'POST', 'execute_php' );
		$r( '/mcp', 'POST', 'mcp' );
	}

	private static function elementor() {
		return class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::$instance : null;
	}

	/* ---------- GET /status ---------- */
	public static function status() {
		global $wp_version;
		$el  = self::elementor();
		$out = [
			'ok'               => true,
			'bridge_version'   => self::VER,
			'wp_version'       => $wp_version,
			'site_url'         => site_url(),
			'theme'            => wp_get_theme()->get( 'Name' ),
			'elementor'        => defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : null,
			'elementor_pro'    => defined( 'ELEMENTOR_PRO_VERSION' ) ? ELEMENTOR_PRO_VERSION : null,
			'active_kit_id'    => (int) get_option( 'elementor_active_kit' ),
			'container_active' => false,
			'breakpoints'      => null,
			'widgets'          => [],
			'php_enabled'      => self::php_allowed(),
		];
		if ( $el ) {
			if ( method_exists( $el, 'experiments' ) && $el->experiments ) {
				$out['container_active'] = (bool) $el->experiments->is_feature_active( 'container' );
			}
			if ( isset( $el->breakpoints ) && method_exists( $el->breakpoints, 'get_breakpoints_config' ) ) {
				$out['breakpoints'] = $el->breakpoints->get_breakpoints_config();
			}
			if ( isset( $el->widgets_manager ) ) {
				$out['widgets'] = array_keys( $el->widgets_manager->get_widget_types() );
			}
		}
		return rest_ensure_response( $out );
	}

	/* ---------- POST /page ---------- */
	public static function save_page( WP_REST_Request $req ) {
		$p    = $req->get_json_params();
		$data = self::normalize_data( $p['elementor_data'] ?? null );
		if ( null === $data ) {
			return new WP_Error( 'pixmentor_bad_data', 'elementor_data missing or invalid JSON', [ 'status' => 400 ] );
		}
		$postarr = [
			'post_type'   => $p['post_type'] ?? 'page',
			'post_title'  => $p['title'] ?? 'Pixmentor page',
			'post_status' => $p['status'] ?? 'draft',
		];
		if ( ! empty( $p['slug'] ) ) {
			$postarr['post_name'] = sanitize_title( $p['slug'] );
		}
		if ( ! empty( $p['id'] ) ) {
			$postarr['ID'] = (int) $p['id'];
			$id            = wp_update_post( wp_slash( $postarr ), true );
		} else {
			$id = wp_insert_post( wp_slash( $postarr ), true );
		}
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$save_path = self::apply_elementor_meta( $id, $data, $p['page_settings'] ?? null, 'wp-page' );
		if ( ! empty( $p['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', sanitize_text_field( $p['template'] ) );
		}
		self::clear_cache();
		return rest_ensure_response( [
			'ok'          => true,
			'id'          => $id,
			'save_path'   => $save_path,
			'id_fixes'    => $GLOBALS['pixmentor_id_fixes'] ?? 0,
			'permalink'   => get_permalink( $id ),
			'preview_url' => get_preview_post_link( $id ),
			'edit_url'    => admin_url( 'post.php?post=' . $id . '&action=elementor' ),
		] );
	}

	/* ---------- GET /page/{id} ---------- */
	public static function get_page( WP_REST_Request $req ) {
		$id   = (int) $req['id'];
		$post = get_post( $id );
		if ( ! $post ) {
			return new WP_Error( 'pixmentor_not_found', 'Post not found', [ 'status' => 404 ] );
		}
		return rest_ensure_response( [
			'ok'             => true,
			'id'             => $id,
			'title'          => $post->post_title,
			'status'         => $post->post_status,
			'permalink'      => get_permalink( $id ),
			'elementor_data' => json_decode( get_post_meta( $id, '_elementor_data', true ), true ),
			'page_settings'  => get_post_meta( $id, '_elementor_page_settings', true ),
		] );
	}

	/* ---------- POST /template ---------- */
	public static function save_template( WP_REST_Request $req ) {
		$p    = $req->get_json_params();
		$type = sanitize_text_field( $p['type'] ?? 'page' );
		$data = self::normalize_data( $p['elementor_data'] ?? null );
		if ( null === $data ) {
			return new WP_Error( 'pixmentor_bad_data', 'elementor_data missing or invalid JSON', [ 'status' => 400 ] );
		}
		$postarr = [
			'post_type'   => 'elementor_library',
			'post_title'  => $p['title'] ?? ( 'Pixmentor ' . $type ),
			'post_status' => $p['status'] ?? 'publish',
		];
		if ( ! empty( $p['id'] ) ) {
			$postarr['ID'] = (int) $p['id'];
			$id            = wp_update_post( wp_slash( $postarr ), true );
		} else {
			$id = wp_insert_post( wp_slash( $postarr ), true );
		}
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$save_path = self::apply_elementor_meta( $id, $data, $p['page_settings'] ?? null, $type );
		wp_set_object_terms( $id, $type, 'elementor_library_type' );
		if ( ! empty( $p['conditions'] ) && is_array( $p['conditions'] ) ) {
			update_post_meta( $id, '_elementor_conditions', array_map( 'sanitize_text_field', $p['conditions'] ) );
			self::regenerate_conditions();
		}
		self::clear_cache();
		return rest_ensure_response( [
			'ok'        => true,
			'id'        => $id,
			'save_path' => $save_path,
			'edit_url'  => admin_url( 'post.php?post=' . $id . '&action=elementor' ),
		] );
	}

	/* ---------- POST /media ---------- */
	public static function save_media( WP_REST_Request $req ) {
		$p      = $req->get_json_params();
		$source = $p['source'] ?? 'url';
		$name   = sanitize_file_name( $p['filename'] ?? 'pixmentor-asset' );
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = null;
		if ( 'url' === $source ) {
			$tmp = download_url( esc_url_raw( $p['data'] ) );
			if ( is_wp_error( $tmp ) ) {
				return $tmp;
			}
		} elseif ( 'base64' === $source ) {
			$bin = base64_decode( $p['data'], true );
			if ( false === $bin ) {
				return new WP_Error( 'pixmentor_b64', 'Invalid base64 payload', [ 'status' => 400 ] );
			}
			$tmp = wp_tempnam( $name );
			file_put_contents( $tmp, $bin );
		} elseif ( 'local_path' === $source ) {
			$path = $p['data'];
			if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
				return new WP_Error( 'pixmentor_path', 'File not found or unreadable: ' . $path, [ 'status' => 400 ] );
			}
			$tmp = wp_tempnam( basename( $path ) );
			copy( $path, $tmp );
			if ( 'pixmentor-asset' === $name ) {
				$name = sanitize_file_name( basename( $path ) );
			}
		} else {
			return new WP_Error( 'pixmentor_source', 'source must be url, base64, or local_path', [ 'status' => 400 ] );
		}

		$file_array = [ 'name' => $name, 'tmp_name' => $tmp ];
		$att_id     = media_handle_sideload( $file_array, 0, $p['title'] ?? null );
		if ( is_wp_error( $att_id ) ) {
			@unlink( $tmp );
			return $att_id;
		}
		return rest_ensure_response( [
			'ok'  => true,
			'id'  => $att_id,
			'url' => wp_get_attachment_url( $att_id ),
		] );
	}

	/* ---------- GET/POST /kit ---------- */
	public static function get_kit() {
		$kit_id = (int) get_option( 'elementor_active_kit' );
		return rest_ensure_response( [
			'ok'       => true,
			'kit_id'   => $kit_id,
			'settings' => $kit_id ? get_post_meta( $kit_id, '_elementor_page_settings', true ) : null,
		] );
	}

	public static function save_kit( WP_REST_Request $req ) {
		$p      = $req->get_json_params();
		$kit_id = (int) get_option( 'elementor_active_kit' );
		if ( ! $kit_id ) {
			return new WP_Error( 'pixmentor_no_kit', 'No active Elementor kit found', [ 'status' => 404 ] );
		}
		if ( empty( $p['settings'] ) || ! is_array( $p['settings'] ) ) {
			return new WP_Error( 'pixmentor_bad_settings', 'settings object required', [ 'status' => 400 ] );
		}
		$current = get_post_meta( $kit_id, '_elementor_page_settings', true );
		$current = is_array( $current ) ? $current : [];
		$merge   = ! isset( $p['merge'] ) || $p['merge'];
		$new     = $merge ? array_replace( $current, $p['settings'] ) : $p['settings'];
		update_post_meta( $kit_id, '_elementor_page_settings', wp_slash( $new ) );
		self::clear_cache();
		return rest_ensure_response( [ 'ok' => true, 'kit_id' => $kit_id, 'settings' => $new ] );
	}

	/* ---------- POST /menu ---------- */
	public static function save_menu( WP_REST_Request $req ) {
		$p    = $req->get_json_params();
		$name = sanitize_text_field( $p['name'] ?? 'Pixmentor Menu' );
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			$menu_id = $menu->term_id;
			$old     = wp_get_nav_menu_items( $menu_id ) ?: [];
			foreach ( $old as $item ) {
				wp_delete_post( $item->ID, true );
			}
		} else {
			$menu_id = wp_create_nav_menu( $name );
			if ( is_wp_error( $menu_id ) ) {
				return $menu_id;
			}
		}
		$created = [];
		foreach ( (array) ( $p['items'] ?? [] ) as $i => $item ) {
			$args = [
				'menu-item-title'  => sanitize_text_field( $item['title'] ?? '' ),
				'menu-item-url'    => esc_url_raw( $item['url'] ?? '#' ),
				'menu-item-status' => 'publish',
			];
			if ( isset( $item['parent_index'] ) && isset( $created[ (int) $item['parent_index'] ] ) ) {
				$args['menu-item-parent-id'] = $created[ (int) $item['parent_index'] ];
			}
			$created[ $i ] = wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		return rest_ensure_response( [ 'ok' => true, 'menu_id' => $menu_id, 'items' => $created ] );
	}

	/* ---------- POST /flush-css ---------- */
	public static function flush_css() {
		self::clear_cache();
		return rest_ensure_response( [ 'ok' => true ] );
	}

	/* ---------- POST /batch ---------- */
	public static function batch( WP_REST_Request $req ) {
		$p   = $req->get_json_params();
		$ops = is_array( $p['ops'] ?? null ) ? $p['ops'] : [];
		$map = [
			'page'      => 'save_page',
			'template'  => 'save_template',
			'media'     => 'save_media',
			'kit'       => 'save_kit',
			'menu'      => 'save_menu',
			'settings'  => 'save_settings',
			'flush-css' => 'flush_css',
		];
		$results = [];
		foreach ( $ops as $op ) {
			$type = $op['op'] ?? '';
			if ( ! isset( $map[ $type ] ) ) {
				$results[] = [ 'ok' => false, 'error' => 'unknown op: ' . $type ];
				continue;
			}
			$sub = new WP_REST_Request( 'POST', '/' . self::NS . '/' . $type );
			$sub->set_header( 'Content-Type', 'application/json' );
			$sub->set_body( wp_json_encode( $op['payload'] ?? [] ) );
			$res = call_user_func( [ __CLASS__, $map[ $type ] ], $sub );
			if ( is_wp_error( $res ) ) {
				$results[] = [ 'ok' => false, 'error' => $res->get_error_message() ];
			} else {
				$results[] = $res->get_data();
			}
		}
		return rest_ensure_response( [ 'ok' => true, 'count' => count( $results ), 'results' => $results ] );
	}

	/* ---------- POST /settings ---------- */
	public static function save_settings( WP_REST_Request $req ) {
		$p    = $req->get_json_params();
		$done = [];
		if ( ! empty( $p['front_page_id'] ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', (int) $p['front_page_id'] );
			$done['front_page_id'] = (int) $p['front_page_id'];
		}
		if ( ! empty( $p['posts_page_id'] ) ) {
			update_option( 'page_for_posts', (int) $p['posts_page_id'] );
			$done['posts_page_id'] = (int) $p['posts_page_id'];
		}
		if ( ! empty( $p['site_title'] ) ) {
			update_option( 'blogname', sanitize_text_field( $p['site_title'] ) );
			$done['site_title'] = true;
		}
		if ( isset( $p['tagline'] ) ) {
			update_option( 'blogdescription', sanitize_text_field( $p['tagline'] ) );
			$done['tagline'] = true;
		}
		if ( ! empty( $p['menu_locations'] ) && is_array( $p['menu_locations'] ) ) {
			$locations = get_theme_mod( 'nav_menu_locations', [] );
			$locations = is_array( $locations ) ? $locations : [];
			foreach ( $p['menu_locations'] as $loc => $ref ) {
				$menu = is_numeric( $ref ) ? wp_get_nav_menu_object( (int) $ref ) : wp_get_nav_menu_object( $ref );
				if ( $menu ) {
					$locations[ sanitize_key( $loc ) ] = $menu->term_id;
				}
			}
			set_theme_mod( 'nav_menu_locations', $locations );
			$done['menu_locations'] = $locations;
		}
		if ( ! empty( $p['permalink_structure'] ) ) {
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure( $p['permalink_structure'] );
			flush_rewrite_rules();
			$done['permalink_structure'] = $p['permalink_structure'];
		}
		return rest_ensure_response( [ 'ok' => true, 'applied' => $done ] );
	}

	/* ---------- GET /widget-schema?type=heading ----------
	 * Live control schema from the running Elementor: control names, types,
	 * defaults, responsive flags. Generate JSON against THIS, not static docs —
	 * an unknown setting key is silently ignored and the theme/kit default leaks.
	 */
	public static function widget_schema( WP_REST_Request $req ) {
		$el   = self::elementor();
		$type = sanitize_text_field( (string) ( $req->get_param( 'type' ) ?? '' ) );
		if ( ! $el ) {
			return new WP_Error( 'pixmentor_schema', 'Elementor not available', [ 'status' => 400 ] );
		}
		if ( '' === $type ) {
			return new WP_Error( 'pixmentor_schema', 'type param required', [ 'status' => 400 ] );
		}
		$element = null;
		if ( isset( $el->widgets_manager ) ) {
			$element = $el->widgets_manager->get_widget_types( $type );
		}
		if ( ! $element && isset( $el->elements_manager ) ) {
			$element = $el->elements_manager->get_element_types( $type );
		}
		if ( ! $element ) {
			return new WP_Error( 'pixmentor_schema', 'Unknown widget/element type: ' . $type, [ 'status' => 404 ] );
		}
		$controls = [];
		foreach ( (array) $element->get_controls() as $name => $c ) {
			if ( ! is_array( $c ) ) {
				continue;
			}
			$entry = [ 'type' => $c['type'] ?? '' ];
			if ( array_key_exists( 'default', $c ) && null !== $c['default'] && [] !== $c['default'] ) {
				$entry['default'] = $c['default'];
			}
			if ( ! empty( $c['responsive'] ) ) {
				$entry['responsive'] = true;
			}
			if ( ! empty( $c['options'] ) && is_array( $c['options'] ) && count( $c['options'] ) <= 30 ) {
				$entry['options'] = array_keys( $c['options'] );
			}
			$controls[ $name ] = $entry;
		}
		return rest_ensure_response( [
			'ok'            => true,
			'type'          => $type,
			'control_count' => count( $controls ),
			'controls'      => $controls,
		] );
	}

	/* ---------- GET /render/{id} ---------- */
	public static function render_page( WP_REST_Request $req ) {
		$el = self::elementor();
		$id = (int) $req['id'];
		if ( ! $el || ! get_post( $id ) ) {
			return new WP_Error( 'pixmentor_render', 'Elementor missing or post not found', [ 'status' => 404 ] );
		}
		$html = '';
		if ( isset( $el->frontend ) && method_exists( $el->frontend, 'get_builder_content_for_display' ) ) {
			$html = (string) $el->frontend->get_builder_content_for_display( $id, true );
		}
		return rest_ensure_response( [
			'ok'        => true,
			'id'        => $id,
			'permalink' => get_permalink( $id ),
			'length'    => strlen( $html ),
			'html'      => $html,
		] );
	}

	/* ---------- GET /validate/{id} ----------
	 * Editability + structural audit of a built page's stored data. Flags the
	 * things that force an admin to recreate a section by hand:
	 *  - duplicate/malformed element ids (editor fails to load the element)
	 *  - layout/style properties baked into custom_css instead of native controls
	 *    (native controls then appear to "do nothing"; responsive edits fail too)
	 *  - elements missing required keys
	 * A page that renders 95% but fails this is NOT a good build.
	 */
	public static function validate_page( WP_REST_Request $req ) {
		$id  = (int) $req['id'];
		$raw = get_post_meta( $id, '_elementor_data', true );
		$data = is_string( $raw ) ? json_decode( $raw, true ) : ( is_array( $raw ) ? $raw : null );
		if ( ! is_array( $data ) ) {
			return new WP_Error( 'pixmentor_validate', 'No Elementor data for post ' . $id, [ 'status' => 404 ] );
		}
		$report = [
			'element_count'      => 0,
			'duplicate_ids'      => [],
			'missing_ids'        => 0,
			'layout_css_offenders' => [], // element id => matched layout properties
			'html_widgets'       => 0,
			'max_depth'          => 0,
		];
		$seen = [];
		self::walk_validate( $data, 1, $seen, $report );
		$report['duplicate_ids'] = array_values( array_unique( $report['duplicate_ids'] ) );
		$editable = empty( $report['duplicate_ids'] )
			&& 0 === $report['missing_ids']
			&& empty( $report['layout_css_offenders'] );
		return rest_ensure_response( [
			'ok'       => true,
			'id'       => $id,
			'editable' => $editable,
			'report'   => $report,
			'note'     => $editable
				? 'No editability blockers found.'
				: 'Editability blockers found — fix before handoff (see report). layout_css_offenders means a property that should be a native control is hardcoded in custom_css.',
		] );
	}

	/** Layout/style CSS properties that MUST be native Elementor controls, not custom_css. */
	private static function layout_css_props() {
		return [ 'padding', 'margin', 'width', 'max-width', 'min-height', 'height', 'gap',
			'display', 'flex-direction', 'justify-content', 'align-items', 'grid-template',
			'background', 'background-color', 'border', 'border-radius', 'box-shadow',
			'color', 'font-size', 'font-weight', 'font-family', 'line-height', 'text-align',
			'position', 'top', 'left', 'right', 'bottom' ];
	}

	private static function walk_validate( array $els, $depth, array &$seen, array &$report ) {
		if ( $depth > $report['max_depth'] ) {
			$report['max_depth'] = $depth;
		}
		foreach ( $els as $el ) {
			if ( ! is_array( $el ) ) {
				continue;
			}
			$report['element_count']++;
			$eid = isset( $el['id'] ) ? (string) $el['id'] : '';
			if ( '' === $eid ) {
				$report['missing_ids']++;
			} elseif ( isset( $seen[ $eid ] ) ) {
				$report['duplicate_ids'][] = $eid;
			} else {
				$seen[ $eid ] = true;
			}
			if ( isset( $el['widgetType'] ) && 'html' === $el['widgetType'] ) {
				$report['html_widgets']++;
			}
			$css = '';
			if ( isset( $el['settings']['custom_css'] ) && is_string( $el['settings']['custom_css'] ) ) {
				$css = strtolower( $el['settings']['custom_css'] );
			}
			if ( '' !== $css ) {
				$hits = [];
				foreach ( self::layout_css_props() as $prop ) {
					if ( false !== strpos( $css, $prop . ':' ) || false !== strpos( $css, $prop . ' :' ) ) {
						$hits[] = $prop;
					}
				}
				if ( $hits ) {
					$report['layout_css_offenders'][ $eid ?: '(no-id)' ] = array_values( array_unique( $hits ) );
				}
			}
			if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
				self::walk_validate( $el['elements'], $depth + 1, $seen, $report );
			}
		}
	}

	/* ---------- POST /php ----------
	 * body: { code } — send raw PHP without an opening tag; use "return $value;"
	 * to return data. Guarded: manage_options AND non-production environment
	 * (override with PIXMENTOR_ALLOW_PHP in wp-config.php). Dev/staging tool.
	 */
	public static function execute_php( WP_REST_Request $req ) {
		if ( ! self::php_allowed() ) {
			return new WP_Error( 'pixmentor_php_forbidden', 'PHP execution disabled: environment is production. Define PIXMENTOR_ALLOW_PHP as true in wp-config.php to override (not recommended).', [ 'status' => 403 ] );
		}
		$p    = $req->get_json_params();
		$code = (string) ( $p['code'] ?? '' );
		if ( '' === trim( $code ) ) {
			return new WP_Error( 'pixmentor_php_empty', 'code required', [ 'status' => 400 ] );
		}
		$captured = [];
		set_error_handler(
			function ( $no, $str, $file, $line ) use ( &$captured ) {
				$captured[] = [ 'type' => $no, 'message' => $str, 'file' => basename( (string) $file ), 'line' => $line ];
				return true;
			}
		);
		$start = microtime( true );
		ob_start();
		try {
			$return_value = eval( $code ); // phpcs:ignore Squiz.PHP.Eval -- deliberate, guarded dev tool.
			$result       = [
				'ok'           => true,
				'return_value' => $return_value,
				'output'       => ob_get_clean(),
			];
		} catch ( \Throwable $e ) {
			$result = [
				'ok'            => false,
				'output'        => ob_get_clean(),
				'error_message' => $e->getMessage(),
				'error_class'   => get_class( $e ),
				'error_line'    => $e->getLine(),
			];
		}
		restore_error_handler();
		$result['errors']            = $captured;
		$result['execution_time_ms'] = round( ( microtime( true ) - $start ) * 1000, 1 );
		return rest_ensure_response( $result );
	}

	/* ---------- POST /mcp ----------
	 * MCP JSON-RPC over HTTP so any AI client (via an MCP remote proxy) can drive
	 * the bridge directly. Same Basic application-password auth as every route.
	 */
	public static function mcp( WP_REST_Request $req ) {
		$msg    = $req->get_json_params();
		$id     = $msg['id'] ?? null;
		$method = $msg['method'] ?? '';

		if ( 'initialize' === $method ) {
			return rest_ensure_response( [
				'jsonrpc' => '2.0',
				'id'      => $id,
				'result'  => [
					'protocolVersion' => $msg['params']['protocolVersion'] ?? '2024-11-05',
					'capabilities'    => [ 'tools' => new stdClass() ],
					'serverInfo'      => [ 'name' => 'pixmentor-bridge', 'version' => self::VER ],
				],
			] );
		}
		if ( is_string( $method ) && 0 === strpos( $method, 'notifications/' ) ) {
			return rest_ensure_response( new stdClass() );
		}
		if ( 'ping' === $method ) {
			return rest_ensure_response( [ 'jsonrpc' => '2.0', 'id' => $id, 'result' => new stdClass() ] );
		}
		if ( 'tools/list' === $method ) {
			$tools = [];
			foreach ( self::mcp_tool_map() as $name => $t ) {
				$tools[] = [ 'name' => $name, 'description' => $t[1], 'inputSchema' => [ 'type' => 'object' ] ];
			}
			return rest_ensure_response( [ 'jsonrpc' => '2.0', 'id' => $id, 'result' => [ 'tools' => $tools ] ] );
		}
		if ( 'tools/call' === $method ) {
			$name = $msg['params']['name'] ?? '';
			$args = $msg['params']['arguments'] ?? [];
			$map  = self::mcp_tool_map();
			if ( ! isset( $map[ $name ] ) ) {
				return rest_ensure_response( [ 'jsonrpc' => '2.0', 'id' => $id, 'error' => [ 'code' => -32602, 'message' => 'Unknown tool: ' . $name ] ] );
			}
			$args = is_array( $args ) ? $args : [];
			$sub  = new WP_REST_Request( 'POST', '/' . self::NS . '/mcp-call' );
			$sub->set_header( 'Content-Type', 'application/json' );
			$sub->set_body( wp_json_encode( $args ) );
			foreach ( $args as $k => $v ) {
				$sub->set_param( $k, $v );
			}
			$res  = call_user_func( [ __CLASS__, $map[ $name ][0] ], $sub );
			$data = is_wp_error( $res ) ? [ 'ok' => false, 'error' => $res->get_error_message() ] : $res->get_data();
			return rest_ensure_response( [
				'jsonrpc' => '2.0',
				'id'      => $id,
				'result'  => [
					'content' => [ [ 'type' => 'text', 'text' => wp_json_encode( $data ) ] ],
					'isError' => is_wp_error( $res ),
				],
			] );
		}
		return rest_ensure_response( [ 'jsonrpc' => '2.0', 'id' => $id, 'error' => [ 'code' => -32601, 'message' => 'Method not found: ' . $method ] ] );
	}

	private static function mcp_tool_map() {
		return [
			'status'        => [ 'status', 'WP + Elementor environment (versions, Pro, containers, breakpoints, widget list, kit id, theme)' ],
			'widget_schema' => [ 'widget_schema', 'Live control schema for a widget/element type on THIS site: { type } → control names, types, defaults, responsive flags. Generate settings against this.' ],
			'render_page'   => [ 'render_page', 'Server-side rendered Elementor HTML for a post: { id }' ],
			'validate_page' => [ 'validate_page', 'Editability/structural audit: { id } → duplicate ids, layout baked into custom_css, html widgets, depth. editable:false = admin will have to recreate sections.' ],
			'save_page'     => [ 'save_page', 'Create/update a page: { id?, title, slug?, status?, template?, elementor_data, page_settings? }' ],
			'get_page'      => [ 'get_page', 'Fetch a page incl. stored elementor_data: { id }' ],
			'save_template' => [ 'save_template', 'Create/update an Elementor library template (header/footer/popup/section/page) with display conditions' ],
			'save_media'    => [ 'save_media', 'Upload media: { source: url|base64|local_path, data, filename?, title? }' ],
			'get_kit'       => [ 'get_kit', 'Read the active Elementor kit settings' ],
			'save_kit'      => [ 'save_kit', 'Write global kit settings: { settings, merge? }' ],
			'save_menu'     => [ 'save_menu', 'Create/replace a nav menu: { name, items: [{title,url,parent_index?}] }' ],
			'save_settings' => [ 'save_settings', 'Site config: { front_page_id?, posts_page_id?, menu_locations?, site_title?, tagline?, permalink_structure? }' ],
			'flush_css'     => [ 'flush_css', 'Clear Elementor generated CSS cache' ],
			'batch'         => [ 'batch', 'Run many ops in one call: { ops: [{op,payload}] }' ],
			'execute_php'   => [ 'execute_php', 'Run PHP on the site (dev/staging only, guarded): { code }' ],
		];
	}

	/* ---------- admin page: Settings → Pixmentor ---------- */

	public static function admin_menu() {
		add_options_page( 'Pixmentor', 'Pixmentor', 'manage_options', 'pixmentor', [ __CLASS__, 'admin_page' ] );
	}

	public static function admin_page() {
		$site = site_url();
		$el   = defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : 'NOT INSTALLED';
		$pro  = defined( 'ELEMENTOR_PRO_VERSION' ) ? ELEMENTOR_PRO_VERSION : 'not active';
		$json = wp_json_encode(
			[
				'mcpServers' => [
					'pixmentor-wp' => [
						'command' => 'npx',
						'args'    => [ '-y', '@automattic/mcp-wordpress-remote@latest' ],
						'env'     => [
							'WP_API_URL'      => trailingslashit( $site ),
							'WP_API_USERNAME' => '<admin-username>',
							'WP_API_PASSWORD' => '<application-password>',
						],
					],
				],
			],
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
		);
		?>
		<div class="wrap">
			<h1>Pixmentor Configuration</h1>
			<table class="widefat striped" style="max-width:820px">
				<tbody>
					<tr><td>Bridge version</td><td><?php echo esc_html( self::VER ); ?></td></tr>
					<tr><td>Elementor</td><td><?php echo esc_html( $el ); ?></td></tr>
					<tr><td>Elementor Pro</td><td><?php echo esc_html( $pro ); ?></td></tr>
					<tr><td>REST base</td><td><code><?php echo esc_html( $site ); ?>/wp-json/pixmentor/v1/</code></td></tr>
					<tr><td>MCP endpoint</td><td><code><?php echo esc_html( $site ); ?>/?rest_route=/pixmentor/v1/mcp</code></td></tr>
					<tr><td>PHP tool</td><td><?php echo self::php_allowed() ? 'enabled (non-production)' : 'disabled (production)'; ?></td></tr>
				</tbody>
			</table>

			<h2>Connect from Claude (Cowork)</h2>
			<p>Install the Pixmentor plugin in the Claude desktop app, then send this in any chat:</p>
			<pre style="background:#fff;padding:12px;border:1px solid #ccd0d4;max-width:820px">Set up pixmentor.
Site: <?php echo esc_html( $site ); ?>

Username: &lt;admin-username&gt;
Application password: &lt;application-password&gt;</pre>
			<p>Create the application password under <strong>Users → Profile → Application Passwords</strong>.</p>

			<details style="max-width:820px">
				<summary style="cursor:pointer;font-weight:600">Need the JSON config for a specific client?</summary>
				<p>For AI clients that take MCP server JSON (Claude Code, Cursor, etc.) via the WordPress remote proxy (requires Node.js). Credentials go ONLY in <code>env</code> — CLI flags are ignored by the package.</p>
				<pre style="background:#fff;padding:12px;border:1px solid #ccd0d4;overflow:auto"><?php echo esc_html( (string) $json ); ?></pre>
			</details>

			<p style="margin-top:16px"><strong>Security:</strong> deactivate this plugin on production once the build is signed off. Revoke application passwords under Users → Profile.</p>
		</div>
		<?php
	}

	/* ---------- helpers ---------- */

	private static function php_allowed() {
		if ( defined( 'PIXMENTOR_ALLOW_PHP' ) ) {
			return (bool) PIXMENTOR_ALLOW_PHP;
		}
		return function_exists( 'wp_get_environment_type' ) && 'production' !== wp_get_environment_type();
	}

	private static function normalize_data( $data ) {
		if ( is_string( $data ) ) {
			$data = json_decode( $data, true );
		}
		return is_array( $data ) ? $data : null;
	}

	/**
	 * Guarantee every element has a unique 7-char hex id. Duplicate or missing
	 * ids make Elementor's editor fail to load that element — the section then
	 * looks broken and can only be recreated. Mutates $data by reference and
	 * counts how many ids were fixed.
	 */
	private static function dedupe_ids( array &$data, array &$seen, &$fixed ) {
		foreach ( $data as &$el ) {
			if ( ! is_array( $el ) ) {
				continue;
			}
			$id = isset( $el['id'] ) ? (string) $el['id'] : '';
			if ( '' === $id || isset( $seen[ $id ] ) || ! preg_match( '/^[0-9a-f]{7,8}$/', $id ) ) {
				do {
					$id = substr( md5( uniqid( (string) wp_rand(), true ) ), 0, 7 );
				} while ( isset( $seen[ $id ] ) );
				$el['id'] = $id;
				$fixed++;
			}
			$seen[ $id ] = true;
			if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
				self::dedupe_ids( $el['elements'], $seen, $fixed );
			}
		}
		unset( $el );
	}

	/**
	 * Save through Elementor's own document API when available, so the payload is
	 * normalized exactly as the editor would and CSS regenerates identically.
	 * Falls back to raw meta writes. Returns which path was used.
	 */
	private static function apply_elementor_meta( $id, array $data, $page_settings, $doc_type ) {
		update_post_meta( $id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $id, '_elementor_template_type', $doc_type );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
		}

		$seen = [];
		$fixed = 0;
		self::dedupe_ids( $data, $seen, $fixed );
		$GLOBALS['pixmentor_id_fixes'] = $fixed;

		$el    = self::elementor();
		$saved = false;
		if ( $el && isset( $el->documents ) ) {
			$doc = $el->documents->get( $id, false );
			if ( $doc && method_exists( $doc, 'save' ) ) {
				$payload = [ 'elements' => $data ];
				if ( is_array( $page_settings ) ) {
					$payload['settings'] = $page_settings;
				}
				try {
					$doc->save( $payload );
					$saved = true;
				} catch ( \Throwable $e ) {
					$saved = false;
				}
			}
		}

		if ( ! $saved ) {
			update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
			if ( is_array( $page_settings ) ) {
				update_post_meta( $id, '_elementor_page_settings', wp_slash( $page_settings ) );
			}
		}
		return $saved ? 'document_api' : 'raw_meta';
	}

	private static function clear_cache() {
		$el = self::elementor();
		if ( $el && isset( $el->files_manager ) ) {
			$el->files_manager->clear_cache();
		}
	}

	private static function regenerate_conditions() {
		if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
			return;
		}
		$pro = \ElementorPro\Plugin::instance();
		if ( ! isset( $pro->modules_manager ) ) {
			return;
		}
		$tb = $pro->modules_manager->get_modules( 'theme-builder' );
		if ( $tb && method_exists( $tb, 'get_conditions_manager' ) ) {
			$cm = $tb->get_conditions_manager();
			if ( method_exists( $cm, 'get_cache' ) ) {
				$cache = $cm->get_cache();
				if ( $cache && method_exists( $cache, 'regenerate' ) ) {
					$cache->regenerate();
				}
			}
		}
	}
}

Pixmentor_Bridge::init();
