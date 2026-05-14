<?php
/**
 * Search results template
 *
 * @package TenTracker
 */
get_header();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <h1 class="tt-page-header__title">
      <?php printf( esc_html__( 'Results for: "%s"', 'tentracker' ), '<span style="color:var(--cyan);">' . esc_html( get_search_query() ) . '</span>' ); ?>
    </h1>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container">

    <!-- Search bar -->
    <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="tt-search-form" style="margin-bottom:32px;max-width:560px;">
      <input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search exams, quizzes…', 'tentracker' ); ?>">
      <button type="submit" class="tt-btn tt-btn--primary"><?php esc_html_e( 'Search', 'tentracker' ); ?></button>
    </form>

    <?php if ( have_posts() ) : ?>
      <div class="tt-feat-grid">
        <?php while ( have_posts() ) : the_post(); ?>
          <article class="tt-feat-card">
            <div class="tt-feat-card__body">
              <div class="tt-feat-card__tags">
                <span class="tt-badge tt-badge--navy"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
              </div>
              <h2 class="tt-feat-card__title">
                <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a>
              </h2>
              <p class="tt-feat-card__desc"><?php the_excerpt(); ?></p>
              <div class="tt-feat-card__foot">
                <span class="tt-feat-card__meta"><?php the_date(); ?></span>
                <a href="<?php the_permalink(); ?>" class="tt-btn tt-btn--outline tt-btn--sm"><?php esc_html_e( 'View →', 'tentracker' ); ?></a>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <div class="tt-pagination"><?php the_posts_pagination( array( 'prev_text' => '‹', 'next_text' => '›' ) ); ?></div>

    <?php else : ?>
      <div class="tt-notice tt-notice--warn">
        <span>⚠</span>
        <span><?php esc_html_e( 'No results found. Try a different keyword or browse all exams below.', 'tentracker' ); ?></span>
      </div>
      <div style="margin-top:32px;">
        <?php echo do_shortcode( '[ek_exam_list posts_per_page="6"]' ); ?>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php get_footer();
