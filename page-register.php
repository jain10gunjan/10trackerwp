<?php
/**
 * Custom registration page.
 *
 * @package TenTracker
 */

if ( is_user_logged_in() ) {
    wp_safe_redirect( home_url( '/dashboard/' ) );
    exit;
}

get_header();
?>

<main class="tt-register-page" id="main">
  <div class="tt-register-shell">
    <section class="tt-register-hero" aria-labelledby="register-heading">
      <p class="tt-register-hero__eyebrow"><?php esc_html_e( 'Join 10Tracker', 'tentracker' ); ?></p>
      <h1 class="tt-register-hero__title" id="register-heading"><?php esc_html_e( 'Create your free practice account', 'tentracker' ); ?></h1>
      <p class="tt-register-hero__text"><?php esc_html_e( 'Save attempts, track progress, and continue practicing from any device.', 'tentracker' ); ?></p>

      <div class="tt-register-benefits">
        <span><?php esc_html_e( 'Instant access', 'tentracker' ); ?></span>
        <span><?php esc_html_e( 'Progress dashboard', 'tentracker' ); ?></span>
        <span><?php esc_html_e( 'Mobile friendly', 'tentracker' ); ?></span>
      </div>
    </section>

    <section class="tt-register-card" aria-label="<?php esc_attr_e( 'Registration form', 'tentracker' ); ?>">
      <form class="tt-register-form" data-tt-register-form novalidate>
        <div class="tt-register-form__notice" data-tt-register-notice hidden></div>

        <label class="tt-register-field">
          <span><?php esc_html_e( 'Full Name', 'tentracker' ); ?></span>
          <input type="text" name="full_name" autocomplete="name" required>
          <em data-error-for="full_name"></em>
        </label>

        <label class="tt-register-field">
          <span><?php esc_html_e( 'Email', 'tentracker' ); ?></span>
          <input type="email" name="email" autocomplete="email" required>
          <em data-error-for="email"></em>
        </label>

        <label class="tt-register-field">
          <span><?php esc_html_e( 'Password', 'tentracker' ); ?></span>
          <input type="password" name="password" autocomplete="new-password" minlength="8" required>
          <em data-error-for="password"></em>
        </label>

        <label class="tt-register-field">
          <span><?php esc_html_e( 'Confirm Password', 'tentracker' ); ?></span>
          <input type="password" name="confirm_password" autocomplete="new-password" minlength="8" required>
          <em data-error-for="confirm_password"></em>
        </label>

        <button class="tt-register-submit" type="submit" data-tt-register-submit>
          <?php esc_html_e( 'Create Account', 'tentracker' ); ?>
        </button>

        <p class="tt-register-login">
          <?php esc_html_e( 'Already have an account?', 'tentracker' ); ?>
          <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Log in', 'tentracker' ); ?></a>
        </p>
      </form>
    </section>
  </div>
</main>

<?php get_footer();
