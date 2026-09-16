<?php
/**
 * Omnichannel tracking slider (Slick, initialised in assets/js/main.js).
 *
 * Heading, description and the channel cards (title/text, optional icon image) are
 * editable from the admin (ACF "Home — Omnichannel Tracking"). The original multi-
 * colour inline SVG icons are kept as per-card fallbacks so the design is unchanged
 * unless an admin uploads a replacement icon. Markup/classes/behaviour are unchanged.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Built-in inline icons (fallback, in the original order).
$tracking_svgs = array(
	'<svg width="50" height="44" viewBox="0 0 50 44" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_513_1589)"><path d="M2.8 22H24.8C26.3 22 27.6 23.3 27.6 24.8V25.9L14.2 35.8L13.9 36C13.7 36 13.6 36 13.5 35.8L0 25.9V24.8C0 23.3 1.2 22 2.8 22ZM27.5 29.3V41.2C27.5 42.7 26.2 44 24.7 44H2.8C1.3 44 0 42.8 0 41.2V29.3L11.7 38C12.3 38.4 13 38.7 13.8 38.7C14.6 38.7 15.3 38.4 15.9 38L27.6 29.3H27.5Z" fill="#004B81"/><path d="M46.8 13.7998H19.3C17.8 13.7998 16.5 15.0998 16.5 16.5998V19.3998H24.7C27.7 19.3998 30.2 21.8998 30.2 24.8998V35.8998H46.7C48.2 35.8998 49.5 34.6998 49.5 33.0998V16.5998C49.5 15.0998 48.2 13.7998 46.7 13.7998H46.8ZM44 23.3998C44 24.1998 43.3 24.7998 42.6 24.7998H39.9C39.1 24.7998 38.5 24.1998 38.5 23.3998V20.5998C38.5 19.8998 39.1 19.1998 39.9 19.1998H42.6C43.3 19.1998 44 19.8998 44 20.5998V23.3998Z" fill="#E97752"/><path d="M8.2 0H35.7C37.2 0 38.5 1.3 38.5 2.8V11H19.3C16.2 11 13.8 13.5 13.8 16.5V19.3H5.5V2.8C5.5 1.3 6.7 0 8.3 0H8.2Z" fill="#00ABAB"/></g><defs><clipPath id="clip0_513_1589"><rect width="50" height="44" fill="white"/></clipPath></defs></svg>',
	'<svg width="45" height="40" viewBox="0 0 45 40" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_513_1593)"><path d="M42.7996 16.7998C45.5996 17.9998 45.5996 21.8998 42.7996 23.0998L4.7996 39.5998C1.8996 40.8998 -1.1004 37.5998 0.3996 34.7998L6.3996 23.6998C6.6996 22.9998 7.3996 22.4998 8.2996 22.3998L23.3996 20.4998C23.6996 20.4998 23.8996 20.1998 23.8996 19.8998" fill="#E97752"/><path d="M44.7996 19.8998H23.8996C23.8996 19.5998 23.5996 19.3998 23.3996 19.3998L8.2996 17.4998C7.3996 17.2998 6.7996 16.8998 6.3996 16.1998L0.3996 5.09976C-1.1004 2.29976 1.8996 -1.00024 4.7996 0.299762L42.7996 16.7998C44.8996 17.6998 44.8996 19.8998 44.8996 19.8998H44.7996Z" fill="#004B81"/></g><defs><clipPath id="clip0_513_1593"><rect width="45" height="40" fill="white"/></clipPath></defs></svg>',
	'<svg width="44" height="39" viewBox="0 0 44 39" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_513_1610)"><path d="M0 5.5C0 2.5 2.4 0 5.5 0H38.5C41.5 0 44 2.5 44 5.5V33C44 36.1 41.5 38.5 38.5 38.5H5.5C2.4 38.5 0 36.1 0 33V5.5Z" fill="#004B81"/><path d="M5.5 8.20039C5.5 9.70039 6.7 11.0004 8.3 11.0004C9.9 11.0004 11.1 9.80039 11.1 8.20039C11.1 6.60039 9.8 5.40039 8.3 5.40039C6.8 5.40039 5.5 6.70039 5.5 8.20039Z" fill="#E97752"/><path d="M38.5002 8.19961C38.5002 7.09961 37.6002 6.09961 36.4002 6.09961H15.8002C14.6002 6.09961 13.7002 6.99961 13.7002 8.19961C13.7002 9.39961 14.6002 10.2996 15.8002 10.2996H36.4002C37.5002 10.2996 38.5002 9.39961 38.5002 8.19961Z" fill="#00ABAB"/></g><defs><clipPath id="clip0_513_1610"><rect width="44" height="39" fill="white"/></clipPath></defs></svg>',
	'<svg width="56" height="44" viewBox="0 0 56 44" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_513_1596)"><path d="M17.9 30.2C14.5 30.2 11.5 29.5 8.9 28.2C8.2 28.5 7.5 28.8 6.8 29.1C5.2 29.7 3.3 30.1 1.5 30.1C1 30.1 0.4 29.8 0.1 29.2C-0.1 28.7 0.1 28.1 0.6 27.7C0.6 27.7 0.9 27.4 1 27.4C1.3 27.1 1.7 26.7 2.1 26.2C2.5 25.6 3 24.8 3.3 24.1C1.3 21.6 0 18.5 0 15.1C0 6.8 8 0 17.9 0C27.8 0 35.8 6.8 35.8 15.1C35.8 23.4 27.7 30.2 17.9 30.2Z" fill="#004B81"/><path d="M8 10.4C8 11.2 8.6 11.8 9.4 11.8H27.5C28.2 11.8 28.9 11.2 28.9 10.4C28.9 9.6 28.2 9 27.5 9H9.4C8.6 9 8 9.7 8 10.4Z" fill="#00ABAB"/><path d="M8 15.4C8 16.2 8.6 16.8 9.4 16.8H21.5C22.2 16.8 22.9 16.2 22.9 15.4C22.9 14.6 22.2 14 21.5 14H9.4C8.6 14 8 14.7 8 15.4Z" fill="#00ABAB"/><path d="M8 20.4C8 21.2 8.6 21.8 9.4 21.8H17.1C17.8 21.8 18.5 21.2 18.5 20.4C18.5 19.6 17.8 19 17.1 19H9.4C8.6 19 8 19.7 8 20.4Z" fill="#00ABAB"/><path d="M20 20.4C20 21.2 20.6 21.8 21.4 21.8H24.1C24.8 21.8 25.5 21.2 25.5 20.4C25.5 19.6 24.8 19 24.1 19H21.4C20.6 19 20 19.7 20 20.4Z" fill="#00ABAB"/><path d="M38.6004 15.0998V13.7998C47.9004 14.2998 55.1004 20.8998 55.1004 28.7998C55.1004 34 53.6004 36.5 51.7004 37.6998C52.0004 38.4998 52.5004 39.1998 52.9004 39.7998C53.3004 40.3998 53.8004 40.6998 54.0004 41.0998C54.2004 41.0998 54.3004 41.3998 54.4004 41.3998C54.9004 41.7998 55.1004 42.3998 54.9004 42.8998C54.7004 43.4998 54.1004 43.7998 53.6004 43.7998C51.7004 43.7998 49.8004 43.3998 48.2004 42.7998C47.4004 42.4998 46.7004 42.1998 46.1004 41.8998C43.4004 43.1998 40.4004 43.8998 37.2004 43.8998C29.0004 43.8998 22.0004 39.2998 19.9004 32.7998C30.0004 31.8998 38.5004 24.6998 38.5004 14.9998L38.6004 15.0998Z" fill="#E97752"/></g><defs><clipPath id="clip0_513_1596"><rect width="56" height="44" fill="white"/></clipPath></defs></svg>',
	'<svg width="44" height="39" viewBox="0 0 44 39" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_513_1603)"><path d="M8.2 5.5C8.2 2.5 10.6 0 13.7 0H38.5C41.5 0 44 2.5 44 5.5V33C44 36.1 41.5 38.5 38.5 38.5H6.9C3 38.5 0 35.5 0 31.6V8.2C0 6.7 1.2 5.4 2.8 5.4C4.4 5.4 5.6 6.7 5.6 8.2V31.6C5.6 32.4 6.2 33 7 33C7.8 33 8.4 32.4 8.4 31.6V5.5H8.2Z" fill="#004B81"/><path d="M13.7998 7.6V14.5C13.7998 15.7 14.6998 16.6 15.8998 16.6H25.4998C26.5998 16.6 27.5998 15.7 27.5998 14.5V7.6C27.5998 6.5 26.6998 5.5 25.4998 5.5H15.8998C14.6998 5.5 13.7998 6.4 13.7998 7.6Z" fill="#E97752"/><path d="M31.5996 6.9C31.5996 7.7 32.1996 8.3 32.9996 8.3H37.0996C37.7996 8.3 38.4996 7.7 38.4996 6.9C38.4996 6.1 37.7996 5.5 37.0996 5.5H32.9996C32.1996 5.5 31.5996 6.2 31.5996 6.9Z" fill="#00ABAB"/><path d="M31.5996 15.1002C31.5996 15.9002 32.1996 16.5002 32.9996 16.5002H37.0996C37.7996 16.5002 38.4996 15.9002 38.4996 15.1002C38.4996 14.3002 37.7996 13.7002 37.0996 13.7002H32.9996C32.1996 13.7002 31.5996 14.4002 31.5996 15.1002Z" fill="#00ABAB"/><path d="M13.8002 23.4C13.8002 24.2 14.4002 24.8 15.2002 24.8H37.2002C37.9002 24.8 38.6002 24.2 38.6002 23.4C38.6002 22.6 37.9002 22 37.2002 22H15.1002C14.3002 22 13.7002 22.7 13.7002 23.4H13.8002Z" fill="#00ABAB"/><path d="M13.8002 31.6002C13.8002 32.4002 14.4002 33.0002 15.2002 33.0002H37.2002C37.9002 33.0002 38.6002 32.4002 38.6002 31.6002C38.6002 30.8002 37.9002 30.2002 37.2002 30.2002H15.1002C14.3002 30.2002 13.7002 30.9002 13.7002 31.6002H13.8002Z" fill="#00ABAB"/></g><defs><clipPath id="clip0_513_1603"><rect width="44" height="39" fill="white"/></clipPath></defs></svg>',
);

$pid          = (int) get_option( 'page_on_front' );
$track_head   = function_exists( 'get_field' ) ? get_field( 'tracking_heading', $pid ) : '';
if ( ! $track_head ) {
	$track_head = 'Our Comprehensive Omnichannel Tracking';
}
$track_desc = function_exists( 'get_field' ) ? get_field( 'tracking_desc', $pid ) : '';
if ( ! $track_desc ) {
	$track_desc = 'Competiscan captures competitor activity across every major channel, giving you one clear, comparable view of how strategies shift over time and where the market is heading next.';
}

$track_rows = function_exists( 'get_field' ) ? get_field( 'tracking_cards', $pid ) : array();
$track_cards = array();
if ( ! empty( $track_rows ) && is_array( $track_rows ) ) {
	foreach ( $track_rows as $r ) {
		$track_cards[] = array(
			'icon'  => ! empty( $r['icon'] ) ? $r['icon'] : '',
			'title' => isset( $r['title'] ) ? $r['title'] : '',
			'text'  => isset( $r['text'] ) ? $r['text'] : '',
		);
	}
} else {
	$track_fallback = array(
		array( 'Direct Mail', 'Track creative trends, mail volumes, and segmentation strategy.' ),
		array( 'Email', 'Monitor email messages, designs, send volumes, and cadence.' ),
		array( 'Digital', 'See online display and online video creatives, spend and impressions.' ),
		array( 'Social Media', 'Follow organic and paid social activity across platforms.' ),
		array( 'Print', 'Capture print advertising across local newspapers and trade publications.' ),
	);
	foreach ( $track_fallback as $tf ) {
		$track_cards[] = array( 'icon' => '', 'title' => $tf[0], 'text' => $tf[1] );
	}
}
?>
<!-- ============ OMNICHANNEL TRACKING ============ -->
<section class="section tracking">
  <div class="container">
    <div class="tracking-grid">
      <h2><?php echo esc_html( $track_head ); ?></h2>
      <p><?php echo esc_html( $track_desc ); ?></p>
    </div>
    <div class="tracking-cards">
      <div class="tracking-slider">
        <?php foreach ( $track_cards as $ci => $card ) : ?>
        <div>
          <div class="tracking-card">
            <div class="icon">
              <?php
              if ( ! empty( $card['icon'] ) ) {
                echo '<img src="' . esc_url( $card['icon'] ) . '" alt="">';
              } elseif ( isset( $tracking_svgs[ $ci ] ) ) {
                echo $tracking_svgs[ $ci ]; // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG.
              }
              ?>
            </div>
            <h4><?php echo esc_html( $card['title'] ); ?></h4>
            <p><?php echo esc_html( $card['text'] ); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="slick-arrow-group">
        <button class="carousel-arrow2 prev" aria-label="Previous">
          <svg width="16" height="12" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button class="carousel-arrow2 next" aria-label="Next">
          <svg width="16" height="12" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>
  </div>
</section>
