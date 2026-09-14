<?php
/**
 * Normalise the dynamically-appended Flexible Content layouts.
 *
 * The per-page layout files (toolkit-layouts.php, careers-layouts.php, etc.) append
 * layouts — and their sub-fields — to the shared cms_content flexible field via the
 * acf/load_field filter. Because those fields are injected at load time they skip
 * ACF's normal field validation, so they are missing default keys ACF core and the
 * admin read directly (`min`/`max` on layouts; `ID`, `required`, `wrapper`, … on
 * fields). That triggers "Undefined array key …" notices in the editor
 * (Render.php, acf-field-group/field.php, acf-field-functions.php).
 *
 * This runs at a late priority (after every layout has been appended) and fills the
 * missing structural defaults on each layout and, recursively, on every sub-field.
 * It only supplies defaults for keys that are absent — no existing values change.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give a single field every default key ACF expects, recursing into the sub-fields
 * of repeaters/groups. Uses array-union (+=) so present values are never overwritten.
 *
 * @param array $f A field definition.
 * @return array
 */
function competiscan_normalize_flex_field( $f ) {
	if ( ! is_array( $f ) ) {
		return $f;
	}

	$f += array(
		'ID'                => false,
		'required'          => 0,
		'conditional_logic' => 0,
		'instructions'      => '',
		'menu_order'        => 0,
		'parent'            => '',
	);
	if ( ! isset( $f['wrapper'] ) || ! is_array( $f['wrapper'] ) ) {
		$f['wrapper'] = array();
	}
	$f['wrapper'] += array(
		'width' => '',
		'class' => '',
		'id'    => '',
	);

	if ( isset( $f['sub_fields'] ) && is_array( $f['sub_fields'] ) ) {
		foreach ( $f['sub_fields'] as $i => $sf ) {
			$f['sub_fields'][ $i ] = competiscan_normalize_flex_field( $sf );
		}
	}

	return $f;
}

/**
 * Ensure every layout on the flexible field — and each of its sub-fields — has the
 * keys ACF expects.
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

		$layout += array(
			'min'     => '',
			'max'     => '',
			'display' => 'block',
		);
		if ( ! isset( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
			$layout['sub_fields'] = array();
		}
		foreach ( $layout['sub_fields'] as $i => $sf ) {
			$layout['sub_fields'][ $i ] = competiscan_normalize_flex_field( $sf );
		}

		$field['layouts'][ $key ] = $layout;
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_normalize_flex_layouts', 999 );
