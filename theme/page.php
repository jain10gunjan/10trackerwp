<?php
/**
 * Default page template
 *
 * @package TenTracker
 */
get_header();
the_post();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <?php tt_breadcrumbs(); ?>
    <h1 class="tt-page-header__title"><?php the_title(); ?></h1>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container">
    <div class="tt-layout<?php echo ! is_active_sidebar( 'sidebar-1' ) ? ' tt-no-sidebar' : ''; ?>">

      <main id="main" class="site-main" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:clamp(20px,4vw,40px);box-shadow:var(--shadow-sm);">
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </main>

      <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
      <aside class="tt-sidebar">
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
      </aside>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php get_footer();
