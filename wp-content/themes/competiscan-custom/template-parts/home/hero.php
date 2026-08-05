<?php
/**
 * Home hero. The site header renders inside this section, as in the HTML source.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';
?>
<!-- ============ HERO ============ -->
<section class="hero">
  <?php get_template_part( 'template-parts/site-header' ); ?>

  <div class="container hero-grid">
    <div class="hero-copy">
      <h1 class="hero-heading">
        Your Single <br> Source for Market <br> and Competitive <br> Insights
      </h1>
      <p>Competiscan transforms direct and digital marketing activity across the marketplace into clear, actionable insights, backed by best-in-class service along with the largest omni-channel consumer, business owner, and advisor/broker panels.</p>
      <form class="hero-form" onsubmit="return false;">
        <input type="email" placeholder="Enter work email" required>
        <button type="submit" class="btn btn-primary">Request a demo
          <svg width="14" height="10" viewBox="0 0 16 12" fill="none"><path d="M1 6H15M15 6L10 1M15 6L10 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </form>
    </div>
    <div class="hero-media">
      <img src="<?php echo esc_url( $img . 'hero-image.jpg' ); ?>" class="media-img" alt="Financial advisor reviewing insights dashboard">
      <div class="float-card card-top">
          <svg width="27" height="23" viewBox="0 0 27 23" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 3.26153C0 1.47805 1.42678 0.000307679 3.26122 0.000307679H22.8285C24.612 0.000307679 26.0898 1.47805 26.0898 3.26153V19.5676C26.0898 21.4021 24.612 22.8289 22.8285 22.8289H3.26122C1.42678 22.8289 0 21.4021 0 19.5676V3.26153ZM3.26122 4.89214C3.26122 5.80936 3.97461 6.52275 4.89183 6.52275C5.75809 6.52275 6.52244 5.80936 6.52244 4.89214C6.52244 4.02588 5.75809 3.26153 4.89183 3.26153C3.97461 3.26153 3.26122 4.02588 3.26122 4.89214ZM22.8285 4.89214C22.8285 4.2297 22.268 3.66918 21.6056 3.66918H9.37601C8.66262 3.66918 8.15305 4.2297 8.15305 4.89214C8.15305 5.60553 8.66262 6.1151 9.37601 6.1151H21.6056C22.268 6.1151 22.8285 5.60553 22.8285 4.89214Z" fill="#004B81"/>
          </svg>
          Media: <strong>Direct Mail</strong></div>
      <div class="float-card card-banking">
        <div class="card-dot blue"></div>
        <div class="cnt">
          <span class="arrow">›</span>
          <h4>Banking</h4>
          <p>
            Increase loyalty from acquisition through onboarding and retention
          </p>
          <div class="tags">
              <span>Acquisition</span>
              <span>Loyalty</span>
          </div>
        </div>
      </div><!---->

      <div class="float-card card-insurance">
          <div class="card-dot orange"></div>
          <div class="cnt">
            <span class="arrow">›</span>
            <h4>Insurance</h4>
            <p>
                Monitor for new policies, rate changes, and channel preference.
            </p>
            <div class="tags">
                <span>Product Updates</span>
            </div>
          </div>
      </div>

      <div class="float-card card-consumer">
        <div class="card-dot green"></div>
        <div class="cnt">
            <span class="arrow">›</span>
          <h4>Consumer Services</h4>
          <p>
              Discover marketing volume, digital impressions and campaign learning.
          </p>
          <div class="tags">
              <a href="#">My Email</a>
          </div>
        </div>
      </div>
      <div class="float-card card-percent">
        <span class="ring">
          <img src="<?php echo esc_url( $img . 'donut-chart.svg' ); ?>" alt="donut-chart">
        </span>
        <strong>Channel Utilization:</strong>
        Direct Mail vs. Digital
      </div>
      <div class="float-card card-audience">
        <svg width="33" height="27" viewBox="0 0 33 27" fill="none" xmlns=" http://www.w3.org/2000/svg">
          <path d="M7.33775 -0.000419855C8.76453 -0.000419855 10.0894 0.814885 10.8538 2.03784C11.5671 3.31176 11.5671 4.89141 10.8538 6.11437C10.0894 7.38828 8.76453 8.15263 7.33775 8.15263C5.86001 8.15263 4.53514 7.38828 3.77079 6.11437C3.05739 4.89141 3.05739 3.31176 3.77079 2.03784C4.53514 0.814885 5.86001 -0.000419855 7.33775 -0.000419855ZM26.0898 -0.000419855C27.5165 -0.000419855 28.8414 0.814885 29.6058 2.03784C30.3192 3.31176 30.3192 4.89141 29.6058 6.11437C28.8414 7.38828 27.5165 8.15263 26.0898 8.15263C24.612 8.15263 23.2872 7.38828 22.5228 6.11437C21.8094 4.89141 21.8094 3.31176 22.5228 2.03784C23.2872 0.814885 24.612 -0.000419855 26.0898 -0.000419855ZM0 15.2356C0 12.2292 2.39496 9.78324 5.4014 9.78324H7.59253C8.40783 9.78324 9.17218 9.98707 9.88558 10.2928C9.78366 10.6495 9.78366 11.0572 9.78366 11.4139C9.78366 13.4012 10.599 15.1337 11.9748 16.3057C11.9748 16.3057 11.9748 16.3057 11.9238 16.3057H1.07009C0.458609 16.3057 0 15.8471 0 15.2356ZM20.6374 16.3057H20.5865C21.9623 15.1337 22.7776 13.4012 22.7776 11.4139C22.7776 11.0572 22.7776 10.6495 22.7266 10.2928C23.3891 9.98707 24.1534 9.78324 24.9687 9.78324H27.1599C30.1663 9.78324 32.6122 12.2292 32.6122 15.2356C32.6122 15.8471 32.1026 16.3057 31.4912 16.3057H20.6374ZM11.4143 11.4139C11.4143 9.68133 12.3315 8.10168 13.8602 7.18446C15.3379 6.3182 17.2233 6.3182 18.752 7.18446C20.2298 8.10168 21.1979 9.68133 21.1979 11.4139C21.1979 13.1973 20.2298 14.777 18.752 15.6942C17.2233 16.5605 15.3379 16.5605 13.8602 15.6942C12.3315 14.777 11.4143 13.1973 11.4143 11.4139ZM6.52244 24.7645C6.52244 20.9937 9.52888 17.9363 13.2997 17.9363H19.2616C23.0324 17.9363 26.0898 20.9937 26.0898 24.7645C26.0898 25.4779 25.4783 26.0893 24.7139 26.0893H7.84731C7.13392 26.0893 6.52244 25.5288 6.52244 24.7645Z" fill="#004B81"/>
        </svg>

       Audience: <strong>Financial Advisors</strong></div>
    </div>
  </div>
</section>
