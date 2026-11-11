<?php
/**
 * Document head and the opening <main> wrapper.
 *
 * In the HTML source the <header> lives *inside* the hero section, and the hero
 * wrapper differs per page (.hero on the home page, .insights-hero on inner pages).
 * So this file stops at <main>; each template opens its own hero section and pulls
 * in template-parts/site-header.php. That keeps the DOM identical to the source.
 *
 * @package Competiscan_Custom
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<main<?php echo is_front_page() ? '' : ' class="inner-pages"'; ?>>
<!-- ============ HEADER ============ -->
<header class="site-header">
  <div class="container header-inner">
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="logo">
        <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/logo-primary-color.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo('name') ); ?>">
    </a>
    <?php
    wp_nav_menu(
      array(
        'theme_location' => 'primary',
        'container'      => 'nav',
        'container_class' => 'main-nav',
        'items_wrap'     => '%3$s',
        'depth'          => 2,
        'walker'         => new Competiscan_Nav_Walker(),
        'fallback_cb'    => 'competiscan_primary_nav_fallback',
      )
    );
    ?>

    <div class="header-actions">
      <?php if ( ! empty( $_SESSION['sess_username'] ) ) : ?>
        <!-- Logged In -->
        <div class="nav-dropdown">
          <button class="nav-dropdown__toggle" aria-haspopup="true" aria-expanded="false">
            Profile
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div class="nav-dropdown__menu">
            <a href="<?php echo esc_url( home_url( '/fullsearch.php?searchview=2' ) ); ?>">Power Search</a>
            <a href="<?php echo esc_url( home_url( '/change_password.php' ) ); ?>">Change Password</a>
            <a href="<?php echo esc_url( home_url( '/logout.php' ) ); ?>">Logout</a>
          </div>
        </div>
      <?php else : ?>
        <!-- Logged Out -->
        <a href="<?php echo esc_url( home_url( '/login.php' ) ); ?>" class="btn btn-outline">Client Login</a>
      <?php endif; ?>
      <a href="#" class="btn btn-primary">Contact Us</a>
      <button class="hamburger" aria-label="Open menu"><span></span></button>
    </div>
  </div>
</header>

<!-- ============ MOBILE MENU ============ -->
<div class="mobile-menu">
  <div class="mobile-menu-top">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.png')" alt="logo">
    </a>
    <button class="mobile-menu-close" aria-label="Close menu"></button>
  </div>
  <?php
  wp_nav_menu(
    array(
      'theme_location' => 'primary',
      'container'      => false,
      'menu_class'     => 'mobile-nav-list',
      'items_wrap'     => '<ul class="mobile-nav-list">%3$s</ul>',
      'depth'          => 2,
      'walker'         => new Competiscan_Mobile_Nav_Walker(),
      'fallback_cb'    => 'competiscan_mobile_nav_fallback',
    )
  );
  ?>
  <div class="header-actions">
   <?php if ( ! empty( $_SESSION['sess_username'] ) ) : ?>
      <a href="<?php echo esc_url( home_url( '/fullsearch.php?searchview=2' ) ); ?>">Power Search</a>
      <a href="<?php echo esc_url( home_url( '/change_password.php' ) ); ?>" class="btn btn-outline">Change Password</a>
      <a href="<?php echo esc_url( home_url( '/logout.php' ) ); ?>" class="btn btn-outline">Logout</a>
    <?php else : ?>
      <a href="<?php echo esc_url( home_url( '/login.php' ) ); ?>" class="btn btn-outline">Client Login</a>
    <?php endif; ?>
    <a href="#" class="btn btn-primary">Contact Us</a>
  </div>
</div>
