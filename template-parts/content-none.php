<?php
/**
 * Content-none partial — no posts state
 *
 * @package TenTracker
 */
?>
<div class="tt-notice tt-notice--info" style="margin-top:24px;">
  <span>🔍</span>
  <div>
    <strong><?php esc_html_e( 'Nothing found.', 'tentracker' ); ?></strong><br>
    <?php esc_html_e( 'Try adjusting your search or browse the exam library.', 'tentracker' ); ?>
    <a href="<?php echo esc_url( get_post_type_archive_link( 'ek_exam' ) ?: home_url() ); ?>" class="tt-btn tt-btn--primary tt-btn--sm" style="margin-left:12px;display:inline-flex;">
      <?php esc_html_e( 'Browse Exams', 'tentracker' ); ?>
    </a>
  </div>
</div>
