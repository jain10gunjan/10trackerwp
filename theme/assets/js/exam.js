/* global ttExam */
(function () {
  'use strict';

  var cfg = window.ttExam || {};
  var examSlug = (cfg.examSlug || '').toString() || 'exam';

  function initHorizontalAccordions() {
    var tabs = Array.prototype.slice.call(document.querySelectorAll('.tt-accordion-tabs .tt-accordion__header'));
    var panels = Array.prototype.slice.call(document.querySelectorAll('.tt-accordion-panels .tt-accordion'));
    if (!tabs.length || !panels.length) return;

    var storageKey = 'ttExamAccordion:' + examSlug;

    function getPanel(tab) {
      var id = tab.getAttribute('aria-controls');
      return id ? document.getElementById(id) : null;
    }

    function activate(tab, persist) {
      var panel = getPanel(tab);
      if (!panel) return;

      tabs.forEach(function (item) {
        var itemPanel = getPanel(item);
        var active = item === tab;

        item.setAttribute('aria-selected', active ? 'true' : 'false');
        item.setAttribute('aria-expanded', active ? 'true' : 'false');
        item.classList.toggle('is-active', active);

        if (itemPanel) {
          itemPanel.hidden = !active;
          itemPanel.classList.toggle('is-open', active);
        }
      });

      if (persist !== false) {
        try {
          localStorage.setItem(storageKey, panel.getAttribute('id') || '');
        } catch (e) {}
      }
    }

    var initial = tabs[0];
    var persisted = '';
    var hash = window.location.hash ? window.location.hash.replace('#', '') : '';

    try {
      persisted = localStorage.getItem(storageKey) || '';
    } catch (e) {}

    if (hash) {
      tabs.some(function (tab) {
        if (tab.getAttribute('aria-controls') === hash) {
          initial = tab;
          return true;
        }
        return false;
      });
    } else if (persisted) {
      tabs.some(function (tab) {
        if (tab.getAttribute('aria-controls') === persisted) {
          initial = tab;
          return true;
        }
        return false;
      });
    }

    activate(initial, false);

    tabs.forEach(function (tab, index) {
      tab.addEventListener('click', function () {
        activate(tab, true);
      });

      tab.addEventListener('keydown', function (event) {
        var key = event.key || event.code;
        var nextIndex = index;

        if (key === 'ArrowRight' || key === 'ArrowDown') {
          nextIndex = (index + 1) % tabs.length;
        } else if (key === 'ArrowLeft' || key === 'ArrowUp') {
          nextIndex = (index - 1 + tabs.length) % tabs.length;
        } else if (key === 'Home') {
          nextIndex = 0;
        } else if (key === 'End') {
          nextIndex = tabs.length - 1;
        } else if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
          event.preventDefault();
          activate(tab, true);
          return;
        } else {
          return;
        }

        event.preventDefault();
        tabs[nextIndex].focus();
        activate(tabs[nextIndex], true);
      });
    });

    document.addEventListener('click', function (event) {
      var link = event.target.closest ? event.target.closest('a[href^="#accordion-"]') : null;
      if (!link) return;

      var targetId = (link.getAttribute('href') || '').replace('#', '');
      tabs.some(function (tab) {
        if (tab.getAttribute('aria-controls') === targetId) {
          activate(tab, true);
          return true;
        }
        return false;
      });
    });
  }

  function initQuizBrowsers() {
    var browsers = Array.prototype.slice.call(document.querySelectorAll('[data-tt-quiz-browser]'));
    if (!browsers.length) return;

    browsers.forEach(function (browser) {
      var rows = Array.prototype.slice.call(browser.querySelectorAll('[data-tt-quiz-row]'));
      var search = browser.querySelector('[data-tt-quiz-search]');
      var difficulty = browser.querySelector('[data-tt-quiz-difficulty]');
      var topic = browser.querySelector('[data-tt-quiz-topic]');
      var reset = browser.querySelector('[data-tt-quiz-reset]');
      var count = browser.querySelector('[data-tt-quiz-count]');
      var questionCount = browser.querySelector('[data-tt-quiz-question-count]');
      var empty = browser.querySelector('[data-tt-quiz-empty]');
      var pagination = browser.querySelector('[data-tt-quiz-pagination]');
      var perPage = parseInt(browser.getAttribute('data-per-page') || '20', 10);
      var currentPage = 1;

      if (!rows.length) return;

      function matches(row) {
        var query = search ? (search.value || '').trim().toLowerCase() : '';
        var diff = difficulty ? (difficulty.value || '').trim().toLowerCase() : '';
        var selectedTopic = topic ? (topic.value || '').trim().toLowerCase() : '';
        var rowSearch = row.getAttribute('data-search') || '';
        var rowDiff = row.getAttribute('data-difficulty') || '';
        var rowTopic = row.getAttribute('data-topic') || '';

        if (query && rowSearch.indexOf(query) === -1) return false;
        if (diff && rowDiff !== diff) return false;
        if (selectedTopic && rowTopic !== selectedTopic) return false;
        return true;
      }

      function renderPager(totalPages) {
        if (!pagination) return;
        pagination.innerHTML = '';

        if (totalPages <= 1) {
          pagination.hidden = true;
          return;
        }

        pagination.hidden = false;

        var prev = document.createElement('button');
        prev.type = 'button';
        prev.className = 'tt-quiz-page-btn';
        prev.textContent = 'Prev';
        prev.disabled = currentPage === 1;
        prev.addEventListener('click', function () {
          currentPage = Math.max(1, currentPage - 1);
          render();
        });
        pagination.appendChild(prev);

        for (var i = 1; i <= totalPages; i++) {
          if (totalPages > 7 && i !== 1 && i !== totalPages && Math.abs(i - currentPage) > 1) {
            if (i === 2 || i === totalPages - 1) {
              var dots = document.createElement('span');
              dots.className = 'tt-quiz-page-dots';
              dots.textContent = '...';
              pagination.appendChild(dots);
            }
            continue;
          }

          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'tt-quiz-page-btn';
          btn.textContent = String(i);
          btn.setAttribute('aria-current', i === currentPage ? 'page' : 'false');
          btn.addEventListener('click', (function (page) {
            return function () {
              currentPage = page;
              render();
            };
          })(i));
          pagination.appendChild(btn);
        }

        var next = document.createElement('button');
        next.type = 'button';
        next.className = 'tt-quiz-page-btn';
        next.textContent = 'Next';
        next.disabled = currentPage === totalPages;
        next.addEventListener('click', function () {
          currentPage = Math.min(totalPages, currentPage + 1);
          render();
        });
        pagination.appendChild(next);
      }

      function render() {
        var filtered = rows.filter(matches);
        var filteredQuestions = filtered.reduce(function (sum, row) {
          return sum + (parseInt(row.getAttribute('data-questions') || '0', 10) || 0);
        }, 0);
        var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));

        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * perPage;
        var end = start + perPage;

        rows.forEach(function (row) {
          row.hidden = true;
        });

        filtered.slice(start, end).forEach(function (row, index) {
          var indexEl = row.querySelector('.tt-quiz-index');
          row.hidden = false;
          if (indexEl) indexEl.textContent = String(start + index + 1);
        });

        if (count) {
          count.textContent = filtered.length === 1 ? '1 quiz' : (filtered.length + ' quizzes');
        }

        if (questionCount) {
          questionCount.textContent = filteredQuestions === 1 ? '1 question' : (filteredQuestions + ' questions');
        }

        if (empty) {
          empty.hidden = filtered.length > 0;
        }

        renderPager(totalPages);
      }

      function resetFilters() {
        if (search) search.value = '';
        if (difficulty) difficulty.value = '';
        if (topic) topic.value = '';
        currentPage = 1;
        render();
      }

      if (search) {
        search.addEventListener('input', function () {
          currentPage = 1;
          render();
        });
      }
      if (difficulty) {
        difficulty.addEventListener('change', function () {
          currentPage = 1;
          render();
        });
      }
      if (topic) {
        topic.addEventListener('change', function () {
          currentPage = 1;
          render();
        });
      }
      if (reset) {
        reset.addEventListener('click', resetFilters);
      }

      render();
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initHorizontalAccordions();
    initQuizBrowsers();
  });
})();
