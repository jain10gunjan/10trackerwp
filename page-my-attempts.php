<?php
/**
 * Template Name: My Attempts / Progress
 *
 * @package TenTracker
 */
get_header();
the_post();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <h1 class="tt-page-header__title"><?php esc_html_e( 'My Progress', 'tentracker' ); ?></h1>
    <p class="tt-page-header__sub"><?php esc_html_e( 'Review all your quiz attempts and track improvement over time.', 'tentracker' ); ?></p>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container">
    <?php echo do_shortcode( '[ek_my_attempts]' ); ?>
  </div>
</div>

<?php get_footer();
