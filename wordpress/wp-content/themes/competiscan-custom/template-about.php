<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * 1:1 replica of competiscan-html/About Us.dc.html.
 *
 * The header and footer are the theme's own (get_header/get_footer) and are
 * reused unchanged. Every text block is editable via the "About Page Content"
 * ACF group and falls back to the original copy when empty. The leadership grid
 * is driven by the Team post type (add / edit / remove / reorder in the admin);
 * a static fallback matching the source renders if no team posts exist yet.
 *
 * @package Competiscan_Custom
 */

get_header();

/**
 * Small helper: return an ACF value or a default when empty.
 */
function competiscan_about_f( $name, $default = '' ) {
	$v = get_field( $name );
	return ( '' === $v || null === $v || false === $v ) ? $default : $v;
}

$logomark = get_template_directory_uri() . '/assets/images/logomark-white.png';

$linkedin_svg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="rgb(0,75,129)"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.42v1.56h.05c.48-.9 1.63-1.85 3.36-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.8 0 0 .78 0 1.73v20.53C0 23.22.8 24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.74V1.73C24 .78 23.2 0 22.22 0z"></path></svg>';

// -- Team: prefer the CPT; fall back to the source's static roster. ----------
$team_members = function_exists( 'competiscan_get_team_members' ) ? competiscan_get_team_members() : array();
$team_cards   = array();

if ( ! empty( $team_members ) ) {
	foreach ( $team_members as $m ) {
		$photo = get_field( 'team_photo', $m->ID );
		$img   = '';
		if ( is_array( $photo ) && ! empty( $photo['url'] ) ) {
			$img = $photo['url'];
		} elseif ( is_string( $photo ) ) {
			$img = $photo;
		}
		$fname = get_field( 'team_full_name', $m->ID );
		$team_cards[] = array(
			'name'  => $fname ? $fname : get_the_title( $m->ID ),
			'role'  => get_field( 'team_designation', $m->ID ),
			'bio'   => get_field( 'team_bio', $m->ID ),
			'li'    => get_field( 'team_linkedin', $m->ID ),
			'img'   => $img,
		);
	}
} else {
	$src = home_url( '/competiscan-html/assets/team/' );
	$team_cards = array(
		array( 'name' => 'Richard Goldman', 'role' => 'CEO', 'img' => $src . 'rg2.jpg', 'li' => 'https://www.linkedin.com/in/richgoldmancompetiscan', 'bio' => 'Rich is the leading and longest-tenured expert in collecting competitor direct-marketing communications and materials. He has redefined how companies track competitors while gauging the impact of their own materials in the marketplaces they serve.' ),
		array( 'name' => 'Jim Frisch', 'role' => 'EVP, Business Development', 'img' => $src . 'jf2.jpg', 'li' => 'https://www.linkedin.com/in/jim-frisch-009', 'bio' => "Jim's expertise lies in facilitating the acquisition and retention of clients while driving cultural initiatives across our staff. He has worked with direct-response leaders like Precision Dialogue and Fiserv across financial, retail, insurance, and hospitality." ),
		array( 'name' => 'Bujeta Vokshi', 'role' => 'VP, People & Strategy', 'img' => $src . 'bv2.jpg', 'li' => 'https://www.linkedin.com/in/bujeta-vokshi-567a8a30', 'bio' => 'With 10+ years as a strategic business leader, Bujeta enhances employee performance while building organizational culture that supports thriving teams. She leads human-capital initiatives and strategy, partnering with the executive team to attract, hire, and retain first-class talent.' ),
		array( 'name' => 'Joe Radtke', 'role' => 'VP, Custom Research', 'img' => $src . 'jr2.jpg', 'li' => 'https://www.linkedin.com/in/joeradtke', 'bio' => 'Joe has over 15 years of experience in strategic consulting and market research across financial-services segments. He works closely with Fortune 1000 companies on product benchmarking, go-to-market strategies, competitive profiles, and communication strategies.' ),
		array( 'name' => 'Scott Hoffman', 'role' => 'AVP, Research & Insights, Insurance', 'img' => $src . 'sh2.jpg', 'li' => 'https://www.linkedin.com/in/scotthhoffman05', 'bio' => 'Scott offers more than eight years of market-research experience and manages Health Insurance and Worksite/Voluntary projects at Competiscan, working directly with clients to ensure impactful results.' ),
		array( 'name' => 'Megan Cipperly', 'role' => 'VP, Client Services', 'img' => $src . 'mc2-1.jpg', 'li' => 'https://www.linkedin.com/in/megan-cipperly-39043667', 'bio' => 'Megan has 10+ years researching direct and digital marketing trends. She draws insights from the activity of marketers reaching thousands of consumers across the U.S., leading a team of analysts reporting on trends across financial services and insurance.' ),
		array( 'name' => 'Nate Hart', 'role' => 'SVP, Operations', 'img' => $src . 'nh2.jpg', 'li' => '', 'bio' => "Nate enjoys solving Competiscan's business challenges. He has designed and built multiple products while establishing time- and money-saving processes, and leads a large team driving growth through IT initiatives." ),
		array( 'name' => 'Jessica Duncan', 'role' => 'AVP, Research & Insights, Financial Services', 'img' => $src . 'jd2.jpg', 'li' => 'https://www.linkedin.com/in/jessica-duncan-a372b04/', 'bio' => "With 15+ years in financial services and consumer marketing, Jessica brings deep expertise in marketing strategy and analytics. She previously led a team of Senior Marketing Analysts at PSCU's marketing and consulting division." ),
		array( 'name' => 'Michael Ruffing', 'role' => 'Senior Sales Director', 'img' => $src . 'mr2.jpg', 'li' => 'https://www.linkedin.com/in/michaelruffing/', 'bio' => 'Michael is driven by a passion for market research and helping organizations revolutionize their marketing and product development. As Senior Sales Director he drives new-business growth and strategic partnerships across all industries.' ),
	);
}

// -- Testimonials (ACF repeater or fallback). --------------------------------
$testi = get_field( 'about_testi_items' );
if ( empty( $testi ) ) {
	$testi = array(
		array( 'quote' => 'Competiscan is a reliable source to receive timely competitor intelligence with national and local perspectives. We are very pleased with the results and relationship with Competiscan.', 'author' => 'VP, Strategic Marketing' ),
		array( 'quote' => "Competiscan's database is thorough and easy to search, but what really stands out is their research. The insights team goes the extra mile to understand and respond to our needs.", 'author' => 'Creative Director' ),
	);
}

// FAQ is rendered by the shared layout acf-layouts/cs_faq_accordion.php (below).
?>

<div class="cs-about cs-x1">

  <!-- ===================== HERO ===================== -->
  <section class="cs-x111" id="top">
    <div class="cs-x112">
      <span class="cs-x113"><?php echo esc_html( competiscan_about_f( 'about_hero_eyebrow', 'About Competiscan' ) ); ?></span>
      <h1 class="cs-x114"><?php echo wp_kses_post( competiscan_about_f( 'about_hero_title', 'Your Market &amp; Competitive Insights Partner' ) ); ?></h1>
      <p class="cs-x115"><?php echo esc_html( competiscan_about_f( 'about_hero_text', 'Since 2004, Competiscan has grown into a trusted marketing intelligence partner, delivering the only true omni-channel tracking panels in the marketplace, backed by best-in-class service.' ) ); ?></p>
      <div class="cs-x21">
        <a class="cs-btn-primary cs-x22" data-cs-calendly href="#connect"><?php echo esc_html( competiscan_about_f( 'about_hero_btn1', 'See it in action' ) ); ?> <span class="cs-x23">&rarr;</span></a>
        <a class="cs-btn-outline cs-x24" href="#team"><?php echo esc_html( competiscan_about_f( 'about_hero_btn2', 'Meet the team' ) ); ?></a>
      </div>
    </div>
  </section>

  <!-- ===================== STORY + SERVICE ===================== -->
  <section class="cs-x116">
    <div class="cs-x117">
      <h2 class="cs-x118"><?php echo esc_html( competiscan_about_f( 'about_story_title', 'Our Story' ) ); ?></h2>
      <p class="cs-x43"><?php echo esc_html( competiscan_about_f( 'about_story_body', "Competiscan was born from the idea that market and competitive insights should be easy to access, reliable, and actionable. Since 2004 we've grown into a trusted marketing intelligence partner, delivering the only true omni-channel tracking panels in the marketplace across multiple sectors, empowering organizations with clear, actionable insights." ) ); ?></p>
    </div>
    <div class="cs-x119">
      <img class="cs-x120" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true">
      <h2 class="cs-x121"><?php echo esc_html( competiscan_about_f( 'about_service_title', 'Superior Service' ) ); ?></h2>
      <p class="cs-x122"><?php echo esc_html( competiscan_about_f( 'about_service_body', 'Our team is an extension of yours, and we mean it. Our dedicated insights team is here to equip you with the information you need to stay ahead of the competition.' ) ); ?></p>
    </div>
  </section>

  <!-- ===================== TESTIMONIALS BAND ===================== -->
  <section class="cs-x80">
    <div class="cs-x123"></div>
    <div class="cs-x124">
      <h2 class="cs-x125"><?php echo esc_html( competiscan_about_f( 'about_testi_title', 'Market leaders trust Competiscan' ) ); ?></h2>
      <div class="cs-x126">
        <?php foreach ( $testi as $t ) : ?>
        <div class="cs-x127">
          <div class="cs-x128">&ldquo;</div>
          <p class="cs-x129"><?php echo esc_html( $t['quote'] ); ?></p>
          <div class="cs-x130"><?php echo esc_html( $t['author'] ); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== LEADERSHIP TEAM ===================== -->
  <section class="cs-x131" id="team">
    <div class="cs-x132">
      <h2 class="cs-x133"><?php echo esc_html( competiscan_about_f( 'about_team_title', 'Meet the leadership team' ) ); ?></h2>
      <p class="cs-x43"><?php echo esc_html( competiscan_about_f( 'about_team_text', 'The people who make Competiscan an extension of your team: deep expertise across research, insights, operations, and client service.' ) ); ?></p>
    </div>
    <div class="cs-x134">
      <?php foreach ( $team_cards as $c ) : ?>
      <div class="cs-x135">
        <div class="cs-x136">
          <?php if ( ! empty( $c['img'] ) ) : ?>
          <img class="cs-x137" src="<?php echo esc_url( $c['img'] ); ?>" alt="<?php echo esc_attr( $c['name'] ); ?>" loading="lazy">
          <?php endif; ?>
        </div>
        <div class="cs-x138">
          <h3 class="cs-x139"><?php echo esc_html( $c['name'] ); ?></h3>
          <div class="cs-x140"><?php echo esc_html( $c['role'] ); ?></div>
          <p class="cs-x141"><?php echo esc_html( $c['bio'] ); ?></p>
          <?php if ( ! empty( $c['li'] ) ) : ?>
          <a class="cs-x142" href="<?php echo esc_url( $c['li'] ); ?>" target="_blank" rel="noopener">
            <?php echo $linkedin_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?>
            LinkedIn
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ===================== CTA / CONNECT ===================== -->
  <section class="cs-x64" id="connect">
    <div class="cs-x143">
      <img class="cs-x144" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true">
      <div class="cs-x145">
        <h2 class="cs-x146"><?php echo esc_html( competiscan_about_f( 'about_cta_title', 'Interested in learning more?' ) ); ?> <span class="cs-x83"><?php echo esc_html( competiscan_about_f( 'about_cta_title_accent', "Let's connect." ) ); ?></span></h2>
        <p class="cs-x147"><?php echo esc_html( competiscan_about_f( 'about_cta_text', 'Tell us where you want to gain an edge and our insights team will show you how Competiscan can help.' ) ); ?></p>
      </div>
      <div class="cs-x148">
        <button class="cs-btn-primary cs-x149" data-cs-calendly type="button"><?php echo esc_html( competiscan_about_f( 'about_cta_btn', 'See it in action' ) ); ?></button>
        <?php $cta_email = competiscan_about_f( 'about_cta_email', 'contactus@competiscan.com' ); ?>
        <span class="cs-x150">Or email <a class="cs-x110" href="mailto:<?php echo esc_attr( $cta_email ); ?>"><?php echo esc_html( $cta_email ); ?></a></span>
      </div>
    </div>
  </section>

  <!-- ===================== FAQ ===================== -->
  <?php
  // Reuse the shared FAQ layout, driven by this page's ACF fields so it stays
  // fully editable from the admin (About Page Content → FAQ).
  get_template_part(
    'acf-layouts/cs_faq_accordion',
    null,
    array(
      'variant'     => 'home',
      'title'       => competiscan_about_f( 'about_faq_title', 'Got Questions?' ),
      'description' => competiscan_about_f( 'about_faq_intro', 'Find answers to common questions about Competiscan and how we work with clients.' ),
      'faqs'        => get_field( 'about_faq_items' ),
    )
  );
  ?>

</div>

<?php
get_footer();
