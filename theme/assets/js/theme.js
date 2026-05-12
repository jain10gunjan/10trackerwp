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

    function openNav() {
        $nav.addClass('open').attr('aria-hidden', 'false');
        $toggle.attr('aria-expanded', 'true');
        $('body').css('overflow', 'hidden');
    }

    function closeNav() {
        $nav.removeClass('open').attr('aria-hidden', 'true');
        $toggle.attr('aria-expanded', 'false');
        $('body').css('overflow', '');
    }

    $toggle.on('click', openNav);
    $close.on('click', closeNav);
    $overlay.on('click', closeNav);

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

})(jQuery);
