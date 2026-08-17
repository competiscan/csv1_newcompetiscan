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
      <?php
      $competiscan_login_page = get_page_by_path( 'client-login' );
      $competiscan_login_url  = $competiscan_login_page ? get_permalink( $competiscan_login_page ) : home_url( '/client-login/' );
      ?>
      <a href="https://competiscan.com/login.php"  class="btn btn-outline">Client Login</a>
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
</div>