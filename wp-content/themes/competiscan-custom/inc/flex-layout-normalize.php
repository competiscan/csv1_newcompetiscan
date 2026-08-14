<?php
/**
 * Normalise the dynamically-appended Flexible Content layouts.
 *
 * The per-page layout files (toolkit-layouts.php, careers-layouts.php, etc.) append
 * layouts to the shared cms_content flexible field without the optional `min`/`max`
 * keys. ACF Pro's Flexible Content renderer reads those keys directly, so on the
 * post-edit screen it emits "Undefined array key min/max" warnings (Render.php),
 * which clutter the editor and make the sections hard to manage.
 *
 * This runs at a late priority (after every layout has been appended) and fills in
 * the keys ACF expects on each layout, so the editor is clean. No layout content
 * changes — it only supplies missing structural defaults.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure every layout on the flexible field has the keys ACF expects.
 *
 * @param array $field The cms_content flexible-content field.
 * @return array
 */
function competiscan_normalize_flex_layouts( $field ) {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	foreach ( $field['layouts'] as $key => $layout ) {
		if ( ! is_array( $layout ) ) {
			continue;
		}
		if ( ! isset( $layout['min'] ) ) {
			$field['layouts'][ $key ]['min'] = '';
		}
		if ( ! isset( $layout['max'] ) ) {
			$field['layouts'][ $key ]['max'] = '';
		}
		if ( ! isset( $layout['display'] ) ) {
			$field['layouts'][ $key ]['display'] = 'block';
		}
		if ( ! isset( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
			$field['layouts'][ $key ]['sub_fields'] = array();
		}
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_normalize_flex_layouts', 999 );
