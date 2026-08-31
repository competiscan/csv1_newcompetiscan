<?php
/**
 * The nine article cards from insights.html, verbatim.
 *
 * Used when no posts exist yet, so the theme matches the HTML build on activation.
 * Once posts are published the Insights template renders those instead.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';

$articles = array(
	array( 'img' => 'pic-1.png', 'alt' => 'Person paying with credit card on laptop', 'title' => 'Why Banking Reward Programs Are Being Rewritten' ),
	array( 'img' => 'pic-2.png', 'alt' => 'Man working on laptop by window at airport', 'title' => 'Airport Lounge Access, Reimagined' ),
	array( 'img' => 'pic-3.png', 'alt' => 'Bitcoin price chart on screen', 'title' => 'Fixed Indexed Annuities Enter the World of Bitcoin' ),
	array( 'img' => 'pic-4.png', 'alt' => 'Shoppers browsing clothing rack', 'title' => 'Generational Influences on Banking' ),
	array( 'img' => 'pic-5.png', 'alt' => 'Hand holding debit card', 'title' => 'Debit Rewards: A Relic or a Resurgence?' ),
	array( 'img' => 'pic-6.png', 'alt' => "Doctor holding newborn's hand", 'title' => 'Health Insurance in the United States: Legislative Impact 2025 and beyond' ),
	array( 'img' => 'pic-7.png', 'alt' => 'Scanning QR code with phone', 'title' => 'Bridging Physical and Digital: How Mailers Are Using Technology to Drive Response and Engagement' ),
	array( 'img' => 'pic-8.png', 'alt' => 'Email notification icons on phone', 'title' => 'The Evolution of Email Marketing: Navigating Algorithms, Personalization, and Data-Driven' ),
	array( 'img' => 'pic-9.png', 'alt' => 'Man holding phone in bank branch', 'title' => 'PNC Bank — A unique brand identity that promotes being "boring"' ),
);

foreach ( $articles as $article ) :
	?>

      <article class="article-card">
        <div class="article-thumb"><img src="<?php echo esc_url( $img . $article['img'] ); ?>" alt="<?php echo esc_attr( $article['alt'] ); ?>"></div>
        <div class="article-body">
          <span class="tag">Articles</span>
          <h3><a href="#"><?php echo esc_html( $article['title'] ); ?></a></h3>
          <a href="#" class="link-arrow">Read Now
            <?php echo competiscan_arrow_svg(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
          </a>
        </div>
      </article>
	<?php
endforeach;
