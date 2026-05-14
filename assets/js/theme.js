/**
 * 10Tracker Theme — theme.js
 * Mobile nav drawer, header scroll-shrink, quiz table responsive labels,
 * quiz list live search highlight
 */
(function ($) {
    'use strict';

    /* ── Mobile nav drawer ─────────────────────────────── */
    var $toggle   = $('#tt-menu-toggle');
    var $close    = $('#tt-mobile-close');
    var $nav      = $('#tt-mobile-nav');
    var $overlay  = $('#tt-mobile-overlay');

    function openNav(e) {
        if (e) e.preventDefault();
        $nav.addClass('open is-open').attr('aria-hidden', 'false');
        $toggle.attr('aria-expanded', 'true');
        $('body').addClass('tt-nav-open').css('overflow', 'hidden');
    }

    function closeNav(e) {
        if (e) e.preventDefault();
        $nav.removeClass('open is-open').attr('aria-hidden', 'true');
        $toggle.attr('aria-expanded', 'false');
        $('body').removeClass('tt-nav-open').css('overflow', '');
    }

    $toggle.on('click', function (e) {
        if ($nav.hasClass('open') || $nav.hasClass('is-open')) closeNav(e);
        else openNav(e);
    });
    $close.on('click', closeNav);
    $overlay.on('click', closeNav);
    $nav.find('a').on('click', closeNav);

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeNav();
    });

    /* ── Category strip active state ───────────────────── */
    var $chips = $('.tt-cat-chip');
    $chips.on('click', function () {
        $chips.removeClass('active');
        $(this).addClass('active');
    });

    /* ── Quiz table: inject data-label attrs for mobile ── */
    $('.ek-quiz-table').each(function () {
        var $table  = $(this);
        var headers = [];
        $table.find('thead th').each(function () {
            headers.push($(this).text().trim());
        });
        $table.find('tbody tr').each(function () {
            $(this).find('td').each(function (i) {
                if (headers[i]) {
                    $(this).attr('data-label', headers[i]);
                }
            });
        });
    });

    /* ── Quiz row list: client-side title highlight on search ── */
    var $searchInput = $('.tt-quiz-search-bar input[type="search"], .tt-quiz-search-bar input[type="text"]');
    var $quizRows    = $('.tt-quiz-row');

    // Restore original titles so we can re-highlight cleanly
    $quizRows.each(function () {
        var $title = $(this).find('.tt-quiz-row__title');
        $title.data('original', $title.text());
    });

    $searchInput.on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        $quizRows.each(function () {
            var $row   = $(this);
            var $title = $row.find('.tt-quiz-row__title');
            var orig   = $title.data('original') || $title.text();

            if (!q) {
                $title.html(orig);
                $row.show();
                return;
            }

            var lc = orig.toLowerCase();
            if (lc.indexOf(q) === -1) {
                $row.hide();
            } else {
                // Highlight match
                var idx    = lc.indexOf(q);
                var before = orig.slice(0, idx);
                var match  = orig.slice(idx, idx + q.length);
                var after  = orig.slice(idx + q.length);
                $title.html(
                    before +
                    '<mark style="background:rgba(37,99,235,.15);color:var(--blue);border-radius:2px;padding:0 2px;">' +
                    match + '</mark>' + after
                );
                $row.show();
            }
        });
    });

    /* ── Smooth scroll for in-page anchors ─────────────── */
    $('a[href^="#"]').on('click', function (e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            var navH = parseInt(getComputedStyle(document.documentElement)
                            .getPropertyValue('--nav-h')) || 64;
            $('html, body').animate({
                scrollTop: target.offset().top - navH - 12
            }, 380);
        }
    });

    /* ── Hero demo option highlight ─────────────────────── */
    $('.tt-hero__opt:not(.tt-hero__opt--correct)').on('click', function () {
        var $parent = $(this).closest('.tt-hero__card-demo-opts');
        $parent.find('.tt-hero__opt').not('.tt-hero__opt--correct').removeClass('active');
        $(this).addClass('active').css({
            borderColor: 'rgba(220,38,38,.5)',
            background: 'rgba(220,38,38,.08)',
            color: '#fca5a5'
        });
    });

    /* ── Custom registration form ───────────────────────── */
    var $registerForm = $('[data-tt-register-form]');
    if ($registerForm.length) {
        var $notice = $registerForm.find('[data-tt-register-notice]');
        var $submit = $registerForm.find('[data-tt-register-submit]');

        function setRegisterNotice(message, type) {
            if (!message) {
                $notice.prop('hidden', true).removeClass('is-error is-success').text('');
                return;
            }
            $notice.prop('hidden', false)
                .removeClass('is-error is-success')
                .addClass(type === 'success' ? 'is-success' : 'is-error')
                .text(message);
        }

        function clearRegisterErrors() {
            $registerForm.find('.tt-register-field').removeClass('has-error');
            $registerForm.find('[data-error-for]').text('');
            setRegisterNotice('', '');
        }

        function showRegisterErrors(errors) {
            Object.keys(errors || {}).forEach(function (field) {
                var $error = $registerForm.find('[data-error-for="' + field + '"]');
                $error.text(errors[field] || '');
                $error.closest('.tt-register-field').addClass('has-error');
            });
        }

        $registerForm.on('submit', function (e) {
            e.preventDefault();
            clearRegisterErrors();

            var formData = new FormData($registerForm[0]);
            formData.append('action', 'tt_register_user');
            formData.append('nonce', (window.ttData && ttData.nonce) ? ttData.nonce : '');

            $submit.prop('disabled', true).text('Creating account...');

            fetch((window.ttData && ttData.ajax_url) ? ttData.ajax_url : '/wp-admin/admin-ajax.php', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (response) {
                    return response.json().catch(function () {
                        throw new Error('Invalid server response');
                    });
                })
                .then(function (json) {
                    if (!json || !json.success) {
                        var data = (json && json.data) || {};
                        showRegisterErrors(data.errors || {});
                        setRegisterNotice(data.message || 'Registration failed. Please try again.', 'error');
                        return;
                    }

                    setRegisterNotice(json.data.message || 'Account created successfully.', 'success');
                    window.setTimeout(function () {
                        window.location.href = json.data.redirect || ((window.ttData && ttData.dashboard_url) ? ttData.dashboard_url : '/dashboard/');
                    }, 600);
                })
                .catch(function () {
                    setRegisterNotice('Registration failed. Please try again.', 'error');
                })
                .finally(function () {
                    $submit.prop('disabled', false).text('Create Account');
                });
        });
    }

})(jQuery);
