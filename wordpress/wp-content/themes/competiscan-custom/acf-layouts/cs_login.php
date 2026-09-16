<?php
/**
 * Login — Client Access form (flexible-content layout).
 *
 * Markup is a 1:1 match of the source Login page. The form posts to WordPress'
 * native login handler (wp_login_url) with the core field names (log / pwd /
 * rememberme), so the Client Login authenticates real users without a custom
 * handler. Every label, placeholder, button and link is editable via ACF and
 * falls back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = get_sub_field( 'eyebrow' ) ?: 'Client Access';
$heading    = get_sub_field( 'heading' ) ?: 'Login';
$email_l    = get_sub_field( 'email_label' ) ?: 'Email';
$email_ph   = get_sub_field( 'email_placeholder' ) ?: 'you@company.com';
$pass_l     = get_sub_field( 'password_label' ) ?: 'Password';
$pass_ph    = get_sub_field( 'password_placeholder' ) ?: '••••••••';
$remember   = get_sub_field( 'remember_label' ) ?: 'Remember me';
$forgot_l   = get_sub_field( 'forgot_label' ) ?: 'Forgot password?';
$forgot_u   = get_sub_field( 'forgot_url' ) ?: wp_lostpassword_url();
$forgot_t   = get_sub_field( 'forgot_target' ) ?: '_self';
$forgot_r   = get_sub_field( 'forgot_rel' );
$submit_l   = get_sub_field( 'submit_label' ) ?: 'Log in';
$help_text  = get_sub_field( 'help_text' );
if ( '' === $help_text || null === $help_text || false === $help_text ) {
	$help_text = 'Having trouble logging in? Contact <a class="cs-x179" href="mailto:contactus@competiscan.com">contactus@competiscan.com</a>.';
}

$redirect = is_user_logged_in() ? admin_url() : home_url( '/' );
?>
<section class="cs-x336" id="top">
  <div class="cs-x337">
    <div class="cs-x338">
      <span class="cs-x339"><?php echo esc_html( $eyebrow ); ?></span>
      <h1 class="cs-x340"><?php echo esc_html( $heading ); ?></h1>
    </div>

    <div class="cs-x341">
      <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>">
        <label class="cs-x342">
          <span class="cs-x343"><?php echo esc_html( $email_l ); ?></span>
          <input class="cs-x344" type="email" name="log" autocomplete="email" required placeholder="<?php echo esc_attr( $email_ph ); ?>">
        </label>

        <label class="cs-x345">
          <span class="cs-x343"><?php echo esc_html( $pass_l ); ?></span>
          <input class="cs-x344" type="password" name="pwd" autocomplete="current-password" required placeholder="<?php echo esc_attr( $pass_ph ); ?>">
        </label>

        <div class="cs-x346">
          <label class="cs-x347">
            <input class="cs-x348" type="checkbox" name="rememberme" value="forever">
            <?php echo esc_html( $remember ); ?>
          </label>
          <a class="cs-x349" href="<?php echo esc_url( $forgot_u ); ?>" target="<?php echo esc_attr( $forgot_t ); ?>"<?php echo $forgot_r ? ' rel="' . esc_attr( $forgot_r ) . '"' : ''; ?>><?php echo esc_html( $forgot_l ); ?></a>
        </div>

        <input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect ); ?>">
        <button type="submit" class="cs-login-submit cs-x350"><?php echo esc_html( $submit_l ); ?></button>
      </form>
    </div>

    <p class="cs-x351"><?php echo wp_kses_post( $help_text ); ?></p>
  </div>
</section>
