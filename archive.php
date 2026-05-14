<?php
/**
 * Generic archive template  — categories, tags, dates, authors
 *
 * @package TenTracker
 */
get_header();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <h1 class="tt-page-header__title"><?php the_archive_title(); ?></h1>
    <?php the_archive_description( '<p class="tt-page-header__sub">', '</p>' ); ?>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container">
    <?php if ( have_posts() ) : ?>
      <div class="tt-feat-grid">
        <?php while ( have_posts() ) : the_post(); ?>
          <article class="tt-feat-card">
            <?php if ( has_post_thumbnail() ) : ?>
              <div class="tt-feat-card__cover">
                <?php the_post_thumbnail( 'tt-card', array( 'style' => 'width:100%;height:100%;object-fit:cover;' ) ); ?>
              </div>
            <?php endif; ?>
            <div class="tt-feat-card__body">
              <h2 class="tt-feat-card__title">
                <a href="<?php the_permalink(); ?>" style="color:inherit;text-decoration:none;"><?php the_title(); ?></a>
              </h2>
              <p class="tt-feat-card__desc"><?php the_excerpt(); ?></p>
              <div class="tt-feat-card__foot">
                <span class="tt-feat-card__meta"><?php the_date(); ?></span>
                <a href="<?php the_permalink(); ?>" class="tt-btn tt-btn--outline tt-btn--sm"><?php esc_html_e( 'Read →', 'tentracker' ); ?></a>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
      <div class="tt-pagination"><?php the_posts_pagination( array( 'prev_text' => '‹', 'next_text' => '›' ) ); ?></div>
    <?php else : ?>
      <div class="tt-notice tt-notice--info">
        <span>ℹ</span>
        <span><?php esc_html_e( 'No posts found.', 'tentracker' ); ?></span>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer();
