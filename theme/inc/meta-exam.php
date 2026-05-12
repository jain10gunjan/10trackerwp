<?php
/**
 * Exam meta boxes for ek_exam post type.
 *
 * Stores data in plain post meta so it's editable without ACF.
 * If ACF is installed, you can map these fields to the same meta keys.
 *
 * @package TenTracker
 */
defined( 'ABSPATH' ) || exit;

/**
 * Register meta boxes for ek_exam.
 */
function tt_exam_register_meta_boxes() {
    add_meta_box(
        'tt_exam_quiz_category',
        __( 'Exam: Quiz Category', 'tentracker' ),
        'tt_exam_metabox_quiz_category',
        'ek_exam',
        'side',
        'default'
    );

    add_meta_box(
        'tt_exam_about',
        __( 'Exam: About', 'tentracker' ),
        'tt_exam_metabox_about',
        'ek_exam',
        'normal',
        'high'
    );

    add_meta_box(
        'tt_exam_current_affairs',
        __( 'Exam: Current Affairs', 'tentracker' ),
        'tt_exam_metabox_current_affairs',
        'ek_exam',
        'normal',
        'default'
    );

    add_meta_box(
        'tt_exam_syllabus',
        __( 'Exam: Syllabus (optional)', 'tentracker' ),
        'tt_exam_metabox_syllabus',
        'ek_exam',
        'normal',
        'default'
    );

    add_meta_box(
        'tt_exam_accordion_extra',
        __( 'Exam: Custom Accordions', 'tentracker' ),
        'tt_exam_metabox_accordion_extra',
        'ek_exam',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'tt_exam_register_meta_boxes' );

function tt_exam_nonce_field() {
    wp_nonce_field( 'tt_exam_meta_save', 'tt_exam_meta_nonce' );
}

function tt_exam_metabox_quiz_category( $post ) {
    tt_exam_nonce_field();
    $val = (string) get_post_meta( $post->ID, 'exam_quiz_category', true );
    ?>
    <p style="margin-top:0;">
        <label for="tt_exam_quiz_category_field" style="display:block;font-weight:600;margin-bottom:6px;">
            <?php esc_html_e( 'Quiz category slug', 'tentracker' ); ?>
        </label>
        <input
            type="text"
            id="tt_exam_quiz_category_field"
            name="exam_quiz_category"
            value="<?php echo esc_attr( $val ); ?>"
            placeholder="<?php esc_attr_e( 'e.g. UPSC-PRELIMS', 'tentracker' ); ?>"
            style="width:100%;"
        />
    </p>
    <p class="description">
        <?php esc_html_e( 'Used to load quizzes via REST: /wp-json/examtracker/v1/questions?category=...', 'tentracker' ); ?>
    </p>
    <?php
}

function tt_exam_editor( $field_name, $meta_key, $post_id ) {
    $content = (string) get_post_meta( $post_id, $meta_key, true );
    wp_editor(
        $content,
        $field_name,
        array(
            'textarea_name' => $meta_key,
            'media_buttons' => true,
            'teeny'         => false,
            'textarea_rows' => 10,
        )
    );
}

function tt_exam_metabox_about( $post ) {
    tt_exam_nonce_field();
    tt_exam_editor( 'tt_exam_about_editor', 'exam_about', $post->ID );
}

function tt_exam_metabox_current_affairs( $post ) {
    tt_exam_nonce_field();
    tt_exam_editor( 'tt_exam_current_affairs_editor', 'exam_current_affairs', $post->ID );
}

function tt_exam_metabox_syllabus( $post ) {
    tt_exam_nonce_field();
    tt_exam_editor( 'tt_exam_syllabus_editor', 'exam_syllabus', $post->ID );
}

function tt_exam_metabox_accordion_extra( $post ) {
    tt_exam_nonce_field();
    $raw = (string) get_post_meta( $post->ID, 'exam_accordion_extra', true );
    if ( '' === trim( $raw ) ) {
        $raw = '[]';
    }
    ?>
    <p class="description" style="margin-top:0;">
        <?php esc_html_e( 'Add unlimited custom accordion sections (title + content).', 'tentracker' ); ?>
    </p>

    <div id="tt-exam-extra-accordion"></div>
    <textarea name="exam_accordion_extra" id="tt-exam-extra-accordion-json" style="display:none;"><?php echo esc_textarea( $raw ); ?></textarea>

    <p style="margin-top:10px;">
        <button type="button" class="button" id="tt-exam-extra-add"><?php esc_html_e( '+ Add Accordion', 'tentracker' ); ?></button>
    </p>

    <style>
        .tt-exam-extra-item { border: 1px solid #dcdcde; border-radius: 8px; padding: 12px; margin: 10px 0; background: #fff; }
        .tt-exam-extra-row { display:flex; gap:10px; align-items:flex-start; }
        .tt-exam-extra-row > * { flex:1; }
        .tt-exam-extra-actions { display:flex; gap:8px; margin-top:10px; }
        .tt-exam-extra-title { width:100%; }
        .tt-exam-extra-content { width:100%; min-height: 110px; }
        .tt-exam-extra-handle { cursor: move; user-select:none; padding: 6px 10px; background:#f6f7f7; border:1px solid #dcdcde; border-radius: 6px; }
        .tt-exam-extra-actions .button-link-delete { color:#b32d2e; }
    </style>

    <script>
    (function() {
      var mount = document.getElementById('tt-exam-extra-accordion');
      var jsonEl = document.getElementById('tt-exam-extra-accordion-json');
      var addBtn = document.getElementById('tt-exam-extra-add');
      if (!mount || !jsonEl || !addBtn) return;

      function safeParse(val) {
        try { return JSON.parse(val || '[]'); } catch (e) { return []; }
      }
      function safeStringify(obj) {
        try { return JSON.stringify(obj || []); } catch (e) { return '[]'; }
      }
      function syncToTextarea() {
        var items = [];
        mount.querySelectorAll('[data-tt-extra-item]').forEach(function(node) {
          var title = (node.querySelector('[data-tt-extra-title]') || {}).value || '';
          var content = (node.querySelector('[data-tt-extra-content]') || {}).value || '';
          if (title.trim() === '' && content.trim() === '') return;
          items.push({ title: title, content: content });
        });
        jsonEl.value = safeStringify(items);
      }

      function createItem(item) {
        var wrap = document.createElement('div');
        wrap.className = 'tt-exam-extra-item';
        wrap.setAttribute('data-tt-extra-item', '1');
        wrap.innerHTML = ''
          + '<div class="tt-exam-extra-row">'
          + '  <div style="flex:0 0 auto;">'
          + '    <div class="tt-exam-extra-handle" title="Drag to reorder">↕</div>'
          + '  </div>'
          + '  <div>'
          + '    <label style="display:block;font-weight:600;margin-bottom:6px;">Title</label>'
          + '    <input type="text" class="tt-exam-extra-title" data-tt-extra-title value="">'
          + '    <div style="height:10px;"></div>'
          + '    <label style="display:block;font-weight:600;margin-bottom:6px;">Content</label>'
          + '    <textarea class="tt-exam-extra-content" data-tt-extra-content></textarea>'
          + '    <div class="tt-exam-extra-actions">'
          + '      <button type="button" class="button button-secondary" data-tt-extra-up>Move up</button>'
          + '      <button type="button" class="button button-secondary" data-tt-extra-down>Move down</button>'
          + '      <button type="button" class="button-link-delete" data-tt-extra-delete>Remove</button>'
          + '    </div>'
          + '  </div>'
          + '</div>';

        var titleEl = wrap.querySelector('[data-tt-extra-title]');
        var contentEl = wrap.querySelector('[data-tt-extra-content]');
        titleEl.value = (item && item.title) ? item.title : '';
        contentEl.value = (item && item.content) ? item.content : '';

        wrap.addEventListener('input', function() { syncToTextarea(); });
        wrap.querySelector('[data-tt-extra-delete]').addEventListener('click', function() {
          wrap.remove();
          syncToTextarea();
        });
        wrap.querySelector('[data-tt-extra-up]').addEventListener('click', function() {
          var prev = wrap.previousElementSibling;
          if (prev) mount.insertBefore(wrap, prev);
          syncToTextarea();
        });
        wrap.querySelector('[data-tt-extra-down]').addEventListener('click', function() {
          var next = wrap.nextElementSibling;
          if (next) mount.insertBefore(next, wrap);
          syncToTextarea();
        });

        // Drag reorder (HTML5 DnD)
        wrap.draggable = true;
        wrap.addEventListener('dragstart', function(e){ wrap.classList.add('is-dragging'); e.dataTransfer.effectAllowed = 'move'; });
        wrap.addEventListener('dragend', function(){ wrap.classList.remove('is-dragging'); syncToTextarea(); });
        mount.addEventListener('dragover', function(e) {
          e.preventDefault();
          var dragging = mount.querySelector('.is-dragging');
          if (!dragging) return;
          var after = getDragAfterElement(mount, e.clientY);
          if (after == null) mount.appendChild(dragging);
          else mount.insertBefore(dragging, after);
        });
        function getDragAfterElement(container, y) {
          var els = [].slice.call(container.querySelectorAll('.tt-exam-extra-item:not(.is-dragging)'));
          return els.reduce(function(closest, child) {
            var box = child.getBoundingClientRect();
            var offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) return { offset: offset, element: child };
            return closest;
          }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        return wrap;
      }

      var initial = safeParse(jsonEl.value);
      initial.forEach(function(item) { mount.appendChild(createItem(item)); });
      syncToTextarea();

      addBtn.addEventListener('click', function() {
        mount.appendChild(createItem({ title: '', content: '' }));
        syncToTextarea();
      });
    })();
    </script>
    <?php
}

/**
 * Save handler for ek_exam meta.
 */
function tt_exam_save_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( 'ek_exam' !== get_post_type( $post_id ) ) return;

    if ( ! isset( $_POST['tt_exam_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tt_exam_meta_nonce'] ) ), 'tt_exam_meta_save' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $simple_fields = array(
        'exam_quiz_category'   => 'sanitize_text_field',
    );
    foreach ( $simple_fields as $key => $cb ) {
        if ( isset( $_POST[ $key ] ) ) {
            $val = call_user_func( $cb, wp_unslash( $_POST[ $key ] ) );
            update_post_meta( $post_id, $key, $val );
        }
    }

    $html_fields = array(
        'exam_about',
        'exam_current_affairs',
        'exam_syllabus',
    );
    foreach ( $html_fields as $key ) {
        if ( isset( $_POST[ $key ] ) ) {
            $val = wp_kses_post( wp_unslash( $_POST[ $key ] ) );
            update_post_meta( $post_id, $key, $val );
        }
    }

    if ( isset( $_POST['exam_accordion_extra'] ) ) {
        $raw = (string) wp_unslash( $_POST['exam_accordion_extra'] );
        // Store as JSON string; validate shape lightly.
        $decoded = json_decode( $raw, true );
        if ( is_array( $decoded ) ) {
            $clean = array();
            foreach ( $decoded as $item ) {
                if ( ! is_array( $item ) ) continue;
                $title = sanitize_text_field( $item['title'] ?? '' );
                $content = wp_kses_post( $item['content'] ?? '' );
                if ( '' === trim( $title ) && '' === trim( $content ) ) continue;
                $clean[] = array( 'title' => $title, 'content' => $content );
            }
            update_post_meta( $post_id, 'exam_accordion_extra', wp_json_encode( $clean ) );
        } else {
            update_post_meta( $post_id, 'exam_accordion_extra', '[]' );
        }
    }
}
add_action( 'save_post', 'tt_exam_save_meta' );

