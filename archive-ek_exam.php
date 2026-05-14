<?php
/**
 * Exam archive — /exam/
 *
 * @package TenTracker
 */
get_header();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <h1 class="tt-page-header__title"><?php esc_html_e( 'All Exams', 'tentracker' ); ?></h1>
    <p class="tt-page-header__sub"><?php esc_html_e( 'Pick an exam and start practising today.', 'tentracker' ); ?></p>
  </div>
</div>

<section class="tt-section">
  <div class="tt-container">
    <?php echo do_shortcode( '[ek_exam_list posts_per_page="50"]' ); ?>
  </div>
</section>

<?php get_footer();
