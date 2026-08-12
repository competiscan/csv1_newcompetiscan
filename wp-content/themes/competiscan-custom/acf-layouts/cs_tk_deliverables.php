<?php
/**
 * AI Toolkit — Deliverables Carousel (flexible-content layout).
 *
 * Fields: title, intro, caption, slides (repeater: image, title, subtitle,
 * lead, bullets). Crossfade carousel behaviour lives in assets/js/cs-site.js
 * ([data-cs-crsl]). Falls back to the source copy/images.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = get_sub_field( 'title' ) ?: 'What you get';
$intro   = get_sub_field( 'intro' ) ?: 'Every run produces polished, presentation-ready documents, generated with your logo and brand colors applied automatically.';
$caption = get_sub_field( 'caption' ) ?: 'One upload. Instant reports. Zero manual work.';

$check = '<svg class="cs-x57" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgb(0,171,171)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>';

$slides       = get_sub_field( 'slides' );
$deliverables = array();

/*
 * Get slides from ACF repeater.
 */
if ( ! empty( $slides ) ) {

	foreach ( $slides as $s ) {

		$bullets = array();

		foreach ( preg_split( '/\r\n|\r|\n/', (string) $s['bullets'] ) as $line ) {

			$line = trim( $line );

			if ( '' !== $line ) {
				$bullets[] = $line;
			}
		}

		/*
		 * ACF image field can return:
		 * - URL
		 * - Array
		 * - Attachment ID
		 */
		$image = '';

		if ( ! empty( $s['image'] ) ) {

			if ( is_array( $s['image'] ) && ! empty( $s['image']['url'] ) ) {

				$image = $s['image']['url'];

			} elseif ( is_numeric( $s['image'] ) ) {

				$image = wp_get_attachment_image_url(
					$s['image'],
					'full'
				);

			} else {

				$image = $s['image'];
			}
		}

		$deliverables[] = array(
			'img'  => $image,
			'h'    => ! empty( $s['title'] ) ? $s['title'] : '',
			'sub'  => ! empty( $s['subtitle'] ) ? $s['subtitle'] : '',
			'lead' => ! empty( $s['lead'] ) ? $s['lead'] : '',
			'b'    => $bullets,
		);
	}

} else {

	/*
	 * FALLBACK IMAGE PATH
	 *
	 * Physical location:
	 *
	 * competiscan-wp/
	 * └── wp-content/
	 *     └── themes/
	 *         └── competiscan-custom/
	 *             └── assets/
	 *                 └── images/
	 *                     ├── report1.png
	 *                     ├── slide1.png
	 *                     └── report2.png
	 *
	 * Browser URL:
	 *
	 * /wp-content/themes/competiscan-custom/assets/images/
	 */

	$images = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/';

	$deliverables = array(

		/*
		 * Slide 1
		 */
		array(
			'img'  => $images . 'report1.png',
			'h'    => 'DME Report',
			'sub'  => 'Your creative execution, scored and explained.',
			'lead' => 'A full written analysis of every asset you upload. For each creative, the report includes:',
			'b'    => array(

				'<strong class="cs-x58">Category scores on a 1&ndash;10 scale</strong> across 10 weighted dimensions, from visual design quality and storytelling to call-to-action clarity, ease of response, and audience fit.',

				'<strong class="cs-x58">Channel-aware weighting</strong>: an email is not judged like a mailer. Subject lines matter for email, the first 5 seconds matter for video, response friction matters for direct mail.',

				'<strong class="cs-x58">Detailed critiques</strong> explaining every score: what&#x27;s working, what&#x27;s holding the piece back, and how it compares to benchmark entries from the Competiscan archive.',
			),
		),

		/*
		 * Slide 2
		 */
		array(
			'img'  => $images . 'slide1.png',
			'h'    => 'DME Presentation',
			'sub'  => 'Your results, ready to present.',
			'lead' => 'Skip the deck-building. Compass turns the DME analysis into a branded PowerPoint automatically:',
			'b'    => array(

				'<strong class="cs-x58">One slide per creative</strong> with the campaign visual, title, and its performance tier (High / Average / Low performing).',

				'A <strong class="cs-x58">&ldquo;Category Scores by Campaign&rdquo;</strong> comparison slide that puts all your entries side by side.',

				'Generated with <strong class="cs-x58">your logo and brand colors</strong>, ready to drop into your next stakeholder meeting.',
			),
		),

		/*
		 * Slide 3
		 */
		array(
			'img'  => $images . 'report2.png',
			'h'    => 'CRE Report',
			'sub'  => 'Your offer vs. the real market.',
			'lead' => "A competitive positioning analysis that compares your product offer against a universe of real competitor products drawn from Competiscan's proprietary database, filtered to your sector and audience. Inside:",
			'b'    => array(

				'An <strong class="cs-x58">overall competitiveness score</strong> with section-by-section breakdowns.',

				'<strong class="cs-x58">Pricing competitiveness</strong>: exactly where your pricing wins and where it loses.',

				'<strong class="cs-x58">Rewards program positioning</strong> against live competitor offers.',

				'<strong class="cs-x58">Strategic insights and differentiators</strong> you can take straight into product and marketing decisions.',
			),
		),
	);
}
?>

<section class="cs-x33" id="deliverables">

	<!-- Section Heading -->
	<div class="cs-x41">

		<h2 class="cs-x42">
			<?php echo esc_html( $title ); ?>
		</h2>

		<p class="cs-x43">
			<?php echo esc_html( $intro ); ?>
		</p>

	</div>


	<!-- Carousel -->
	<div class="cs-x44">

		<!-- Previous Button -->
		<button
			class="cs-x45"
			data-cs-crsl-prev
			type="button"
			aria-label="Previous deliverable"
		>

			<svg
				width="20"
				height="20"
				viewBox="0 0 24 24"
				fill="none"
				stroke="currentColor"
				stroke-width="3"
				stroke-linecap="round"
				stroke-linejoin="round"
				aria-hidden="true"
			>
				<path d="M19 12H5"></path>
				<path d="m12 19-7-7 7-7"></path>
			</svg>

		</button>


		<!-- Slides -->
		<div
			class="cs-x46"
			data-cs-crsl
		>

			<?php foreach ( $deliverables as $di => $d ) : ?>

				<article
					class="<?php echo 0 === $di ? 'cs-x47' : 'cs-x59'; ?>"
					data-cs-crsl-slide
					aria-hidden="<?php echo 0 === $di ? 'false' : 'true'; ?>"
				>

					<div class="cs-x48">

						<!-- Image -->
						<div class="cs-x49">

							<?php if ( ! empty( $d['img'] ) ) : ?>

								<img
									class="cs-x50"
									src="<?php echo esc_url( $d['img'] ); ?>"
									alt="<?php echo esc_attr( $d['h'] ); ?>"
								>

							<?php endif; ?>

						</div>


						<!-- Content -->
						<div class="cs-x51">

							<h3 class="cs-x52">
								<?php echo esc_html( $d['h'] ); ?>
							</h3>

							<p class="cs-x53">
								<?php echo esc_html( $d['sub'] ); ?>
							</p>

							<p class="cs-x54">
								<?php echo esc_html( $d['lead'] ); ?>
							</p>


							<?php if ( ! empty( $d['b'] ) ) : ?>

								<ul class="cs-x55">

									<?php foreach ( $d['b'] as $bullet ) : ?>

										<li class="cs-x56">

											<?php
											echo $check; // phpcs:ignore WordPress.Security.EscapeOutput
											?>

											<span>
												<?php echo wp_kses_post( $bullet ); ?>
											</span>

										</li>

									<?php endforeach; ?>

								</ul>

							<?php endif; ?>

						</div>

					</div>

				</article>

			<?php endforeach; ?>

		</div>


		<!-- Next Button -->
		<button
			class="cs-x45"
			data-cs-crsl-next
			type="button"
			aria-label="Next deliverable"
		>

			<svg
				width="20"
				height="20"
				viewBox="0 0 24 24"
				fill="none"
				stroke="currentColor"
				stroke-width="3"
				stroke-linecap="round"
				stroke-linejoin="round"
				aria-hidden="true"
			>

				<path d="M5 12h14"></path>

				<path d="m12 5 7 7-7 7"></path>

			</svg>

		</button>

	</div>


	<!-- Dots -->
	<div class="cs-x60">

		<?php foreach ( $deliverables as $di => $d ) : ?>

			<button
				class="<?php echo 0 === $di ? 'cs-x61' : 'cs-x62'; ?>"
				data-cs-crsl-dot
				type="button"
				aria-label="Go to slide <?php echo (int) $di + 1; ?>"
			></button>

		<?php endforeach; ?>

	</div>


	<!-- Caption -->
	<p class="cs-x63">
		<?php echo esc_html( $caption ); ?>
	</p>

</section>