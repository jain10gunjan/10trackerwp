<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ══ SITE HEADER ══════════════════════════════════════════ -->
<header class="tt-header" role="banner">
  <div class="tt-container tt-header__inner">

    <!-- Logo -->
    <?php tt_the_logo(); ?>

    <!-- Primary nav -->
    <?php tt_primary_nav(); ?>

    <!-- Search -->
    <div class="tt-header__search" role="search">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search exams…', 'tentracker' ); ?>"
               value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
      </form>
    </div>

    <!-- Auth buttons -->
    <div class="tt-header__actions">
      <?php if ( is_user_logged_in() ) : ?>
        <a href="<?php echo esc_url( home_url( '/my-attempts' ) ); ?>" class="tt-btn tt-btn--secondary tt-btn--sm">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
          </svg>
          <?php esc_html_e( 'My Progress', 'tentracker' ); ?>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url( wp_login_url() ); ?>" class="tt-btn tt-btn--secondary tt-btn--sm">
          <?php esc_html_e( 'Login', 'tentracker' ); ?>
        </a>
        <a href="<?php echo esc_url( tt_register_url() ); ?>" class="tt-btn tt-btn--primary tt-btn--sm">
          <?php esc_html_e( 'Sign Up Free', 'tentracker' ); ?>
        </a>
      <?php endif; ?>
    </div>

    <!-- Mobile hamburger -->
    <button class="tt-menu-toggle" id="tt-menu-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'tentracker' ); ?>" aria-expanded="false">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="6"  x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>

  </div><!-- .tt-header__inner -->
</header>

<!-- ══ MOBILE NAV DRAWER ════════════════════════════════════ -->
<div class="tt-mobile-nav" id="tt-mobile-nav" aria-hidden="true">
  <div class="tt-mobile-nav__overlay" id="tt-mobile-overlay"></div>
  <div class="tt-mobile-nav__drawer" role="dialog" aria-label="<?php esc_attr_e( 'Navigation', 'tentracker' ); ?>">

    <?php tt_the_logo(); ?>

    <button class="tt-mobile-nav__close" id="tt-mobile-close" aria-label="<?php esc_attr_e( 'Close menu', 'tentracker' ); ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 6 6 18M6 6l12 12"/>
      </svg>
    </button>

    <?php
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'menu_class'     => 'tt-mobile-nav__links',
        'container'      => false,
        'fallback_cb'    => 'tt_nav_fallback',
        'depth'          => 2,
    ) );
    ?>

    <div style="padding: 20px 0 0; display: flex; flex-direction: column; gap: 8px; border-top: 1px solid rgba(255,255,255,.1); margin-top: 16px;">
      <?php if ( is_user_logged_in() ) : ?>
        <a href="<?php echo esc_url( home_url( '/my-attempts' ) ); ?>" class="tt-btn tt-btn--secondary" style="width:100%; justify-content:center;">
          <?php esc_html_e( 'My Progress', 'tentracker' ); ?>
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url( wp_login_url() ); ?>" class="tt-btn tt-btn--secondary" style="width:100%; justify-content:center;">
          <?php esc_html_e( 'Login', 'tentracker' ); ?>
        </a>
        <a href="<?php echo esc_url( tt_register_url() ); ?>" class="tt-btn tt-btn--primary" style="width:100%; justify-content:center;">
          <?php esc_html_e( 'Sign Up Free', 'tentracker' ); ?>
        </a>
      <?php endif; ?>
    </div>

  </div>
</div>

<div id="page" class="site">
