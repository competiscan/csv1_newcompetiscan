<?php
/**
 * Insights page — Webinars grid.
 *
 * Renders every published "Webinar" post (WP Admin → Webinars, see inc/webinars.php)
 * with the original card markup. Optional parts (image, tag, time, description,
 * button, closed message) are only output when they have a value, so a card with
 * the default fields looks exactly like the original design.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$competiscan_webinars = function_exists( 'competiscan_get_webinars' ) ? competiscan_get_webinars() : array();

if ( empty( $competiscan_webinars ) ) : ?>
          <p class="conferences-note webinars-empty"><?php esc_html_e( 'Check back soon for upcoming webinars.', 'competiscan-custom' ); ?></p>
	<?php
	return;
endif;
?>
          <div class="webinars-grid">
<?php
foreach ( $competiscan_webinars as $competiscan_w ) :
	$competiscan_card_class = 'webinar-card';
	if ( $competiscan_w['image_id'] ) {
		$competiscan_card_class .= ' has-thumb';
	}
	if ( '' === $competiscan_w['tag'] ) {
		$competiscan_card_class .= ' no-tag';
	}
	$competiscan_show_button = ! $competiscan_w['closed'] && '' !== $competiscan_w['button_label'] && '' !== $competiscan_w['button_url'];
	?>
            <div class="<?php echo esc_attr( $competiscan_card_class ); ?>">
              <?php if ( $competiscan_w['image_id'] ) : ?>
              <div class="webinar-thumb"><?php echo wp_get_attachment_image( $competiscan_w['image_id'], 'medium_large', false, array( 'alt' => $competiscan_w['image_alt'], 'loading' => 'lazy' ) ); ?></div>
              <?php endif; ?>
              <?php if ( '' !== $competiscan_w['tag'] ) : ?>
              <span class="tag"><?php echo esc_html( $competiscan_w['tag'] ); ?></span>
              <?php endif; ?>
              <div class="webinar-date">
                <?php if ( '' !== $competiscan_w['month'] ) : ?>
                <div class="date-box"><div class="mon"><?php echo esc_html( $competiscan_w['month'] ); ?></div><div class="day"><?php echo esc_html( $competiscan_w['day'] ); ?></div></div>
                <?php endif; ?>
                <div class="webinar-body">
                  <?php if ( '' !== $competiscan_w['time'] ) : ?>
                  <span class="webinar-time"><?php echo esc_html( $competiscan_w['time'] ); ?></span>
                  <?php endif; ?>
                  <h4><?php echo esc_html( $competiscan_w['title'] ); ?></h4>
                  <?php if ( '' !== $competiscan_w['description'] ) : ?>
                  <p class="webinar-desc"><?php echo nl2br( esc_html( $competiscan_w['description'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
                  <?php endif; ?>
                  <?php if ( $competiscan_w['closed'] ) : ?>
                    <?php if ( '' !== $competiscan_w['closed_text'] ) : ?>
                  <span class="closed"><?php echo esc_html( $competiscan_w['closed_text'] ); ?></span>
                    <?php endif; ?>
                  <?php elseif ( $competiscan_show_button ) : ?>
                  <a href="<?php echo esc_url( $competiscan_w['button_url'] ); ?>" class="link-arrow"<?php echo $competiscan_w['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $competiscan_w['button_label'] ); ?>
                    <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                  </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
<?php endforeach; ?>
          </div>
