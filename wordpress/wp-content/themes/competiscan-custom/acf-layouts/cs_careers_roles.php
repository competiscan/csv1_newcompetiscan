<?php
/**
 * Careers — Open Roles accordion (flexible-content layout).
 *
 * Role cards are rendered dynamically from the Career post type, so adding a
 * Career post updates this listing automatically. The heading, intro and footer
 * note are editable; the accordion behaviour is the shared one in cs-site.js.
 *
 * IMPORTANT: the Career fields are read with get_post_meta (not get_field) on
 * purpose. This layout runs inside template-cms.php's have_rows('cms_content')
 * loop, and calling ACF's get_field on other posts inside that loop corrupts the
 * flexible-content row pointer (which would drop the sections after this one).
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' ) ?: 'Open roles';
$intro   = get_sub_field( 'intro' ) ?: "We're growing our financial services insights team. Find the role that fits and reach out. We'd love to meet you.";
$note    = get_sub_field( 'footer_note' ) ?: 'Interested candidates should email a resume to the attention of <strong class="cs-x179">Bujeta Vokshi</strong> at <a class="cs-x110" href="mailto:contactus@competiscan.com">contactus@competiscan.com</a>.';

$careers = function_exists( 'competiscan_get_careers' ) ? competiscan_get_careers() : array();
$pin     = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="rgb(0,171,171)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>';

/**
 * Read an ACF repeater's single-subfield values via raw meta (loop-safe).
 */
$repeater_items = function ( $pid, $field, $sub ) {
	$out   = array();
	$count = (int) get_post_meta( $pid, $field, true );
	for ( $k = 0; $k < $count; $k++ ) {
		$v = get_post_meta( $pid, $field . '_' . $k . '_' . $sub, true );
		if ( '' !== $v ) {
			$out[] = $v;
		}
	}
	return $out;
};
?>
<section class="cs-x161" id="roles">
  <div class="cs-x162">
    <h2 class="cs-x133"><?php echo esc_html( $heading ); ?></h2>
    <p class="cs-x43"><?php echo esc_html( $intro ); ?></p>
  </div>
  <?php if ( ! empty( $careers ) ) : ?>
  <div class="cs-x163">
    <?php
    foreach ( $careers as $i => $career_post ) :
      $pid       = $career_post->ID;
      $title     = get_the_title( $pid );
      $location  = get_post_meta( $pid, 'career_location', true );
      $desc      = get_post_meta( $pid, 'career_description', true );
      $duties    = $repeater_items( $pid, 'career_duties', 'item' );
      $skills    = $repeater_items( $pid, 'career_skills', 'item' );
      $apply_lbl = get_post_meta( $pid, 'career_apply_label', true );
      $apply_lbl = '' !== $apply_lbl ? $apply_lbl : 'Apply for this role';
      $apply_url = get_post_meta( $pid, 'career_apply_url', true );
      $apply_url = '' !== $apply_url ? $apply_url : ( 'mailto:contactus@competiscan.com?subject=Application: ' . $title );
      $open      = ( 0 === $i );
      ?>
    <div class="cs-x164">
      <button class="cs-x165" data-cs-acc-btn="roles" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
        <div class="cs-x166">
          <h3 class="cs-x167"><?php echo esc_html( $title ); ?></h3>
          <?php if ( $location ) : ?>
          <div class="cs-x168"><?php echo $pin; // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( $location ); ?></div>
          <?php endif; ?>
        </div>
        <span class="cs-x169"><?php echo $open ? '&minus;' : '+'; ?></span>
      </button>
      <div class="cs-x170" data-cs-acc-panel="" <?php echo $open ? '' : 'hidden'; ?>>
        <?php if ( $desc ) : ?><p class="cs-x171"><?php echo esc_html( $desc ); ?></p><?php endif; ?>
        <div class="cs-x172">
          <?php if ( ! empty( $duties ) ) : ?>
          <div>
            <div class="cs-x173">Duties &amp; responsibilities</div>
            <ul class="cs-x55">
              <?php foreach ( $duties as $d ) : ?>
              <li class="cs-x174"><span class="cs-x175"></span><span><?php echo esc_html( $d ); ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
          <?php if ( ! empty( $skills ) ) : ?>
          <div>
            <div class="cs-x173">Skills &amp; experience</div>
            <ul class="cs-x55">
              <?php foreach ( $skills as $s ) : ?>
              <li class="cs-x174"><span class="cs-x176"></span><span><?php echo esc_html( $s ); ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
        </div>
        <a class="cs-btn-primary cs-x177" href="<?php echo esc_url( $apply_url ); ?>"><?php echo esc_html( $apply_lbl ); ?> <span class="cs-x23">&rarr;</span></a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php if ( $note ) : ?>
  <p class="cs-x178"><?php echo wp_kses_post( $note ); ?></p>
  <?php endif; ?>
</section>
