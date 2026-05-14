<?php
/**
 * Comments template
 *
 * @package TenTracker
 */

if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);padding:clamp(20px,4vw,36px);box-shadow:var(--shadow-sm);">

  <?php if ( have_comments() ) : ?>
    <h2 style="font-family:var(--head);font-size:1.1rem;font-weight:700;margin-bottom:20px;color:var(--text);">
      <?php comments_number( 'No Comments', '1 Comment', '% Comments' ); ?>
    </h2>

    <ol class="comment-list" style="list-style:none;padding:0;margin:0 0 28px;">
      <?php
      wp_list_comments( array(
          'style'       => 'ol',
          'short_ping'  => true,
          'avatar_size' => 40,
          'callback'    => 'tt_comment_callback',
      ) );
      ?>
    </ol>

    <?php
    the_comments_pagination( array(
        'prev_text' => '← Older',
        'next_text' => 'Newer →',
    ) );
    ?>

  <?php endif; ?>

  <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
    <p style="color:var(--text-3);font-size:.875rem;"><?php esc_html_e( 'Comments are closed.', 'tentracker' ); ?></p>
  <?php endif; ?>

  <?php comment_form( array(
      'title_reply'          => __( 'Leave a Comment', 'tentracker' ),
      'label_submit'         => __( 'Post Comment', 'tentracker' ),
      'class_submit'         => 'tt-btn tt-btn--primary',
      'comment_notes_before' => '',
  ) ); ?>

</div>

<?php
/**
 * Custom comment callback
 */
function tt_comment_callback( $comment, $args, $depth ) {
    $avatar = get_avatar( $comment, 40 );
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'tt-comment' ); ?> style="display:flex;gap:12px;padding:14px 0;border-bottom:1px solid var(--border);">
        <div style="flex-shrink:0;"><?php echo wp_kses_post( $avatar ); ?></div>
        <div style="flex:1;">
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                <strong style="font-size:.875rem;color:var(--text);"><?php comment_author(); ?></strong>
                <time style="font-size:.75rem;color:var(--text-4);"><?php echo esc_html( get_comment_date() ); ?></time>
                <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div>
            <?php if ( '0' === $comment->comment_approved ) : ?>
                <p style="color:var(--amber);font-size:.8125rem;"><?php esc_html_e( 'Your comment is awaiting moderation.', 'tentracker' ); ?></p>
            <?php endif; ?>
            <div style="font-size:.875rem;color:var(--text-2);line-height:1.6;"><?php comment_text(); ?></div>
        </div>
    </li>
    <?php
}
?>
