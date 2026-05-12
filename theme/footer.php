  </div><!-- #page -->

<!-- ══ FOOTER ══════════════════════════════════════════════ -->
<footer class="tt-footer" id="colophon" role="contentinfo">
  <div class="tt-container">

    <div class="tt-footer__grid">

      <!-- Brand col -->
      <div class="tt-footer__brand">
        <?php tt_the_logo(); ?>
        <p class="tt-footer__brand-desc">
          <?php echo esc_html( get_theme_mod( 'tt_footer_tagline', __( "India's smartest exam prep platform. Unlimited MCQ practice, real-time analytics, and expert-curated content.", 'tentracker' ) ) ); ?>
        </p>
      </div>

      <!-- Exams col -->
      <div>
        <p class="tt-footer__col-title"><?php esc_html_e( 'Popular Exams', 'tentracker' ); ?></p>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer',
            'menu_class'     => 'tt-footer__links',
            'container'      => false,
            'fallback_cb'    => 'tt_footer_nav_fallback',
            'depth'          => 1,
        ) );
        ?>
      </div>

      <!-- Quick Links col -->
      <div>
        <p class="tt-footer__col-title"><?php esc_html_e( 'Quick Links', 'tentracker' ); ?></p>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer2',
            'menu_class'     => 'tt-footer__links',
            'container'      => false,
            'fallback_cb'    => 'tt_footer_nav2_fallback',
            'depth'          => 1,
        ) );
        ?>
      </div>

      <!-- Legal col -->
      <div>
        <p class="tt-footer__col-title"><?php esc_html_e( 'Legal', 'tentracker' ); ?></p>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'footer3',
            'menu_class'     => 'tt-footer__links',
            'container'      => false,
            'fallback_cb'    => 'tt_footer_nav3_fallback',
            'depth'          => 1,
        ) );
        ?>
      </div>

    </div><!-- .tt-footer__grid -->

    <div class="tt-footer__bottom">
      <p class="tt-footer__copy">
        &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>.
        <?php esc_html_e( 'All rights reserved.', 'tentracker' ); ?>
      </p>

      <div class="tt-footer__social">
        <a href="#" aria-label="Twitter / X">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
        </a>
        <a href="#" aria-label="Telegram">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
          </svg>
        </a>
        <a href="#" aria-label="YouTube">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
          </svg>
        </a>
      </div>
    </div>

  </div><!-- .tt-container -->
</footer>

<?php wp_footer(); ?>
</body>
</html>

<?php
/* Fallback menus */
function tt_footer_nav_fallback() {
    echo '<ul class="tt-footer__links">';
    $items = array( 'UPSC Civil Services', 'SSC CGL', 'IBPS PO', 'Railways RRB', 'NEET', 'JEE Main' );
    foreach ( $items as $item ) {
        echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $item ) . '</a></li>';
    }
    echo '</ul>';
}

function tt_footer_nav2_fallback() {
    echo '<ul class="tt-footer__links">';
    $items = array(
        'Practice Tests'  => home_url( '/exams' ),
        'Leaderboard'     => home_url( '/leaderboard' ),
        'My Progress'     => home_url( '/my-attempts' ),
        'Study Material'  => home_url( '/study' ),
        'Current Affairs' => home_url( '/current-affairs' ),
    );
    foreach ( $items as $label => $url ) {
        echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

function tt_footer_nav3_fallback() {
    echo '<ul class="tt-footer__links">';
    $items = array(
        'About Us'       => home_url( '/about' ),
        'Privacy Policy' => home_url( '/privacy-policy' ),
        'Terms of Use'   => home_url( '/terms' ),
        'Contact Us'     => home_url( '/contact' ),
    );
    foreach ( $items as $label => $url ) {
        echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}
?>
