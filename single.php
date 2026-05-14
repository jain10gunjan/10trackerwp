<?php
/**
 * Single post template  — blog / study material articles
 *
 * @package TenTracker
 */
get_header();
the_post();
?>

<div class="tt-page-header">
  <div class="tt-container tt-page-header__inner">
    <?php tt_breadcrumbs(); ?>
    <div style="margin-bottom:12px;">
      <?php
      $cats = get_the_category();
      foreach ( $cats as $cat ) :
      ?>
        <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="tt-badge tt-badge--blue" style="text-decoration:none;margin-right:6px;">
          <?php echo esc_html( $cat->name ); ?>
        </a>
      <?php endforeach; ?>
    </div>
    <h1 class="tt-page-header__title"><?php the_title(); ?></h1>
    <div style="display:flex;align-items:center;gap:14px;margin-top:12px;flex-wrap:wrap;">
      <span style="font-size:.8125rem;color:rgba(255,255,255,.5);">
        <?php echo get_the_date(); ?>
      </span>
      <span style="font-size:.8125rem;color:rgba(255,255,255,.5);">
        <?php echo esc_html( get_the_author() ); ?>
      </span>
      <?php
      $reading_time = max( 1, intval( str_word_count( strip_tags( get_the_content() ) ) / 200 ) );
      ?>
      <span style="font-size:.8125rem;color:rgba(255,255,255,.5);">
        ⏱ <?php printf( esc_html__( '%d min read', 'tentracker' ), $reading_time ); ?>
      </span>
    </div>
  </div>
</div>

<div class="tt-content">
  <div class="tt-container">
    <div class="tt-layout">

      <main id="main">
        <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom:24px;border-radius:var(--r-lg);overflow:hidden;box-shadow:var(--shadow);">
            <?php the_post_thumbnail( 'tt-hero', array( 'style' => 'width:100%;height:auto;' ) ); ?>
          </div>
        <?php endif; ?>

        <div class="entry-content" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:clamp(20px,4vw,40px);box-shadow:var(--shadow-sm);">
          <?php the_content(); ?>

          <?php
          wp_link_pages( array(
              'before'      => '<div class="page-links">' . __( 'Pages:', 'tentracker' ),
              'after'       => '</div>',
              'link_before' => '<span class="page-number">',
              'link_after'  => '</span>',
          ) );
          ?>
        </div>

        <!-- Post navigation -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:24px;">
          <?php
          $prev = get_previous_post();
          $next = get_next_post();
          if ( $prev ) :
          ?>
            <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>"
               style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px 18px;text-decoration:none;display:flex;flex-direction:column;gap:4px;">
              <span style="font-size:.6875rem;color:var(--text-4);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">← Previous</span>
              <span style="font-size:.875rem;font-weight:600;color:var(--text);"><?php echo esc_html( get_the_title( $prev ) ); ?></span>
            </a>
          <?php else : echo '<div></div>'; endif; ?>
          <?php if ( $next ) : ?>
            <a href="<?php echo esc_url( get_permalink( $next ) ); ?>"
               style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px 18px;text-decoration:none;display:flex;flex-direction:column;gap:4px;text-align:right;">
              <span style="font-size:.6875rem;color:var(--text-4);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Next →</span>
              <span style="font-size:.875rem;font-weight:600;color:var(--text);"><?php echo esc_html( get_the_title( $next ) ); ?></span>
            </a>
          <?php endif; ?>
        </div>

        <?php if ( comments_open() || get_comments_number() ) : ?>
          <div style="margin-top:28px;">
            <?php comments_template(); ?>
          </div>
        <?php endif; ?>
      </main>

      <aside class="tt-sidebar">
        <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
          <?php dynamic_sidebar( 'sidebar-1' ); ?>
        <?php else : ?>
          <!-- Related exams widget -->
          <div class="tt-sidebar-widget">
            <div class="tt-sidebar-widget__head">
              <h3 class="tt-sidebar-widget__title"><?php esc_html_e( 'Practice Exams', 'tentracker' ); ?></h3>
            </div>
            <div class="tt-sidebar-widget__body" style="padding:0;">
              <?php
              $exams = get_posts( array( 'post_type' => 'ek_exam', 'posts_per_page' => 6, 'post_status' => 'publish' ) );
              foreach ( $exams as $exam ) :
              ?>
                <a href="<?php echo esc_url( get_permalink( $exam->ID ) ); ?>"
                   style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid var(--border);font-size:.8125rem;font-weight:500;color:var(--text-2);text-decoration:none;transition:background .15s;"
                   onmouseover="this.style.background='var(--blue-lt)';this.style.color='var(--blue)'"
                   onmouseout="this.style.background='';this.style.color='var(--text-2)'">
                  <?php echo esc_html( $exam->post_title ); ?>
                  <span style="color:var(--text-4);">→</span>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </aside>

    </div>
  </div>
</div>

<?php get_footer();
