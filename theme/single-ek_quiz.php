<?php
/**
 * Single Quiz page  — ExamKit quiz attempt + solution review on finish
 * Full-width (no sidebar) so the quiz UI gets maximum room.
 *
 * @package TenTracker
 */
get_header();
the_post();
$quiz_id = get_the_ID();
?>

<!-- Slim page header (breadcrumb + title) -->
<div class="tt-page-header" style="padding:20px 0 24px;">
  <div class="tt-container tt-page-header__inner">
    <?php tt_breadcrumbs(); ?>
    <h1 class="tt-page-header__title" style="font-size:clamp(1.1rem,2vw,1.5rem);"><?php the_title(); ?></h1>
  </div>
</div>

<div class="tt-content" style="padding-block:0;">
  <!-- ExamKit auto-injects [ek_quiz] via the_content filter.
       We only call the shortcode explicitly when it's NOT already present. -->
  <div class="tt-quiz-wrap" style="padding-inline:0;max-width:100%;">
    <?php
    $content = get_the_content();
    if ( strpos( $content, 'ek_quiz' ) === false ) {
        echo do_shortcode( '[ek_quiz quiz_id="' . $quiz_id . '"]' );
    } else {
        echo apply_filters( 'the_content', $content );
    }
    ?>
  </div>

  <!-- Solution review panel — revealed by JS when ExamKit fires its result event -->
  <div class="tt-container" id="tt-solution-panel" style="display:none;padding-block:32px 48px;">
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
