<?php
/**
 * 10Tracker Theme — functions.php
 *
 * @package TenTracker
 */

defined( 'ABSPATH' ) || exit;

define( 'TT_VERSION', '1.0.0' );
define( 'TT_DIR',     get_template_directory() );
define( 'TT_URL',     get_template_directory_uri() );

/* Load nav walker */
require_once TT_DIR . '/inc/class-tt-nav-walker.php';

/* Load template tag helpers */
require_once TT_DIR . '/inc/template-tags.php';

/* Exam meta boxes (ek_exam) */
require_once TT_DIR . '/inc/meta-exam.php';

/* ════════════════════════════════════════════════════════════
   SETUP
   ════════════════════════════════════════════════════════════ */
function tt_setup() {
    load_theme_textdomain( 'tentracker', TT_DIR . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-color-palette', array(
        array( 'name' => 'Navy',  'slug' => 'navy',  'color' => '#0a1628' ),
        array( 'name' => 'Blue',  'slug' => 'blue',  'color' => '#2563eb' ),
        array( 'name' => 'Cyan',  'slug' => 'cyan',  'color' => '#06b6d4' ),
        array( 'name' => 'White', 'slug' => 'white', 'color' => '#ffffff' ),
    ) );

    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    register_nav_menus( array(
        'primary' => __( 'Primary Navigation', 'tentracker' ),
        'footer'  => __( 'Footer Navigation',  'tentracker' ),
        'footer2' => __( 'Footer Column 2',    'tentracker' ),
        'footer3' => __( 'Footer Column 3',    'tentracker' ),
    ) );

    add_image_size( 'tt-card',   600, 360, true );
    add_image_size( 'tt-thumb',  300, 200, true );
    add_image_size( 'tt-hero',  1400, 600, true );
}
add_action( 'after_setup_theme', 'tt_setup' );

/* ════════════════════════════════════════════════════════════
   ENQUEUE
   ════════════════════════════════════════════════════════════ */
function tt_enqueue() {
    // Main stylesheet
    wp_enqueue_style(
        'tentracker',
        get_stylesheet_uri(),
        array(),
        TT_VERSION
    );

    // Dropdown nav supplement
    wp_enqueue_style(
        'tentracker-nav',
        TT_URL . '/assets/css/nav-dropdown.css',
        array( 'tentracker' ),
        TT_VERSION
    );

    // Theme JS
    wp_enqueue_script(
        'tentracker',
        TT_URL . '/assets/js/theme.js',
        array( 'jquery' ),
        TT_VERSION,
        true
    );

    wp_localize_script( 'tentracker', 'ttData', array(
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'home_url'   => home_url(),
        'nonce'      => wp_create_nonce( 'tt_nonce' ),
        'is_logged'  => is_user_logged_in() ? 1 : 0,
        'login_url'  => wp_login_url( get_permalink() ),
        'register_url' => wp_registration_url(),
    ) );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'tt_enqueue' );

/**
 * Enqueue exam page assets only on single exam pages.
 */
function tt_enqueue_exam_assets() {
    if ( ! is_singular( array( 'ek_exam', 'ek_quiz' ) ) ) {
        return;
    }

    $css_rel = '/assets/css/exam.css';
    $js_rel  = '/assets/js/exam.js';

    $css_path = TT_DIR . $css_rel;
    $js_path  = TT_DIR . $js_rel;

    wp_enqueue_style(
        'tt-exam',
        TT_URL . $css_rel,
        array( 'tentracker' ),
        file_exists( $css_path ) ? filemtime( $css_path ) : TT_VERSION
    );

    wp_enqueue_script(
        'tt-exam',
        TT_URL . $js_rel,
        array( 'jquery' ),
        file_exists( $js_path ) ? filemtime( $js_path ) : TT_VERSION,
        true
    );

    $post_id  = get_queried_object_id();
    $category = (string) get_post_meta( $post_id, 'exam_quiz_category', true );
    if ( '' === trim( $category ) ) {
        $category = (string) get_post_field( 'post_name', $post_id );
    }

    wp_localize_script( 'tt-exam', 'ttExam', array(
        'restUrl'  => rest_url( 'examtracker/v1/' ),
        'category' => $category,
        'nonce'    => wp_create_nonce( 'wp_rest' ),
        'examSlug' => (string) get_post_field( 'post_name', $post_id ),
        'examId'   => (int) $post_id,
    ) );
}
add_action( 'wp_enqueue_scripts', 'tt_enqueue_exam_assets' );

/* ════════════════════════════════════════════════════════════
   WIDGETS
   ════════════════════════════════════════════════════════════ */
function tt_register_sidebars() {
    $shared = array(
        'before_widget' => '<div class="tt-sidebar-widget" id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="tt-sidebar-widget__head"><h3 class="tt-sidebar-widget__title">',
        'after_title'   => '</h3></div><div class="tt-sidebar-widget__body">',
    );

    register_sidebar( array_merge( $shared, array(
        'name'          => __( 'Sidebar', 'tentracker' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Appears on exam/quiz pages', 'tentracker' ),
        'after_widget'  => '</div></div>',
    ) ) );

    register_sidebar( array_merge( $shared, array(
        'name'          => __( 'Footer Column 1', 'tentracker' ),
        'id'            => 'footer-1',
        'after_widget'  => '</div></div>',
    ) ) );
}
add_action( 'widgets_init', 'tt_register_sidebars' );

/* ════════════════════════════════════════════════════════════
   CONTENT WIDTH
   ════════════════════════════════════════════════════════════ */
function tt_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'tt_content_width', 900 );
}
add_action( 'after_setup_theme', 'tt_content_width', 0 );

/* ════════════════════════════════════════════════════════════
   BODY CLASSES
   ════════════════════════════════════════════════════════════ */
function tt_body_classes( $classes ) {
    if ( is_singular( 'ek_exam' ) ) $classes[] = 'tt-is-exam-page';
    if ( is_singular( 'ek_quiz' ) ) $classes[] = 'tt-is-quiz-page';
    if ( ! is_active_sidebar( 'sidebar-1' ) ) $classes[] = 'tt-no-sidebar';
    return $classes;
}
add_filter( 'body_class', 'tt_body_classes' );

/* ════════════════════════════════════════════════════════════
   EXCERPT
   ════════════════════════════════════════════════════════════ */
function tt_excerpt_length() { return 20; }
add_filter( 'excerpt_length', 'tt_excerpt_length' );

function tt_excerpt_more() { return '&hellip;'; }
add_filter( 'excerpt_more', 'tt_excerpt_more' );

/* ════════════════════════════════════════════════════════════
   TITLE SEPARATOR
   ════════════════════════════════════════════════════════════ */
function tt_document_title_separator() { return '·'; }
add_filter( 'document_title_separator', 'tt_document_title_separator' );

/* ════════════════════════════════════════════════════════════
   BREADCRUMBS helper  (no plugin dependency)
   ════════════════════════════════════════════════════════════ */
function tt_breadcrumbs() {
    if ( is_front_page() ) return;

    $sep = '<span class="tt-breadcrumb__sep">›</span>';
    $out = '<nav class="tt-breadcrumb" aria-label="Breadcrumb">';
    $out .= '<a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Home', 'tentracker' ) . '</a>';
    $out .= $sep;

    if ( is_singular( 'ek_quiz' ) ) {
        $exam_id = get_post_meta( get_the_ID(), '_ek_exam_id', true );
        if ( $exam_id ) {
            $out .= '<a href="' . esc_url( get_permalink( $exam_id ) ) . '">' . esc_html( get_the_title( $exam_id ) ) . '</a>';
            $out .= $sep;
        }
        $out .= '<span class="tt-breadcrumb__current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_singular( 'ek_exam' ) ) {
        $out .= '<a href="' . esc_url( get_post_type_archive_link( 'ek_exam' ) ) . '">' . __( 'Exams', 'tentracker' ) . '</a>';
        $out .= $sep;
        $out .= '<span class="tt-breadcrumb__current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_singular() ) {
        $out .= '<span class="tt-breadcrumb__current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_archive() ) {
        $out .= '<span class="tt-breadcrumb__current">' . esc_html( get_the_archive_title() ) . '</span>';
    }

    $out .= '</nav>';
    echo $out; // phpcs:ignore WordPress.Security.EscapeOutput
}

/* ════════════════════════════════════════════════════════════
   TEMPLATE HELPERS
   ════════════════════════════════════════════════════════════ */

/**
 * Echo the site logo or text fallback.
 */
function tt_the_logo() {
    if ( has_custom_logo() ) {
        the_custom_logo();
    } else {
        $name = get_bloginfo( 'name' );
        $initial = mb_strtoupper( mb_substr( $name, 0, 2 ) );
        echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="tt-logo" rel="home">';
        echo '<div class="tt-logo__mark">' . esc_html( $initial ) . '</div>';
        echo '<span class="tt-logo__text">' . esc_html( $name ) . '</span>';
        echo '</a>';
    }
}

/**
 * Render primary nav with fallback.
 */
function tt_primary_nav() {
    wp_nav_menu( array(
        'theme_location'  => 'primary',
        'menu_class'      => 'tt-nav',
        'container'       => false,
        'fallback_cb'     => 'tt_nav_fallback',
        'depth'           => 2,
        'walker'          => class_exists( 'TT_Nav_Walker' ) ? new TT_Nav_Walker() : null,
    ) );
}

function tt_nav_fallback() {
    echo '<ul class="tt-nav">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . __( 'Home', 'tentracker' ) . '</a></li>';
    $exams_url = get_post_type_archive_link( 'ek_exam' );
    if ( $exams_url ) {
        echo '<li><a href="' . esc_url( $exams_url ) . '">' . __( 'Exams', 'tentracker' ) . '</a></li>';
    }
    echo '<li><a href="' . esc_url( home_url( '/quizzes' ) ) . '">' . __( 'Practice', 'tentracker' ) . '</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/my-attempts' ) ) . '">' . __( 'My Progress', 'tentracker' ) . '</a></li>';
    echo '</ul>';
}

/* ════════════════════════════════════════════════════════════
   CUSTOMIZER
   ════════════════════════════════════════════════════════════ */
function tt_customizer( $wp_customize ) {

    /* ── Hero section ── */
    $wp_customize->add_section( 'tt_hero', array(
        'title'    => __( 'Hero Section', 'tentracker' ),
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'tt_hero_heading', array(
        'default'           => __( 'Crack Every Competitive Exam', 'tentracker' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'tt_hero_heading', array(
        'label'   => __( 'Hero Heading', 'tentracker' ),
        'section' => 'tt_hero',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tt_hero_subtext', array(
        'default'           => __( 'Practice unlimited MCQ quizzes for UPSC, SSC, Banking, Railways & more. Track your progress and ace the exam.', 'tentracker' ),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'tt_hero_subtext', array(
        'label'   => __( 'Hero Sub-text', 'tentracker' ),
        'section' => 'tt_hero',
        'type'    => 'textarea',
    ) );

    $wp_customize->add_setting( 'tt_hero_btn_text', array(
        'default'           => __( 'Start Practising Free', 'tentracker' ),
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tt_hero_btn_text', array(
        'label'   => __( 'CTA Button Text', 'tentracker' ),
        'section' => 'tt_hero',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'tt_hero_btn_url', array(
        'default'           => get_post_type_archive_link( 'ek_exam' ) ?: home_url( '/exams' ),
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'tt_hero_btn_url', array(
        'label'   => __( 'CTA Button URL', 'tentracker' ),
        'section' => 'tt_hero',
        'type'    => 'url',
    ) );

    /* ── Footer ── */
    $wp_customize->add_section( 'tt_footer', array(
        'title'    => __( 'Footer', 'tentracker' ),
        'priority' => 80,
    ) );

    $wp_customize->add_setting( 'tt_footer_tagline', array(
        'default'           => __( 'India\'s smartest exam prep platform. Unlimited MCQ practice, real-time analytics, and expert-curated content.', 'tentracker' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'tt_footer_tagline', array(
        'label'   => __( 'Footer Tagline', 'tentracker' ),
        'section' => 'tt_footer',
        'type'    => 'textarea',
    ) );
}
add_action( 'customize_register', 'tt_customizer' );

/* ════════════════════════════════════════════════════════════
   EXAM CATEGORY COLORS  (for the strip)
   ════════════════════════════════════════════════════════════ */
function tt_exam_dot_color( $post_id ) {
    $colors = array( '#2563eb','#06b6d4','#7c3aed','#16a34a','#d97706','#dc2626','#0891b2','#9333ea' );
    return $colors[ $post_id % count( $colors ) ];
}

/**
 * Return category terms that can reasonably represent an exam.
 *
 * Editors usually keep the exam post slug, quiz category meta, and article
 * category slug aligned (for example: upsc-prelims). This helper accepts all
 * of those signals so the front end remains useful even if only one is set.
 */
function tt_exam_get_category_terms( $exam_id ) {
    $exam_id = absint( $exam_id );
    if ( ! $exam_id ) {
        return array();
    }

    $sources = array(
        (string) get_post_field( 'post_name', $exam_id ),
        (string) get_post_meta( $exam_id, 'exam_quiz_category', true ),
        (string) get_the_title( $exam_id ),
    );

    $terms_by_id = array();
    $assigned    = get_the_terms( $exam_id, 'category' );
    if ( is_array( $assigned ) ) {
        foreach ( $assigned as $term ) {
            if ( $term instanceof WP_Term ) {
                $terms_by_id[ $term->term_id ] = $term;
            }
        }
    }

    foreach ( $sources as $source ) {
        $source = trim( $source );
        if ( '' === $source ) {
            continue;
        }

        $slug_candidates = array_unique( array_filter( array(
            sanitize_title( $source ),
            str_replace( '_', '-', sanitize_title( $source ) ),
        ) ) );

        foreach ( $slug_candidates as $slug ) {
            $term = get_category_by_slug( $slug );
            if ( $term instanceof WP_Term ) {
                $terms_by_id[ $term->term_id ] = $term;
            }
        }

        $term = get_term_by( 'name', $source, 'category' );
        if ( $term instanceof WP_Term ) {
            $terms_by_id[ $term->term_id ] = $term;
        }
    }

    return array_values( $terms_by_id );
}

/**
 * Get the Current Affairs category term, if present.
 */
function tt_exam_get_current_affairs_term() {
    $term = get_category_by_slug( 'current-affairs' );
    if ( $term instanceof WP_Term ) {
        return $term;
    }

    $term = get_term_by( 'name', 'Current Affairs', 'category' );
    return ( $term instanceof WP_Term ) ? $term : null;
}

/**
 * Fetch articles related to an exam category.
 *
 * When $require_current_affairs is true, returned posts must be in both the
 * exam category and the Current Affairs category.
 */
function tt_exam_get_related_posts( $exam_id, $require_current_affairs = false ) {
    $exam_terms = tt_exam_get_category_terms( $exam_id );
    if ( empty( $exam_terms ) ) {
        return array();
    }

    $tax_query = array(
        array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => wp_list_pluck( $exam_terms, 'term_id' ),
            'operator' => 'IN',
        ),
    );

    if ( $require_current_affairs ) {
        $current_affairs = tt_exam_get_current_affairs_term();
        if ( ! $current_affairs instanceof WP_Term ) {
            return array();
        }

        $tax_query['relation'] = 'AND';
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => array( $current_affairs->term_id ),
            'operator' => 'IN',
        );
    }

    return get_posts( array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => -1,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'tax_query'           => $tax_query,
    ) );
}

/**
 * Fetch quizzes/tests connected to an exam by ExamKit meta or matching category.
 */
function tt_exam_get_quiz_posts( $exam_id ) {
    $exam_id = absint( $exam_id );
    if ( ! $exam_id ) {
        return array();
    }

    $posts_by_id = array();
    $add_posts   = static function ( $posts ) use ( &$posts_by_id ) {
        foreach ( (array) $posts as $post ) {
            if ( $post instanceof WP_Post ) {
                $posts_by_id[ $post->ID ] = $post;
            }
        }
    };

    $add_posts( get_posts( array(
        'post_type'      => 'ek_quiz',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
        'meta_query'     => array(
            'relation' => 'OR',
            array( 'key' => '_ek_exam_id', 'value' => $exam_id, 'compare' => '=' ),
            array( 'key' => 'ek_exam_id',  'value' => $exam_id, 'compare' => '=' ),
            array( 'key' => 'exam_id',     'value' => $exam_id, 'compare' => '=' ),
        ),
    ) ) );

    $exam_terms = tt_exam_get_category_terms( $exam_id );
    if ( ! empty( $exam_terms ) ) {
        $add_posts( get_posts( array(
            'post_type'      => 'ek_quiz',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => wp_list_pluck( $exam_terms, 'term_id' ),
                    'operator' => 'IN',
                ),
            ),
        ) ) );
    }

    $category_values = array_filter( array_unique( array(
        (string) get_post_meta( $exam_id, 'exam_quiz_category', true ),
        (string) get_post_field( 'post_name', $exam_id ),
        sanitize_title( (string) get_post_meta( $exam_id, 'exam_quiz_category', true ) ),
    ) ) );

    if ( ! empty( $category_values ) ) {
        $add_posts( get_posts( array(
            'post_type'      => 'ek_quiz',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => 'category',       'value' => $category_values, 'compare' => 'IN' ),
                array( 'key' => 'quiz_category',  'value' => $category_values, 'compare' => 'IN' ),
                array( 'key' => '_quiz_category', 'value' => $category_values, 'compare' => 'IN' ),
                array( 'key' => 'ek_category',    'value' => $category_values, 'compare' => 'IN' ),
                array( 'key' => '_ek_category',   'value' => $category_values, 'compare' => 'IN' ),
            ),
        ) ) );
    }

    $quizzes = array_values( $posts_by_id );
    usort( $quizzes, static function ( $a, $b ) {
        $menu = (int) $a->menu_order <=> (int) $b->menu_order;
        return 0 !== $menu ? $menu : strcasecmp( $a->post_title, $b->post_title );
    } );

    return $quizzes;
}

/**
 * Resolve a quiz question count from common ExamKit/custom meta keys.
 */
function tt_exam_get_quiz_question_count( $quiz_id ) {
    foreach ( array( '_ek_question_count', 'ek_question_count', 'question_count', 'questions_count', 'total_questions', '_question_count' ) as $key ) {
        $value = get_post_meta( $quiz_id, $key, true );
        if ( is_numeric( $value ) ) {
            return max( 0, (int) $value );
        }
    }

    foreach ( array( '_ek_questions', 'ek_questions', 'questions' ) as $key ) {
        $value = get_post_meta( $quiz_id, $key, true );
        if ( is_array( $value ) ) {
            return count( $value );
        }
        if ( is_string( $value ) && '' !== trim( $value ) ) {
            $decoded = json_decode( $value, true );
            if ( is_array( $decoded ) ) {
                return count( $decoded );
            }
        }
    }

    return 0;
}

/**
 * Resolve a display difficulty for a quiz.
 */
function tt_exam_get_quiz_difficulty( $quiz_id ) {
    foreach ( array( '_ek_difficulty', 'ek_difficulty', 'difficulty', 'level', '_level' ) as $key ) {
        $value = trim( (string) get_post_meta( $quiz_id, $key, true ) );
        if ( '' !== $value ) {
            return sanitize_text_field( $value );
        }
    }

    return '';
}

/**
 * Resolve a duration label for a quiz when present.
 */
function tt_exam_get_quiz_duration( $quiz_id ) {
    foreach ( array( '_ek_duration', 'ek_duration', 'duration', 'time_limit', '_time_limit' ) as $key ) {
        $value = trim( (string) get_post_meta( $quiz_id, $key, true ) );
        if ( '' === $value ) {
            continue;
        }

        if ( is_numeric( $value ) ) {
            return sprintf( _n( '%s min', '%s mins', (int) $value, 'tentracker' ), number_format_i18n( (int) $value ) );
        }

        return sanitize_text_field( $value );
    }

    return '';
}

/**
 * Resolve the chapter/topic label for a quiz when present.
 */
function tt_exam_get_quiz_chapter( $quiz_id ) {
    foreach ( array( '_ek_chapter', 'ek_chapter', 'quiz_chapter', '_quiz_chapter', 'chapter', '_chapter', 'topic', '_topic', 'lesson', '_lesson' ) as $key ) {
        $value = trim( (string) get_post_meta( $quiz_id, $key, true ) );
        if ( '' !== $value ) {
            return sanitize_text_field( $value );
        }
    }

    return '';
}

/**
 * Resolve category labels for quiz display and filtering.
 */
function tt_exam_get_quiz_category_labels( $quiz_id ) {
    $labels = array();
    $terms  = get_the_terms( $quiz_id, 'category' );

    if ( is_array( $terms ) ) {
        foreach ( $terms as $term ) {
            if ( $term instanceof WP_Term ) {
                $labels[] = $term->name;
            }
        }
    }

    foreach ( array( 'category', 'quiz_category', '_quiz_category', 'ek_category', '_ek_category' ) as $key ) {
        $value = trim( (string) get_post_meta( $quiz_id, $key, true ) );
        if ( '' !== $value ) {
            $labels[] = $value;
        }
    }

    return array_values( array_unique( array_filter( array_map( 'sanitize_text_field', $labels ) ) ) );
}

/**
 * Build quick stats for the exam hero.
 */
function tt_exam_get_quiz_stats( $quizzes ) {
    $question_count = 0;
    $difficulty    = array();

    foreach ( (array) $quizzes as $quiz ) {
        if ( ! $quiz instanceof WP_Post ) {
            continue;
        }

        $question_count += tt_exam_get_quiz_question_count( $quiz->ID );
        $diff            = strtolower( tt_exam_get_quiz_difficulty( $quiz->ID ) );
        if ( '' !== $diff ) {
            $difficulty[ $diff ] = isset( $difficulty[ $diff ] ) ? $difficulty[ $diff ] + 1 : 1;
        }
    }

    $difficulty_parts = array();
    foreach ( $difficulty as $label => $count ) {
        $difficulty_parts[] = ucwords( $label ) . ' ' . (int) $count;
    }

    return array(
        'quiz_count'     => count( (array) $quizzes ),
        'question_count' => $question_count,
        'difficulty_mix' => $difficulty_parts ? implode( ', ', $difficulty_parts ) : '',
    );
}

/**
 * Fetch quizzes related to a quiz by same exam, chapter, or category.
 */
function tt_quiz_get_related_quizzes( $quiz_id, $limit = 6 ) {
    $quiz_id = absint( $quiz_id );
    if ( ! $quiz_id ) {
        return array();
    }

    $posts_by_id = array();
    $add_posts   = static function ( $posts ) use ( &$posts_by_id, $quiz_id, $limit ) {
        foreach ( (array) $posts as $post ) {
            if ( ! $post instanceof WP_Post || $post->ID === $quiz_id ) {
                continue;
            }
            $posts_by_id[ $post->ID ] = $post;
            if ( count( $posts_by_id ) >= $limit * 3 ) {
                break;
            }
        }
    };

    $exam_id = 0;
    foreach ( array( '_ek_exam_id', 'ek_exam_id', 'exam_id' ) as $key ) {
        $value = absint( get_post_meta( $quiz_id, $key, true ) );
        if ( $value ) {
            $exam_id = $value;
            break;
        }
    }

    if ( $exam_id ) {
        $add_posts( get_posts( array(
            'post_type'      => 'ek_quiz',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'exclude'        => array( $quiz_id ),
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => '_ek_exam_id', 'value' => $exam_id, 'compare' => '=' ),
                array( 'key' => 'ek_exam_id',  'value' => $exam_id, 'compare' => '=' ),
                array( 'key' => 'exam_id',     'value' => $exam_id, 'compare' => '=' ),
            ),
        ) ) );
    }

    $chapter = tt_exam_get_quiz_chapter( $quiz_id );
    if ( $chapter ) {
        $add_posts( get_posts( array(
            'post_type'      => 'ek_quiz',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'exclude'        => array( $quiz_id ),
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'meta_query'     => array(
                'relation' => 'OR',
                array( 'key' => '_ek_chapter',    'value' => $chapter, 'compare' => '=' ),
                array( 'key' => 'ek_chapter',     'value' => $chapter, 'compare' => '=' ),
                array( 'key' => 'quiz_chapter',   'value' => $chapter, 'compare' => '=' ),
                array( 'key' => '_quiz_chapter',  'value' => $chapter, 'compare' => '=' ),
                array( 'key' => 'chapter',        'value' => $chapter, 'compare' => '=' ),
                array( 'key' => 'topic',          'value' => $chapter, 'compare' => '=' ),
            ),
        ) ) );
    }

    $terms = get_the_terms( $quiz_id, 'category' );
    if ( is_array( $terms ) && ! empty( $terms ) ) {
        $add_posts( get_posts( array(
            'post_type'      => 'ek_quiz',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'exclude'        => array( $quiz_id ),
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => wp_list_pluck( $terms, 'term_id' ),
                    'operator' => 'IN',
                ),
            ),
        ) ) );
    }

    $related = array_values( $posts_by_id );
    usort( $related, static function ( $a, $b ) {
        $menu = (int) $a->menu_order <=> (int) $b->menu_order;
        return 0 !== $menu ? $menu : strcasecmp( $a->post_title, $b->post_title );
    } );

    return array_slice( $related, 0, $limit );
}

/**
 * Render the exam quiz/test table.
 */
function tt_exam_render_quiz_table( $quizzes, $exam_id ) {
    if ( empty( $quizzes ) ) {
        ?>
        <div class="tt-exam-empty">
            <?php esc_html_e( 'No quizzes or tests are linked with this exam yet.', 'tentracker' ); ?>
        </div>
        <?php
        return;
    }

    $difficulty_options = array();
    $topic_options      = array();
    foreach ( $quizzes as $quiz ) {
        if ( ! $quiz instanceof WP_Post ) {
            continue;
        }
        $difficulty = tt_exam_get_quiz_difficulty( $quiz->ID );
        $chapter    = tt_exam_get_quiz_chapter( $quiz->ID );
        $categories = tt_exam_get_quiz_category_labels( $quiz->ID );
        if ( $difficulty ) {
            $difficulty_options[ strtolower( $difficulty ) ] = $difficulty;
        }
        if ( $chapter ) {
            $topic_options[ sanitize_title( $chapter ) ] = $chapter;
        } elseif ( ! empty( $categories ) ) {
            $topic_options[ sanitize_title( $categories[0] ) ] = $categories[0];
        }
    }
    ?>

    <div class="tt-quiz-browser" data-tt-quiz-browser data-per-page="20">
        <div class="tt-quiz-toolbar" role="region" aria-label="<?php esc_attr_e( 'Quiz search and filters', 'tentracker' ); ?>">
            <label class="tt-quiz-search">
                <span class="tt-quiz-search__icon" aria-hidden="true">Search</span>
                <input type="search" data-tt-quiz-search placeholder="<?php esc_attr_e( 'Search quiz or test name...', 'tentracker' ); ?>" autocomplete="off">
            </label>

            <?php if ( ! empty( $difficulty_options ) ) : ?>
                <label class="tt-quiz-filter">
                    <span><?php esc_html_e( 'Difficulty', 'tentracker' ); ?></span>
                    <select data-tt-quiz-difficulty>
                        <option value=""><?php esc_html_e( 'All', 'tentracker' ); ?></option>
                        <?php foreach ( $difficulty_options as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endif; ?>

            <?php if ( ! empty( $topic_options ) ) : ?>
                <label class="tt-quiz-filter">
                    <span><?php esc_html_e( 'Chapter/Category', 'tentracker' ); ?></span>
                    <select data-tt-quiz-topic>
                        <option value=""><?php esc_html_e( 'All', 'tentracker' ); ?></option>
                        <?php foreach ( $topic_options as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endif; ?>

            <button class="tt-quiz-reset" type="button" data-tt-quiz-reset><?php esc_html_e( 'Reset', 'tentracker' ); ?></button>
        </div>

        <div class="tt-quiz-table-meta">
            <span data-tt-quiz-count><?php echo esc_html( sprintf( _n( '%s quiz', '%s quizzes', count( $quizzes ), 'tentracker' ), number_format_i18n( count( $quizzes ) ) ) ); ?></span>
            <span><?php esc_html_e( '20 per page', 'tentracker' ); ?></span>
        </div>

        <div class="tt-quiz-table-shell">
            <table class="tt-quiz-table tt-quiz-table--browser">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php esc_html_e( 'Quiz/Test', 'tentracker' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Questions', 'tentracker' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Difficulty', 'tentracker' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Chapter/Category', 'tentracker' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Duration', 'tentracker' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Action', 'tentracker' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $quizzes as $index => $quiz ) :
            if ( ! $quiz instanceof WP_Post ) {
                continue;
            }

            $question_count = tt_exam_get_quiz_question_count( $quiz->ID );
            $difficulty     = tt_exam_get_quiz_difficulty( $quiz->ID );
            $duration       = tt_exam_get_quiz_duration( $quiz->ID );
            $excerpt        = has_excerpt( $quiz ) ? get_the_excerpt( $quiz ) : wp_trim_words( wp_strip_all_tags( $quiz->post_content ), 22 );
            $difficulty_key = $difficulty ? sanitize_html_class( strtolower( $difficulty ) ) : 'na';
            $chapter        = tt_exam_get_quiz_chapter( $quiz->ID );
            $categories     = tt_exam_get_quiz_category_labels( $quiz->ID );
            $topic_label    = $chapter ?: ( ! empty( $categories ) ? $categories[0] : '' );
            $topic_key      = sanitize_title( $topic_label );
            $search_text    = trim( get_the_title( $quiz ) . ' ' . $excerpt . ' ' . $difficulty . ' ' . $topic_label . ' ' . implode( ' ', $categories ) );
            ?>
                        <tr
                            data-tt-quiz-row
                            data-search="<?php echo esc_attr( strtolower( $search_text ) ); ?>"
                            data-difficulty="<?php echo esc_attr( strtolower( $difficulty ) ); ?>"
                            data-topic="<?php echo esc_attr( $topic_key ); ?>"
                        >
                            <td data-label="#" class="tt-quiz-col-num"><span class="tt-quiz-index"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span></td>
                            <td data-label="<?php esc_attr_e( 'Quiz/Test', 'tentracker' ); ?>" class="tt-quiz-col-name">
                                <a class="tt-quiz-name" href="<?php echo esc_url( get_permalink( $quiz ) ); ?>"><?php echo esc_html( get_the_title( $quiz ) ); ?></a>
                                <?php if ( $excerpt ) : ?>
                                    <span class="tt-quiz-desc"><?php echo esc_html( $excerpt ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Questions', 'tentracker' ); ?>" class="tt-quiz-col-qs">
                                <?php echo esc_html( $question_count ? number_format_i18n( $question_count ) : __( 'Soon', 'tentracker' ) ); ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Difficulty', 'tentracker' ); ?>" class="tt-quiz-col-diff">
                                <span class="tt-diff tt-diff--<?php echo esc_attr( $difficulty_key ); ?>"><?php echo esc_html( $difficulty ?: __( 'Mixed', 'tentracker' ) ); ?></span>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Chapter/Category', 'tentracker' ); ?>" class="tt-quiz-col-topic">
                                <?php echo esc_html( $topic_label ?: __( 'General', 'tentracker' ) ); ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Duration', 'tentracker' ); ?>" class="tt-quiz-col-time">
                                <?php echo esc_html( $duration ?: __( 'Flexible', 'tentracker' ) ); ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Action', 'tentracker' ); ?>" class="tt-quiz-col-action">
                                <a class="tt-quiz-action" href="<?php echo esc_url( get_permalink( $quiz ) ); ?>"><?php esc_html_e( 'Start', 'tentracker' ); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="tt-quiz-empty" data-tt-quiz-empty hidden>
            <?php esc_html_e( 'No quizzes match your search or filters.', 'tentracker' ); ?>
        </div>

        <div class="tt-quiz-pagination" data-tt-quiz-pagination aria-label="<?php esc_attr_e( 'Quiz pagination', 'tentracker' ); ?>"></div>
    </div>
    <?php
}

/**
 * Render compact related quiz cards.
 */
function tt_quiz_render_related_cards( $quizzes ) {
    if ( empty( $quizzes ) ) {
        return;
    }
    ?>
    <div class="tt-related-quiz-grid">
        <?php foreach ( $quizzes as $quiz ) :
            if ( ! $quiz instanceof WP_Post ) {
                continue;
            }
            $question_count = tt_exam_get_quiz_question_count( $quiz->ID );
            $difficulty     = tt_exam_get_quiz_difficulty( $quiz->ID );
            $chapter        = tt_exam_get_quiz_chapter( $quiz->ID );
            ?>
            <article class="tt-related-quiz-card">
                <div class="tt-related-quiz-card__meta">
                    <?php if ( $difficulty ) : ?>
                        <span><?php echo esc_html( $difficulty ); ?></span>
                    <?php endif; ?>
                    <?php if ( $question_count ) : ?>
                        <span><?php echo esc_html( sprintf( _n( '%s question', '%s questions', $question_count, 'tentracker' ), number_format_i18n( $question_count ) ) ); ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="tt-related-quiz-card__title">
                    <a href="<?php echo esc_url( get_permalink( $quiz ) ); ?>"><?php echo esc_html( get_the_title( $quiz ) ); ?></a>
                </h3>
                <?php if ( $chapter ) : ?>
                    <p class="tt-related-quiz-card__chapter"><?php echo esc_html( $chapter ); ?></p>
                <?php endif; ?>
                <a class="tt-related-quiz-card__link" href="<?php echo esc_url( get_permalink( $quiz ) ); ?>">
                    <?php esc_html_e( 'Practice next', 'tentracker' ); ?>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Render article cards for exam sections.
 */
function tt_exam_render_article_cards( $posts, $empty_message ) {
    if ( empty( $posts ) ) {
        ?>
        <div class="tt-exam-empty">
            <?php echo esc_html( $empty_message ); ?>
        </div>
        <?php
        return;
    }
    ?>

    <div class="tt-post-grid">
        <?php foreach ( $posts as $article ) :
            if ( ! $article instanceof WP_Post ) {
                continue;
            }

            $categories     = get_the_category( $article->ID );
            $category_names = array_slice( wp_list_pluck( (array) $categories, 'name' ), 0, 2 );
            $excerpt        = has_excerpt( $article ) ? get_the_excerpt( $article ) : wp_trim_words( wp_strip_all_tags( $article->post_content ), 24 );
            ?>
            <article class="tt-post-card">
                <a class="tt-post-card__media" href="<?php echo esc_url( get_permalink( $article ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $article ) ); ?>">
                    <?php if ( has_post_thumbnail( $article ) ) : ?>
                        <?php echo get_the_post_thumbnail( $article, 'tt-thumb', array( 'class' => 'tt-post-card__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
                    <?php else : ?>
                        <span class="tt-post-card__placeholder"><?php echo esc_html( mb_strtoupper( mb_substr( get_the_title( $article ), 0, 1 ) ) ); ?></span>
                    <?php endif; ?>
                </a>

                <div class="tt-post-card__body">
                    <div class="tt-post-card__meta">
                        <?php if ( ! empty( $category_names ) ) : ?>
                            <span><?php echo esc_html( implode( ' / ', $category_names ) ); ?></span>
                        <?php endif; ?>
                        <span><?php echo esc_html( get_the_date( '', $article ) ); ?></span>
                    </div>

                    <h3 class="tt-post-card__title">
                        <a href="<?php echo esc_url( get_permalink( $article ) ); ?>"><?php echo esc_html( get_the_title( $article ) ); ?></a>
                    </h3>

                    <?php if ( $excerpt ) : ?>
                        <p class="tt-post-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <?php endif; ?>

                    <a class="tt-post-card__link" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
                        <?php esc_html_e( 'Read article', 'tentracker' ); ?>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
}

/* ════════════════════════════════════════════════════════════
   REMOVE DEFAULT WP EMOJI
   ════════════════════════════════════════════════════════════ */
remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles',     'print_emoji_styles' );
remove_action( 'admin_print_styles',  'print_emoji_styles' );
