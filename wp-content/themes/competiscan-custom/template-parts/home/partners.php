<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ACF must be active.
if ( ! function_exists( 'get_field' ) ) {
    return;
}

// Get actual homepage ID.
$pid = (int) get_option( 'page_on_front' );

if ( ! $pid ) {
    return;
}

// Get admin-added logos.
$logo_rows = get_field( 'partners_logos', $pid );

// No admin logos = no section.
if ( empty( $logo_rows ) || ! is_array( $logo_rows ) ) {
    return;
}

$logos = array();

foreach ( $logo_rows as $row ) {

    $logo = $row['logo'] ?? '';

    if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
        $logos[] = array(
            'url' => $logo['url'],
            'alt' => $logo['alt'] ?? 'Partner logo',
        );
    } elseif ( is_numeric( $logo ) ) {

        $url = wp_get_attachment_image_url( (int) $logo, 'full' );

        if ( $url ) {
            $logos[] = array(
                'url' => $url,
                'alt' => 'Partner logo',
            );
        }

    } elseif ( is_string( $logo ) && ! empty( $logo ) ) {

        $logos[] = array(
            'url' => $logo,
            'alt' => 'Partner logo',
        );
    }
}

// Hide section if no valid images.
if ( empty( $logos ) ) {
    return;
}

// All logos removed in WP Admin → hide the whole section.
if ( empty( $logos ) ) {
	return;
}
?>

<section class="partners">
    <div class="container">
        <span class="eyebrow-pill">
            <?php echo esc_html( get_field( 'partners_eyebrow', $pid ) ?: 'Our Trusted Partners' ); ?>
        </span>
    </div>

    <div class="partners-track container">
        <div class="marquee-row">

            <?php foreach ( $logos as $logo ) : ?>
                <span class="logo-chip">
                    <img
                        src="<?php echo esc_url( $logo['url'] ); ?>"
                        alt="<?php echo esc_attr( $logo['alt'] ); ?>"
                    >
                </span>
            <?php endforeach; ?>

        </div>
    </div>
</section>