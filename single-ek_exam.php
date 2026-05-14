<?php
/**
 * Single Exam page — premium accordion-based exam detail layout
 *
 * @package TenTracker
 */
get_header();

the_post();
$exam_id = get_the_ID();

$quiz_category = (string) get_post_meta( $exam_id, 'exam_quiz_category', true );
$about_html    = (string) get_post_meta( $exam_id, 'exam_about', true );
$ca_html       = (string) get_post_meta( $exam_id, 'exam_current_affairs', true );
$syllabus_html = (string) get_post_meta( $exam_id, 'exam_syllabus', true );
$extra_raw     = (string) get_post_meta( $exam_id, 'exam_accordion_extra', true );
$extra_items   = json_decode( $extra_raw, true );
if ( ! is_array( $extra_items ) ) {
    $extra_items = array();
}

$intro_content          = preg_replace( '/\[ek_quiz_list[^\]]*\]/i', '', (string) get_the_content( null, false, $exam_id ) );
$intro_content          = trim( (string) $intro_content );
?>

<!-- Hero -->
<header class="tt-exam-hero tt-exam-hero--test-series">
  <div class="tt-container tt-exam-hero__inner">
    <?php tt_breadcrumbs(); ?>

    <div class="tt-exam-hero__grid">
      <div class="tt-exam-hero__content">
        <h1 class="tt-exam-hero__title">
          <?php
          printf(
              /* translators: %s: exam title */
              esc_html__( '%s Mock Test Series', 'tentracker' ),
              esc_html( get_the_title() )
          );
          ?>
        </h1>

        <p class="tt-exam-hero__updated">
          <?php
          printf(
              /* translators: %s: modified date */
              esc_html__( 'Last updated on %s', 'tentracker' ),
              esc_html( get_the_modified_date() )
          );
          ?>
        </p>

        <?php if ( has_excerpt() ) : ?>
          <p class="tt-exam-hero__sub"><?php the_excerpt(); ?></p>
        <?php endif; ?>

        <div class="tt-exam-hero__meta">
          <?php if ( $quiz_category ) : ?>
            <span class="tt-pill tt-pill--blue"><?php echo esc_html( $quiz_category ); ?></span>
          <?php endif; ?>
          <span class="tt-pill tt-pill--muted"><?php esc_html_e( 'Mobile friendly', 'tentracker' ); ?></span>
          <span class="tt-pill tt-pill--muted"><?php esc_html_e( 'Instant results', 'tentracker' ); ?></span>
        </div>

        <div class="tt-exam-hero__cta">
          <a href="#accordion-quiz" class="tt-btn tt-btn--primary"><?php esc_html_e( 'Start Practice', 'tentracker' ); ?></a>
          <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( home_url( '/my-attempts' ) ); ?>" class="tt-btn tt-btn--outline"><?php esc_html_e( 'View My Progress', 'tentracker' ); ?></a>
          <?php else : ?>
            <a href="<?php echo esc_url( tt_register_url() ); ?>" class="tt-btn tt-btn--outline"><?php esc_html_e( 'Create free account', 'tentracker' ); ?></a>
          <?php endif; ?>
        </div>
      </div>

      <div class="tt-exam-hero__card">
        <div class="tt-exam-hero__card-title"><?php esc_html_e( 'Test Series', 'tentracker' ); ?></div>
        <div class="tt-exam-hero__card-list">
          <div class="tt-exam-hero__card-row">
            <span><?php esc_html_e( 'Total Tests', 'tentracker' ); ?></span>
            <strong id="tt-exam-quiz-count">—</strong>
          </div>
          <div class="tt-exam-hero__card-row">
            <span><?php esc_html_e( 'Total Questions', 'tentracker' ); ?></span>
            <strong id="tt-exam-question-count">—</strong>
          </div>
          <div class="tt-exam-hero__card-row">
            <span><?php esc_html_e( 'Languages', 'tentracker' ); ?></span>
            <strong id="tt-exam-diff-mix"><?php esc_html_e( 'Loading', 'tentracker' ); ?></strong>
          </div>
        </div>
        <div class="tt-exam-hero__card-foot">
          <a href="<?php echo esc_url( is_user_logged_in() ? '#accordion-quiz' : tt_register_url() ); ?>" class="tt-exam-series-cta">
            <?php echo esc_html( is_user_logged_in() ? __( 'Start Free Tests', 'tentracker' ) : __( 'Sign up & Take Free Tests', 'tentracker' ) ); ?>
          </a>
          <span class="tt-exam-hero__card-note"><?php esc_html_e( 'Practice tests, chapter tests and current affairs in one place.', 'tentracker' ); ?></span>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="tt-content">
  <div class="tt-container">
    <main id="main" class="site-main tt-exam-main">

      <?php if ( '' !== trim( wp_strip_all_tags( strip_shortcodes( $intro_content ) ) ) ) : ?>
        <div class="tt-exam-intro entry-content">
          <?php echo wpautop( do_shortcode( $intro_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>
      <?php endif; ?>

      <?php
      $about_posts           = tt_exam_get_related_posts( $exam_id );
      $current_affairs_posts = tt_exam_get_related_posts( $exam_id, true );
      ?>

      <section class="tt-accordions tt-accordions--horizontal" aria-label="<?php esc_attr_e( 'Exam sections', 'tentracker' ); ?>">
        <div class="tt-accordion-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Exam sections', 'tentracker' ); ?>">
          <button class="tt-accordion__header" id="accordion-quiz-tab" type="button" role="tab" aria-controls="accordion-quiz" aria-selected="true" aria-expanded="true">
            <span class="tt-accordion__icon" aria-hidden="true">📋</span>
            <span class="tt-accordion__title"><?php esc_html_e( 'Practice Quizzes', 'tentracker' ); ?></span>
          </button>

          <button class="tt-accordion__header" id="accordion-about-tab" type="button" role="tab" aria-controls="accordion-about" aria-selected="false" aria-expanded="false">
            <span class="tt-accordion__icon" aria-hidden="true">ℹ️</span>
            <span class="tt-accordion__title"><?php esc_html_e( 'About Exam', 'tentracker' ); ?></span>
          </button>

          <button class="tt-accordion__header" id="accordion-ca-tab" type="button" role="tab" aria-controls="accordion-ca" aria-selected="false" aria-expanded="false">
            <span class="tt-accordion__icon" aria-hidden="true">📰</span>
            <span class="tt-accordion__title"><?php esc_html_e( 'Current Affairs', 'tentracker' ); ?></span>
          </button>

          <?php if ( $syllabus_html ) : ?>
            <button class="tt-accordion__header" id="accordion-syllabus-tab" type="button" role="tab" aria-controls="accordion-syllabus" aria-selected="false" aria-expanded="false">
              <span class="tt-accordion__icon" aria-hidden="true">🧾</span>
              <span class="tt-accordion__title"><?php esc_html_e( 'Syllabus', 'tentracker' ); ?></span>
            </button>
          <?php endif; ?>

          <?php foreach ( $extra_items as $idx => $item ) :
              if ( ! is_array( $item ) ) continue;
              $t = sanitize_text_field( $item['title'] ?? '' );
              $c = $item['content'] ?? '';
              if ( '' === trim( $t ) && '' === trim( (string) $c ) ) continue;
              $acc_id = 'accordion-extra-' . (int) $idx;
          ?>
            <button class="tt-accordion__header" id="<?php echo esc_attr( $acc_id ); ?>-tab" type="button" role="tab" aria-controls="<?php echo esc_attr( $acc_id ); ?>" aria-selected="false" aria-expanded="false">
              <span class="tt-accordion__icon" aria-hidden="true">📌</span>
              <span class="tt-accordion__title"><?php echo esc_html( $t ?: __( 'More', 'tentracker' ) ); ?></span>
            </button>
          <?php endforeach; ?>
        </div>

        <div class="tt-accordion-panels">
          <div class="tt-accordion is-open" id="accordion-quiz" role="tabpanel" aria-labelledby="accordion-quiz-tab" data-tt-accordion="quiz">
            <div class="tt-accordion__body">
              <div class="tt-section-panel-head">
                <div>
                  <p class="tt-section-panel-head__eyebrow"><?php esc_html_e( 'All Tests', 'tentracker' ); ?></p>
                  <h2 class="tt-section-panel-head__title">
                    <?php
                    printf(
                        /* translators: %s: exam title */
                        esc_html__( '%s Mock Test Series All Tests', 'tentracker' ),
                        esc_html( get_the_title() )
                    );
                    ?>
                  </h2>
                </div>
                <span class="tt-section-panel-head__count" id="tt-practice-summary"><?php esc_html_e( 'Loading quizzes...', 'tentracker' ); ?></span>
              </div>

              <?php tt_exam_render_quiz_rest_browser( $exam_id ); ?>
            </div>
          </div>

          <div class="tt-accordion" id="accordion-about" role="tabpanel" aria-labelledby="accordion-about-tab" data-tt-accordion="about" hidden>
            <div class="tt-accordion__body">
              <?php if ( $about_html ) : ?>
                <div class="tt-exam-rich entry-content">
                  <?php echo apply_filters( 'the_content', $about_html ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
              <?php endif; ?>

              <div class="tt-section-panel-head">
                <div>
                  <p class="tt-section-panel-head__eyebrow"><?php esc_html_e( 'Related Articles', 'tentracker' ); ?></p>
                  <h2 class="tt-section-panel-head__title"><?php esc_html_e( 'Articles from this exam category', 'tentracker' ); ?></h2>
                </div>
                <span class="tt-section-panel-head__count">
                  <?php echo esc_html( sprintf( _n( '%s article', '%s articles', count( $about_posts ), 'tentracker' ), number_format_i18n( count( $about_posts ) ) ) ); ?>
                </span>
              </div>

              <?php tt_exam_render_article_cards( $about_posts, __( 'No articles matched this exam category yet.', 'tentracker' ) ); ?>
            </div>
          </div>

          <div class="tt-accordion" id="accordion-ca" role="tabpanel" aria-labelledby="accordion-ca-tab" data-tt-accordion="current-affairs" hidden>
            <div class="tt-accordion__body">
              <?php if ( $ca_html ) : ?>
                <div class="tt-exam-rich entry-content">
                  <?php echo apply_filters( 'the_content', $ca_html ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
              <?php endif; ?>

              <div class="tt-section-panel-head">
                <div>
                  <p class="tt-section-panel-head__eyebrow"><?php esc_html_e( 'Current Affairs', 'tentracker' ); ?></p>
                  <h2 class="tt-section-panel-head__title"><?php esc_html_e( 'Posts tagged with this exam and Current Affairs', 'tentracker' ); ?></h2>
                </div>
                <span class="tt-section-panel-head__count">
                  <?php echo esc_html( sprintf( _n( '%s update', '%s updates', count( $current_affairs_posts ), 'tentracker' ), number_format_i18n( count( $current_affairs_posts ) ) ) ); ?>
                </span>
              </div>

              <?php tt_exam_render_article_cards( $current_affairs_posts, __( 'No current affairs posts matched both categories yet.', 'tentracker' ) ); ?>
            </div>
          </div>

          <?php if ( $syllabus_html ) : ?>
            <div class="tt-accordion" id="accordion-syllabus" role="tabpanel" aria-labelledby="accordion-syllabus-tab" data-tt-accordion="syllabus" hidden>
              <div class="tt-accordion__body">
                <div class="tt-exam-rich entry-content">
                  <?php echo apply_filters( 'the_content', $syllabus_html ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php foreach ( $extra_items as $idx => $item ) :
              if ( ! is_array( $item ) ) continue;
              $t = sanitize_text_field( $item['title'] ?? '' );
              $c = $item['content'] ?? '';
              if ( '' === trim( $t ) && '' === trim( (string) $c ) ) continue;
              $acc_id = 'accordion-extra-' . (int) $idx;
          ?>
            <div class="tt-accordion" id="<?php echo esc_attr( $acc_id ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( $acc_id ); ?>-tab" data-tt-accordion="extra-<?php echo (int) $idx; ?>" hidden>
              <div class="tt-accordion__body">
                <div class="tt-exam-rich entry-content">
                  <?php echo apply_filters( 'the_content', (string) $c ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

    </main>
  </div>
</div>

<?php get_footer();
