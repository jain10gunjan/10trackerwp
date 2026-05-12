<?php
/**
 * Single Quiz page  — ExamKit quiz attempt + solution review on finish
 * Full-width (no sidebar) so the quiz UI gets maximum room.
 *
 * @package TenTracker
 */
get_header();
the_post();
$quiz_id        = get_the_ID();
$exam_id        = 0;
$question_count = tt_exam_get_quiz_question_count( $quiz_id );
$difficulty     = tt_exam_get_quiz_difficulty( $quiz_id );
$duration       = tt_exam_get_quiz_duration( $quiz_id );
$chapter        = tt_exam_get_quiz_chapter( $quiz_id );
$categories     = tt_exam_get_quiz_category_labels( $quiz_id );
$related_quiz   = tt_quiz_get_related_quizzes( $quiz_id, 6 );

foreach ( array( '_ek_exam_id', 'ek_exam_id', 'exam_id' ) as $exam_key ) {
    $maybe_exam_id = absint( get_post_meta( $quiz_id, $exam_key, true ) );
    if ( $maybe_exam_id ) {
        $exam_id = $maybe_exam_id;
        break;
    }
}
?>

<header class="tt-single-quiz-hero">
  <div class="tt-container">
    <?php tt_breadcrumbs(); ?>

    <div class="tt-single-quiz-hero__grid">
      <div class="tt-single-quiz-hero__content">
        <p class="tt-single-quiz-hero__eyebrow"><?php esc_html_e( 'Practice Quiz', 'tentracker' ); ?></p>
        <h1 class="tt-single-quiz-hero__title"><?php the_title(); ?></h1>

        <?php if ( has_excerpt() ) : ?>
          <p class="tt-single-quiz-hero__sub"><?php the_excerpt(); ?></p>
        <?php endif; ?>

        <div class="tt-single-quiz-hero__chips">
          <?php if ( $difficulty ) : ?>
            <span><?php echo esc_html( $difficulty ); ?></span>
          <?php endif; ?>
          <?php if ( $chapter ) : ?>
            <span><?php echo esc_html( $chapter ); ?></span>
          <?php elseif ( ! empty( $categories ) ) : ?>
            <span><?php echo esc_html( $categories[0] ); ?></span>
          <?php endif; ?>
          <?php if ( $duration ) : ?>
            <span><?php echo esc_html( $duration ); ?></span>
          <?php endif; ?>
        </div>

        <div class="tt-single-quiz-hero__actions">
          <a href="#quiz-attempt" class="tt-btn tt-btn--primary"><?php esc_html_e( 'Start now', 'tentracker' ); ?></a>
          <?php if ( $exam_id ) : ?>
            <a href="<?php echo esc_url( get_permalink( $exam_id ) ); ?>" class="tt-btn tt-btn--outline"><?php esc_html_e( 'Back to exam', 'tentracker' ); ?></a>
          <?php endif; ?>
        </div>
      </div>

      <div class="tt-single-quiz-stats" aria-label="<?php esc_attr_e( 'Quiz details', 'tentracker' ); ?>">
        <div>
          <span><?php esc_html_e( 'Questions', 'tentracker' ); ?></span>
          <strong><?php echo esc_html( $question_count ? number_format_i18n( $question_count ) : __( 'Ready', 'tentracker' ) ); ?></strong>
        </div>
        <div>
          <span><?php esc_html_e( 'Level', 'tentracker' ); ?></span>
          <strong><?php echo esc_html( $difficulty ?: __( 'Mixed', 'tentracker' ) ); ?></strong>
        </div>
        <div>
          <span><?php esc_html_e( 'Time', 'tentracker' ); ?></span>
          <strong><?php echo esc_html( $duration ?: __( 'Flexible', 'tentracker' ) ); ?></strong>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="tt-content tt-single-quiz-content">
  <div class="tt-container">
    <section class="tt-quiz-attempt-card" id="quiz-attempt" aria-label="<?php esc_attr_e( 'Quiz attempt', 'tentracker' ); ?>">
      <div class="tt-quiz-attempt-card__head">
        <div>
          <p class="tt-section-panel-head__eyebrow"><?php esc_html_e( 'Attempt', 'tentracker' ); ?></p>
          <h2 class="tt-quiz-attempt-card__title"><?php esc_html_e( 'Answer carefully and review instantly', 'tentracker' ); ?></h2>
        </div>
        <span><?php esc_html_e( 'Mobile optimized', 'tentracker' ); ?></span>
      </div>

      <div class="tt-quiz-wrap">
        <?php
        $content = get_the_content();
        if ( strpos( $content, 'ek_quiz' ) === false ) {
            echo do_shortcode( '[ek_quiz quiz_id="' . $quiz_id . '"]' );
        } else {
            echo apply_filters( 'the_content', $content );
        }
        ?>
      </div>
    </section>

    <?php if ( ! empty( $related_quiz ) ) : ?>
      <section class="tt-related-quiz-section" aria-labelledby="related-quiz-heading">
        <div class="tt-section-panel-head">
          <div>
            <p class="tt-section-panel-head__eyebrow"><?php esc_html_e( 'Keep Practicing', 'tentracker' ); ?></p>
            <h2 class="tt-section-panel-head__title" id="related-quiz-heading"><?php esc_html_e( 'Related quizzes from the same chapter or category', 'tentracker' ); ?></h2>
          </div>
          <span class="tt-section-panel-head__count">
            <?php echo esc_html( sprintf( _n( '%s quiz', '%s quizzes', count( $related_quiz ), 'tentracker' ), number_format_i18n( count( $related_quiz ) ) ) ); ?>
          </span>
        </div>

        <?php tt_quiz_render_related_cards( $related_quiz ); ?>
      </section>
    <?php endif; ?>

    <!-- Solution review panel — revealed by JS when ExamKit fires its result event -->
    <div id="tt-solution-panel" style="display:none;">
      <div class="tt-solution-review">
        <div class="tt-solution-review__header">
          <span class="tt-solution-review__title">📖 <?php esc_html_e( 'Solution Review', 'tentracker' ); ?></span>
          <span class="tt-solution-review__badge"><?php esc_html_e( 'All Questions', 'tentracker' ); ?></span>
        </div>
        <!-- Items injected by JS after quiz finishes -->
        <div id="tt-solution-items"></div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  /**
   * Attempts to pull quiz state from ExamKit's JS objects.
   * ExamKit stores attempts in window.examkitData or fires custom events.
   * We watch for DOM mutations on the results container as a reliable hook.
   */
  var panel   = document.getElementById('tt-solution-panel');
  var itemsEl = document.getElementById('tt-solution-items');
  var shown   = false;

  function escHtml(str) {
    var d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
  }

  function renderSolutions(questions) {
    if (!questions || !questions.length || shown) return;
    shown = true;

    var html = '';
    questions.forEach(function (q, idx) {
      var status = q.correct ? 'correct' : (q.answered ? 'wrong' : 'skip');
      var statusLabel = q.correct ? '✓' : (q.answered ? '✗' : '–');
      html += '<div class="tt-solution-item tt-solution-item--' + status + '">';

      // Question row
      html += '<div class="tt-solution-item__q-row">';
      html += '<div class="tt-solution-item__qnum">' + statusLabel + '</div>';
      html += '<div class="tt-solution-item__question">' + escHtml(q.question || ('Question ' + (idx + 1))) + '</div>';
      html += '</div>';

      // Options
      if (q.options && q.options.length) {
        html += '<div class="tt-solution-item__opts">';
        var labels = ['A','B','C','D','E'];
        q.options.forEach(function (opt, oi) {
          var isCorrect = (oi === q.correctIndex) || (opt.correct);
          var isUserWrong = (q.userIndex === oi && !isCorrect);
          var cls = isCorrect ? 'tt-solution-opt--correct' : (isUserWrong ? 'tt-solution-opt--wrong' : '');
          html += '<div class="tt-solution-opt ' + cls + '">';
          html += '<span class="tt-solution-opt__label">' + (labels[oi] || (oi+1)) + '</span>';
          html += escHtml(opt.text || opt) ;
          if (isCorrect) html += ' ✓';
          html += '</div>';
        });
        html += '</div>';
      }

      // Explanation
      if (q.explanation) {
        html += '<div class="tt-solution-item__explanation"><strong>Explanation:</strong> ' + escHtml(q.explanation) + '</div>';
      }

      html += '</div>'; // .tt-solution-item
    });

    itemsEl.innerHTML = html;
    panel.style.display = '';
    // Smooth scroll to solution panel
    setTimeout(function () {
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 400);
  }

  /* ── Strategy 1: ExamKit custom event ─────────────────── */
  document.addEventListener('examkit:quiz:result', function (e) {
    if (e.detail && e.detail.questions) renderSolutions(e.detail.questions);
  });
  document.addEventListener('ek:quiz:complete', function (e) {
    if (e.detail && e.detail.questions) renderSolutions(e.detail.questions);
  });

  /* ── Strategy 2: MutationObserver on results container ── */
  // ExamKit typically renders a .ek-result or .examkit-result div on finish.
  // We watch the quiz wrap for that div and then scrape the rendered DOM.
  var quizWrap = document.querySelector('.tt-quiz-wrap');
  if (quizWrap && window.MutationObserver) {
    var observer = new MutationObserver(function (mutations) {
      // Check if a result/summary screen appeared
      var resultEl = quizWrap.querySelector(
        '.ek-result, .ek-quiz-result, .examkit-result, [class*="result"], [class*="summary"]'
      );
      if (!resultEl || shown) return;

      // Try window.examkitData (set by ExamKit)
      if (window.examkitData && window.examkitData.questions) {
        renderSolutions(window.examkitData.questions);
        return;
      }

      // Try to find question data in any global ExamKit store
      var ekKeys = Object.keys(window).filter(function(k){
        return /examkit|ek_quiz|ekQuiz/i.test(k);
      });
      for (var i = 0; i < ekKeys.length; i++) {
        var obj = window[ekKeys[i]];
        if (obj && (obj.questions || (obj.quiz && obj.quiz.questions))) {
          renderSolutions(obj.questions || obj.quiz.questions);
          return;
        }
      }

      // Fallback: scrape question rows from the result DOM
      var rows = quizWrap.querySelectorAll('[class*="question"], [class*="ek-q"]');
      if (rows.length) {
        var scraped = [];
        rows.forEach(function (row) {
          var qText   = (row.querySelector('[class*="question-text"], [class*="q-text"], p') || {}).textContent || '';
          var correct = row.querySelector('[class*="correct"]');
          var wrong   = row.querySelector('[class*="wrong"], [class*="incorrect"]');
          scraped.push({
            question: qText.trim(),
            correct: !!correct && !wrong,
            answered: !!(correct || wrong),
            explanation: (row.querySelector('[class*="explanation"], [class*="solution"]') || {}).textContent || ''
          });
        });
        if (scraped.length) renderSolutions(scraped);
      }
    });
    observer.observe(quizWrap, { childList: true, subtree: true });
  }
})();
</script>

<?php get_footer();
