<?php
/**
 * 404 template
 *
 * @package TenTracker
 */
get_header();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <h1 class="tt-page-header__title"><?php esc_html_e( 'Page Not Found', 'tentracker' ); ?></h1>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container" style="max-width:560px;text-align:center;padding-block:60px;">
    <div style="font-size:5rem;margin-bottom:16px;">🔍</div>
    <h2 style="font-family:var(--head);font-size:1.5rem;font-weight:800;color:var(--text);margin-bottom:12px;">
      <?php esc_html_e( "Oops! We couldn't find that page.", 'tentracker' ); ?>
    </h2>
    <p style="color:var(--text-3);margin-bottom:28px;">
      <?php esc_html_e( 'The page may have moved, been deleted, or never existed. Try searching for an exam instead.', 'tentracker' ); ?>
    </p>

    <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="tt-search-form" style="justify-content:center;margin-bottom:20px;">
      <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search for an exam…', 'tentracker' ); ?>" style="max-width:320px;">
      <button type="submit" class="tt-btn tt-btn--primary"><?php esc_html_e( 'Search', 'tentracker' ); ?></button>
    </form>

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tt-btn tt-btn--ghost">
      ← <?php esc_html_e( 'Back to Home', 'tentracker' ); ?>
    </a>
  </div>
</div>

<?php get_footer();
