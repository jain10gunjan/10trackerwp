<?php
/**
 * Template tag helpers  — called from templates
 *
 * @package TenTracker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output exam meta row (questions count, duration).
 * Uses ExamKit post meta keys.
 */
function tt_quiz_meta_row( $quiz_id ) {
    global $wpdb;
    $q_count  = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}examkit_questions WHERE quiz_id=%d", $quiz_id
    ) );
    $duration = absint( get_post_meta( $quiz_id, '_ek_duration', true ) ) ?: 60;
    $marks    = get_post_meta( $quiz_id, '_ek_total_marks', true );
    ?>
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:6px;">
      <span class="tt-badge tt-badge--navy">📋 <?php echo esc_html( $q_count ); ?> Qs</span>
      <span class="tt-badge tt-badge--navy">⏱ <?php echo esc_html( $duration ); ?> min</span>
      <?php if ( $marks ) : ?>
      <span class="tt-badge tt-badge--navy">🎯 <?php echo esc_html( $marks ); ?> marks</span>
      <?php endif; ?>
    </div>
    <?php
}

/**
 * Output a user's best score pill for a given quiz.
 */
function tt_best_score( $quiz_id, $user_id = null ) {
    if ( ! $user_id ) $user_id = get_current_user_id();
    if ( ! $user_id ) return;
    global $wpdb;
    $best = $wpdb->get_row( $wpdb->prepare(
        "SELECT score, total_marks FROM {$wpdb->prefix}examkit_attempts
         WHERE user_id=%d AND quiz_id=%d AND status='completed'
         ORDER BY score DESC LIMIT 1",
        $user_id, $quiz_id
    ) );
    if ( $best ) {
        $pct = $best->total_marks ? round( ( $best->score / $best->total_marks ) * 100 ) : 0;
        $cls = $pct >= 70 ? 'green' : ( $pct >= 40 ? 'amber' : 'red' );
        echo '<span class="tt-badge tt-badge--' . esc_attr( $cls ) . '">' . esc_html( $best->score . '/' . $best->total_marks ) . ' (' . $pct . '%)</span>';
    }
}

/**
 * Render a compact user progress summary bar.
 */
function tt_user_progress_bar( $user_id = null ) {
    if ( ! $user_id ) $user_id = get_current_user_id();
    if ( ! $user_id ) return;
    global $wpdb;
    $total = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}examkit_attempts WHERE user_id=%d AND status='completed'", $user_id
    ) );
    $avg = (float) $wpdb->get_var( $wpdb->prepare(
        "SELECT AVG( score / total_marks * 100 ) FROM {$wpdb->prefix}examkit_attempts
         WHERE user_id=%d AND status='completed' AND total_marks > 0", $user_id
    ) );
    if ( ! $total ) return;
    $avg_r = round( $avg );
    ?>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px 20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
      <div>
        <div style="font-family:var(--head);font-size:1.25rem;font-weight:800;color:var(--blue);"><?php echo esc_html( $total ); ?></div>
        <div style="font-size:.75rem;color:var(--text-3);text-transform:uppercase;letter-spacing:.05em;">Attempts</div>
      </div>
      <div style="flex:1;min-width:120px;">
        <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--text-3);margin-bottom:5px;">
          <span><?php esc_html_e( 'Avg Score', 'tentracker' ); ?></span>
          <strong style="color:var(--text);"><?php echo esc_html( $avg_r ); ?>%</strong>
        </div>
        <div class="tt-progress">
          <div class="tt-progress__bar" style="width:<?php echo esc_attr( $avg_r ); ?>%"></div>
        </div>
      </div>
    </div>
    <?php
}
