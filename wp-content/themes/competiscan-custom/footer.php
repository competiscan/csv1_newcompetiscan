<?php
/**
 * Site footer, the closing </main>, and wp_footer().
 *
 * Markup is carried over from the HTML source unchanged; only the logo path, the
 * two link columns, and the copyright line are dynamic.
 *
 * @package Competiscan_Custom
 */

// Footer company information — all editable via Site Options (ACF). Values fall
// back gracefully so the footer never renders broken or empty markup.
$competiscan_has_acf   = function_exists( 'get_field' );
$competiscan_logo      = $competiscan_has_acf ? get_field( 'footer_logo', 'option' ) : '';
if ( empty( $competiscan_logo ) ) {
	$competiscan_logo = get_template_directory_uri() . '/assets/images/logo.png';
}
$competiscan_logo_link = $competiscan_has_acf ? get_field( 'footer_logo_link', 'option' ) : '';
if ( empty( $competiscan_logo_link ) ) {
	$competiscan_logo_link = home_url( '/' );
}
$competiscan_address    = $competiscan_has_acf ? get_field( 'footer_address', 'option' ) : '';
$competiscan_phone      = $competiscan_has_acf ? get_field( 'footer_phone', 'option' ) : '';
$competiscan_phone_link = $competiscan_has_acf ? get_field( 'footer_phone_link', 'option' ) : '';
$competiscan_email      = $competiscan_has_acf ? get_field( 'footer_email', 'option' ) : '';

// Build the address/phone/email block, including only the pieces that have a value.
$competiscan_contact = array();
if ( ! empty( $competiscan_address ) ) {
	$competiscan_contact[] = nl2br( esc_html( $competiscan_address ) );
}
if ( ! empty( $competiscan_phone ) ) {
	$competiscan_tel       = ! empty( $competiscan_phone_link ) ? $competiscan_phone_link : $competiscan_phone;
	$competiscan_tel       = preg_replace( '/[^0-9+]/', '', $competiscan_tel );
	$competiscan_contact[] = '<a class="footer-tel" href="tel:' . esc_attr( $competiscan_tel ) . '">' . esc_html( $competiscan_phone ) . '</a>';
}
if ( ! empty( $competiscan_email ) ) {
	$competiscan_contact[] = '<a href="mailto:' . esc_attr( $competiscan_email ) . '">' . esc_html( $competiscan_email ) . '</a>';
}
?>
<!-- ============ FOOTER ============ -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-top">
      <div class="footer-col footer-logo-col">
        <?php if ( ! empty( $competiscan_logo ) ) : ?>
        <a class="footer-logo" href="<?php echo esc_url( $competiscan_logo_link ); ?>">
          <img src="<?php echo esc_url( $competiscan_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
        </a>
        <?php endif; ?>
        <?php
        if ( ! empty( $competiscan_contact ) ) {
          echo '<p>' . implode( '<br>', $competiscan_contact ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- each part escaped above.
        }
        ?>
      </div>
      <div class="footer-col">
        <h5>Solutions</h5>
        <?php
        wp_nav_menu(
          array(
            'theme_location' => 'footer_solutions',
            'container'      => false,
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
            'fallback_cb'    => 'competiscan_footer_solutions_fallback',
          )
        );
        ?>
      </div>
      <div class="footer-col">
        <h5>Company</h5>
        <?php
        wp_nav_menu(
          array(
            'theme_location' => 'footer_company',
            'container'      => false,
            'items_wrap'     => '<ul>%3$s</ul>',
            'depth'          => 1,
            'fallback_cb'    => 'competiscan_footer_company_fallback',
          )
        );
        ?>
      </div>
      <div class="footer-col">
        <h5>Connect</h5>
        <div class="footer-social">
          <?php foreach ( competiscan_social_links() as $competiscan_social ) : ?>
          <a href="<?php echo esc_url( $competiscan_social['url'] ); ?>" aria-label="<?php echo esc_attr( $competiscan_social['label'] ); ?>" target="_blank" rel="noopener">
            <?php echo $competiscan_social['svg']; // phpcs:ignore WordPress.Security.EscapeOutput -- static, trusted SVG markup. ?>
            <span><?php echo esc_html( $competiscan_social['label'] ); ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
      <span>ⓒ</span>
      <a href="#" class="back-to-top">Back to top ↑</a>
    </div>
  </div>
</footer>
</main>
<?php wp_footer(); ?>
</body>
</html>
