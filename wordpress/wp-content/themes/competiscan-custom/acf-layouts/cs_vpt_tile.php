<?php
/**
 * Value Prop Trackers — Feature tile + dashboard mock (flexible-content layout).
 *
 * Copy (badge, title, description, bullets, button, footnote) and the mock
 * dashboard (title, filter chips, table rows) are all editable; falls back to
 * the source content. Row accent colours and alternating row styles are applied
 * by index to match the source.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logomark = get_template_directory_uri() . '/assets/images/logomark-white.png';
$badge    = get_sub_field( 'badge' ) ?: 'Credit cards';
$title    = get_sub_field( 'title' ) ?: 'Unlock insights with the credit card value prop tracker';
$desc     = get_sub_field( 'description' ) ?: 'Track dozens of data points across 600+ credit cards over time, filter and sort by card type, issuer, and media channel, and understand pricing and incentive trends in real time.';

$bullets = get_sub_field( 'bullets' );
if ( ! empty( $bullets ) ) {
	$bullets = wp_list_pluck( $bullets, 'text' );
} else {
	$bullets = array(
		'Compare regular and intro APR, fees, and rewards side-by-side',
		'Filter by issuer, card type, industry, and risk group',
		'See offer trends across direct mail, email, and web in one dashboard',
		'<strong>AI-powered chat feature</strong>: ask questions, set up custom tables, and more, directly within the dashboard',
	);
}

$btn_label = get_sub_field( 'btn_label' ) ?: 'Explore the tracker';
$btn_url   = get_sub_field( 'btn_url' ) ?: 'mailto:contactus@competiscan.com?subject=Value Prop Tracker demo';
$btn_tgt   = get_sub_field( 'btn_target' ) ?: '_self';
$btn_rel   = get_sub_field( 'btn_rel' );
$footnote  = get_sub_field( 'footnote' );
if ( '' === $footnote || null === $footnote || false === $footnote ) {
	$footnote = 'Value Prop Trackers are also available for <strong class="cs-x372">deposits</strong>, <strong class="cs-x372">retail</strong>, and <strong class="cs-x372">travel loyalty</strong> programs.';
}

$table_label = get_sub_field( 'table_label' ) ?: 'Value Prop Tracker';
$filters     = get_sub_field( 'filters' );
$filters     = ! empty( $filters ) ? wp_list_pluck( $filters, 'label' ) : array( 'Card Type: All', 'Issuer: All', 'Reward Type: All', 'Annual Fee Yr 2+: All' );

$rows = get_sub_field( 'rows' );
if ( empty( $rows ) ) {
	$rows = array(
		array( 'name' => 'Travel Rewards Visa', 'issuer' => 'Bank of America', 'type' => 'Cobrand', 'apr' => '18.24–28.24%', 'fee' => '$0' ),
		array( 'name' => 'Strata Premier Card', 'issuer' => 'Citibank', 'type' => 'GPCC', 'apr' => '20.24–28.24%', 'fee' => '$95' ),
		array( 'name' => 'Bonvoy Brilliant', 'issuer' => 'American Express', 'type' => 'Cobrand', 'apr' => '20.24%', 'fee' => '$650' ),
		array( 'name' => 'Secured Card', 'issuer' => 'Capital One', 'type' => 'Secured', 'apr' => '29.24%', 'fee' => '$0' ),
		array( 'name' => 'Points Plus Card', 'issuer' => 'AT&T / Synchrony', 'type' => 'PLCC', 'apr' => '28.24%', 'fee' => '$0' ),
	);
}
$dot_classes = array( 'cs-x390', 'cs-x395', 'cs-x396', 'cs-x397', 'cs-x398' );
?>
<section class="cs-x33">
  <div class="cs-x359">
    <div class="cs-x235"><img class="cs-x236" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true"></div>
    <div class="cs-2col cs-x360">

      <div class="cs-x361">
        <div class="cs-x362">
          <span class="cs-x363"></span>
          <span class="cs-x364"><?php echo esc_html( $badge ); ?></span>
        </div>
        <h2 class="cs-x365"><?php echo esc_html( $title ); ?></h2>
        <p class="cs-x366"><?php echo esc_html( $desc ); ?></p>
        <ul class="cs-x367">
          <?php foreach ( $bullets as $b ) : ?>
          <li class="cs-x368">
            <svg class="cs-x369" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgb(0,171,171)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span><?php echo wp_kses_post( $b ); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <a class="cs-btn-white cs-x370" data-cs-calendly href="<?php echo esc_url( $btn_url ); ?>" target="<?php echo esc_attr( $btn_tgt ); ?>"<?php echo $btn_rel ? ' rel="' . esc_attr( $btn_rel ) . '"' : ''; ?>><?php echo esc_html( $btn_label ); ?> <span class="cs-x23">&rarr;</span></a>
        <p class="cs-x371"><?php echo wp_kses_post( $footnote ); ?></p>
      </div>

      <div class="cs-vpt-mockcol cs-x373">
        <div class="cs-x374">
          <div class="cs-x375">
            <div class="cs-x376"><span class="cs-x377"></span><span class="cs-x377"></span><span class="cs-x377"></span></div>
            <span class="cs-x378"><?php echo esc_html( $table_label ); ?></span>
          </div>
          <div class="cs-x379">
            <?php foreach ( $filters as $fi => $filter ) : ?>
            <span class="<?php echo ( count( $filters ) - 1 === $fi ) ? 'cs-x381' : 'cs-x380'; ?>"><?php echo esc_html( $filter ); ?></span>
            <?php endforeach; ?>
          </div>
          <table class="cs-x382">
            <thead>
              <tr class="cs-x383">
                <th class="cs-x384">Card Name</th>
                <th class="cs-x385">Issuer</th>
                <th class="cs-x385">Type</th>
                <th class="cs-x385">Reg. APR</th>
                <th class="cs-x386">Annual Fee</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ( $rows as $ri => $row ) : ?>
              <tr class="<?php echo ( 0 === $ri % 2 ) ? 'cs-x387' : 'cs-x394'; ?>">
                <td class="cs-x388"><div class="cs-x389"><span class="<?php echo esc_attr( $dot_classes[ $ri % count( $dot_classes ) ] ); ?>"></span><span class="cs-x391"><?php echo esc_html( $row['name'] ); ?></span></div></td>
                <td class="cs-x392"><?php echo esc_html( $row['issuer'] ); ?></td>
                <td class="cs-x392"><?php echo esc_html( $row['type'] ); ?></td>
                <td class="cs-x392"><?php echo esc_html( $row['apr'] ); ?></td>
                <td class="cs-x393"><?php echo esc_html( $row['fee'] ); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
