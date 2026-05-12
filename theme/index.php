<?php
/**
 * Index / Blog fallback template
 *
 * @package TenTracker
 */
get_header();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <?php tt_breadcrumbs(); ?>
    <h1 class="tt-page-header__title">
      <?php
      if ( is_search() ) {
          printf( esc_html__( 'Search results for: "%s"', 'tentracker' ), get_search_query() );
      } elseif ( is_archive() ) {
          the_archive_title();
      } else {
          esc_html_e( 'Latest Posts', 'tentracker' );
      }
      ?>
    </h1>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container">
    <?php if ( have_posts() ) : ?>
      <div class="tt-feat-grid">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
          <article class="tt-feat-card" id="post-<?php the_ID(); ?>">
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
                <a href="<?php the_permalink(); ?>" class="tt-btn tt-btn--outline tt-btn--sm">
                  <?php esc_html_e( 'Read More', 'tentracker' ); ?>
                </a>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <div class="tt-pagination">
        <?php
        the_posts_pagination( array(
            'prev_text'          => '‹',
            'next_text'          => '›',
            'before_page_number' => '<span class="tt-sr-only">' . __( 'Page', 'tentracker' ) . ' </span>',
        ) );
        ?>
      </div>

    <?php else : ?>
      <div class="tt-notice tt-notice--info">
        <span>ℹ</span>
        <span><?php esc_html_e( 'Nothing found for your search. Try a different query.', 'tentracker' ); ?></span>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php get_footer();
