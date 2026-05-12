<?php
/**
 * Front Page template  — 10Tracker homepage
 *
 * @package TenTracker
 */
get_header();

$hero_heading = get_theme_mod( 'tt_hero_heading', 'Crack Every Competitive Exam' );
$hero_sub     = get_theme_mod( 'tt_hero_subtext', 'Practice unlimited MCQ quizzes for UPSC, SSC, Banking, Railways & more. Track your progress and ace the exam.' );
$hero_btn     = get_theme_mod( 'tt_hero_btn_text', 'Start Practising Free' );
$hero_url     = get_theme_mod( 'tt_hero_btn_url', home_url( '/exams' ) );
?>

<!-- ══ HERO ══════════════════════════════════════════════ -->
<section class="tt-hero" aria-labelledby="hero-heading">
  <div class="tt-container">
    <div class="tt-hero__inner">

      <!-- Left content -->
      <div class="tt-hero__content">

        <div class="tt-hero__badge">
          <?php esc_html_e( 'Trusted by 50,000+ aspirants', 'tentracker' ); ?>
        </div>

        <h1 class="tt-hero__title" id="hero-heading">
          <?php
          // Split at a natural breakpoint for gradient accent
          $words = explode( ' ', esc_html( $hero_heading ) );
          $half  = intval( count( $words ) / 2 );
          $first = implode( ' ', array_slice( $words, 0, $half ) );
          $rest  = implode( ' ', array_slice( $words, $half ) );
          echo $first . ' <span class="accent">' . $rest . '</span>';
          ?>
        </h1>

        <p class="tt-hero__subtitle"><?php echo esc_html( $hero_sub ); ?></p>

        <div class="tt-hero__cta">
          <a href="<?php echo esc_url( $hero_url ); ?>" class="tt-btn tt-btn--primary tt-btn--lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
              <polygon points="5 3 19 12 5 21 5 3"/>
            </svg>
            <?php echo esc_html( $hero_btn ); ?>
          </a>
          <?php if ( ! is_user_logged_in() ) : ?>
          <a href="<?php echo esc_url( wp_login_url() ); ?>" class="tt-btn tt-btn--secondary tt-btn--lg">
            <?php esc_html_e( 'Login', 'tentracker' ); ?>
          </a>
          <?php endif; ?>
        </div>

        <div class="tt-hero__stats">
          <div>
            <span class="tt-hero__stat-val">10,000+</span>
            <span class="tt-hero__stat-lbl"><?php esc_html_e( 'Questions', 'tentracker' ); ?></span>
          </div>
          <div>
            <span class="tt-hero__stat-val">200+</span>
            <span class="tt-hero__stat-lbl"><?php esc_html_e( 'Quizzes', 'tentracker' ); ?></span>
          </div>
          <div>
            <span class="tt-hero__stat-val">50K+</span>
            <span class="tt-hero__stat-lbl"><?php esc_html_e( 'Students', 'tentracker' ); ?></span>
          </div>
        </div>

      </div>

      <!-- Right: demo quiz card -->
      <div class="tt-hero__visual" aria-hidden="true">
        <div class="tt-hero__card-demo">
          <div class="tt-hero__card-demo-label">📝 Sample Question</div>
          <div class="tt-hero__card-demo-q">
            Which Article of the Indian Constitution abolishes untouchability and forbids its practice in any form?
          </div>
          <div class="tt-hero__card-demo-opts">
            <div class="tt-hero__opt">
              <span class="tt-hero__opt--label">A</span> Article 14
            </div>
            <div class="tt-hero__opt tt-hero__opt--correct">
              <span class="tt-hero__opt--label">B</span> Article 17 ✓
            </div>
            <div class="tt-hero__opt">
              <span class="tt-hero__opt--label">C</span> Article 19
            </div>
            <div class="tt-hero__opt">
              <span class="tt-hero__opt--label">D</span> Article 21
            </div>
          </div>
        </div>

        <div class="tt-hero__mini-stats">
          <div class="tt-hero__mini-stat">
            <div class="tt-hero__mini-stat-val">87%</div>
            <div class="tt-hero__mini-stat-lbl">Accuracy</div>
          </div>
          <div class="tt-hero__mini-stat">
            <div class="tt-hero__mini-stat-val">42</div>
            <div class="tt-hero__mini-stat-lbl">Streak</div>
          </div>
          <div class="tt-hero__mini-stat">
            <div class="tt-hero__mini-stat-val">#12</div>
            <div class="tt-hero__mini-stat-lbl">Rank</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ══ EXAM CATEGORY STRIP ══════════════════════════════ -->
<div class="tt-cats">
  <div class="tt-container">
    <div class="tt-cats__track">
      <?php
      $cats = array(
          array( 'label' => 'UPSC',           'color' => '#2563eb' ),
          array( 'label' => 'SSC CGL',        'color' => '#06b6d4' ),
          array( 'label' => 'IBPS PO',        'color' => '#7c3aed' ),
          array( 'label' => 'Railways',       'color' => '#16a34a' ),
          array( 'label' => 'State PSC',      'color' => '#d97706' ),
          array( 'label' => 'NEET',           'color' => '#dc2626' ),
          array( 'label' => 'JEE',            'color' => '#0891b2' ),
          array( 'label' => 'Defence',        'color' => '#9333ea' ),
          array( 'label' => 'Teaching',       'color' => '#b45309' ),
          array( 'label' => 'Insurance',      'color' => '#0f766e' ),
          array( 'label' => 'Current Affairs','color' => '#1d4ed8' ),
          array( 'label' => 'GK',             'color' => '#6d28d9' ),
      );
      foreach ( $cats as $cat ) :
      ?>
        <a href="<?php echo esc_url( home_url( '/?s=' . urlencode( $cat['label'] ) ) ); ?>" class="tt-cat-chip">
          <span class="tt-cat-chip__dot" style="background:<?php echo esc_attr( $cat['color'] ); ?>"></span>
          <?php echo esc_html( $cat['label'] ); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══ EXAMS GRID ════════════════════════════════════════ -->
<section class="tt-section">
  <div class="tt-container">
    <div class="tt-section__head">
      <div>
        <p class="tt-section__eyebrow"><?php esc_html_e( 'Practice Tests', 'tentracker' ); ?></p>
        <h2 class="tt-section__title"><?php esc_html_e( 'Choose Your Exam', 'tentracker' ); ?></h2>
        <p class="tt-section__sub"><?php esc_html_e( 'Curated question banks for every major competitive exam in India.', 'tentracker' ); ?></p>
      </div>
      <a href="<?php echo esc_url( get_post_type_archive_link( 'ek_exam' ) ?: home_url( '/exams' ) ); ?>" class="tt-btn tt-btn--ghost">
        <?php esc_html_e( 'View All →', 'tentracker' ); ?>
      </a>
    </div>

    <!-- ExamKit renders the grid; CSS takes over styling -->
    <?php echo do_shortcode( '[ek_exam_list posts_per_page="12"]' ); ?>
  </div>
</section>

<!-- ══ STATS ROW ═════════════════════════════════════════ -->
<section class="tt-section tt-section--alt">
  <div class="tt-container">
    <div class="tt-stats-grid">
      <div class="tt-stat-card tt-stat-card--blue">
        <div class="tt-stat-card__icon">📚</div>
        <div class="tt-stat-card__val">10K+</div>
        <div class="tt-stat-card__lbl"><?php esc_html_e( 'Questions Available', 'tentracker' ); ?></div>
        <span class="tt-stat-card__delta tt-stat-card__delta--up">↑ Growing daily</span>
      </div>
      <div class="tt-stat-card tt-stat-card--green">
        <div class="tt-stat-card__icon">✅</div>
        <div class="tt-stat-card__val">98%</div>
        <div class="tt-stat-card__lbl"><?php esc_html_e( 'Accuracy Guarantee', 'tentracker' ); ?></div>
        <span class="tt-stat-card__delta tt-stat-card__delta--up">Expert verified</span>
      </div>
      <div class="tt-stat-card tt-stat-card--amber">
        <div class="tt-stat-card__icon">⚡</div>
        <div class="tt-stat-card__val">50K+</div>
        <div class="tt-stat-card__lbl"><?php esc_html_e( 'Active Students', 'tentracker' ); ?></div>
        <span class="tt-stat-card__delta tt-stat-card__delta--up">↑ 2k this month</span>
      </div>
      <div class="tt-stat-card tt-stat-card--purple">
        <div class="tt-stat-card__icon">🏆</div>
        <div class="tt-stat-card__val">1200+</div>
        <div class="tt-stat-card__lbl"><?php esc_html_e( 'Selections So Far', 'tentracker' ); ?></div>
        <span class="tt-stat-card__delta tt-stat-card__delta--up">↑ Success stories</span>
      </div>
    </div>
  </div>
</section>

<!-- ══ HOW IT WORKS ══════════════════════════════════════ -->
<section class="tt-section">
  <div class="tt-container">
    <div class="tt-section__head">
      <div>
        <p class="tt-section__eyebrow"><?php esc_html_e( 'Get Started', 'tentracker' ); ?></p>
        <h2 class="tt-section__title"><?php esc_html_e( 'How 10Tracker Works', 'tentracker' ); ?></h2>
      </div>
    </div>

    <div class="tt-steps">
      <div class="tt-step">
        <div class="tt-step__icon">🎯</div>
        <h3 class="tt-step__title"><?php esc_html_e( 'Pick Your Exam', 'tentracker' ); ?></h3>
        <p class="tt-step__desc"><?php esc_html_e( 'Browse our library of exams — UPSC, SSC, Banking, Railways and more. Pick the one you\'re targeting.', 'tentracker' ); ?></p>
      </div>
      <div class="tt-step">
        <div class="tt-step__icon">📝</div>
        <h3 class="tt-step__title"><?php esc_html_e( 'Attempt a Quiz', 'tentracker' ); ?></h3>
        <p class="tt-step__desc"><?php esc_html_e( 'Timed MCQ practice with instant scoring. Questions support LaTeX for math and Science subjects.', 'tentracker' ); ?></p>
      </div>
      <div class="tt-step">
        <div class="tt-step__icon">📊</div>
        <h3 class="tt-step__title"><?php esc_html_e( 'Review & Improve', 'tentracker' ); ?></h3>
        <p class="tt-step__desc"><?php esc_html_e( 'After each attempt see correct answers with detailed explanations, then target your weak areas.', 'tentracker' ); ?></p>
      </div>
      <div class="tt-step">
        <div class="tt-step__icon">🚀</div>
        <h3 class="tt-step__title"><?php esc_html_e( 'Track Progress', 'tentracker' ); ?></h3>
        <p class="tt-step__desc"><?php esc_html_e( 'Your attempts history, best scores, and accuracy trends are all in one dashboard so you stay on track.', 'tentracker' ); ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ══ TESTIMONIALS ══════════════════════════════════════ -->
<section class="tt-section tt-section--dark">
  <div class="tt-container">
    <div class="tt-section__head">
      <div>
        <p class="tt-section__eyebrow"><?php esc_html_e( 'Success Stories', 'tentracker' ); ?></p>
        <h2 class="tt-section__title"><?php esc_html_e( 'What Our Students Say', 'tentracker' ); ?></h2>
      </div>
    </div>

    <div class="tt-testimonials">
      <?php
      $testimonials = array(
          array(
              'text'   => 'I cleared SSC CGL after 3 months of daily practice on 10Tracker. The question quality is top-notch and the timer really helps with speed.',
              'name'   => 'Rahul Sharma',
              'role'   => 'SSC CGL 2024 — AIR 234',
              'init'   => 'RS',
          ),
          array(
              'text'   => 'The UPSC Polity section questions are extremely relevant. I used 10Tracker every day for my Prelims prep and it made a huge difference.',
              'name'   => 'Priya Meena',
              'role'   => 'UPSC Prelims Qualified 2024',
              'init'   => 'PM',
          ),
          array(
              'text'   => 'Best free platform for IBPS PO preparation. The banking awareness quizzes are updated with latest pattern questions.',
              'name'   => 'Amit Verma',
              'role'   => 'IBPS PO 2024',
              'init'   => 'AV',
          ),
      );
      foreach ( $testimonials as $t ) :
      ?>
        <div class="tt-testimonial">
          <div class="tt-testimonial__stars">★★★★★</div>
          <p class="tt-testimonial__text">"<?php echo esc_html( $t['text'] ); ?>"</p>
          <div class="tt-testimonial__author">
            <div class="tt-testimonial__avatar"><?php echo esc_html( $t['init'] ); ?></div>
            <div>
              <div class="tt-testimonial__name"><?php echo esc_html( $t['name'] ); ?></div>
              <div class="tt-testimonial__role"><?php echo esc_html( $t['role'] ); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ CTA BAND ══════════════════════════════════════════ -->
<section class="tt-section tt-section--alt">
  <div class="tt-container" style="text-align:center; padding-block: 0;">
    <h2 style="font-family:var(--head);font-size:clamp(1.4rem,3vw,2rem);font-weight:800;letter-spacing:-.02em;color:var(--text);margin-bottom:12px;">
      <?php esc_html_e( 'Ready to start your exam journey?', 'tentracker' ); ?>
    </h2>
    <p style="color:var(--text-3);font-size:.9375rem;margin-bottom:28px;">
      <?php esc_html_e( 'Join 50,000+ students already practising on 10Tracker. It\'s completely free.', 'tentracker' ); ?>
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="tt-btn tt-btn--primary tt-btn--lg">
        <?php esc_html_e( 'Create Free Account', 'tentracker' ); ?>
      </a>
      <a href="<?php echo esc_url( get_post_type_archive_link( 'ek_exam' ) ?: home_url( '/exams' ) ); ?>" class="tt-btn tt-btn--outline tt-btn--lg">
        <?php esc_html_e( 'Browse Exams', 'tentracker' ); ?>
      </a>
    </div>
  </div>
</section>

<?php get_footer();
