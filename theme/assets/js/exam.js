/* global ttExam */
(function () {
  'use strict';

  var cfg = window.ttExam || {};
  var examSlug = (cfg.examSlug || '').toString() || 'exam';

  function escHtml(value) {
    var node = document.createElement('div');
    node.textContent = value == null ? '' : String(value);
    return node.innerHTML;
  }

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

  function initRestQuizBrowsers() {
    var browsers = Array.prototype.slice.call(document.querySelectorAll('[data-tt-quiz-rest-browser]'));
    if (!browsers.length) return;

    browsers.forEach(function (browser) {
      var endpoint = browser.getAttribute('data-endpoint') || cfg.quizEndpoint || '';
      var perPage = parseInt(browser.getAttribute('data-per-page') || '20', 10);
      var skeleton = browser.querySelector('[data-tt-quiz-skeleton]');
      var content = browser.querySelector('[data-tt-quiz-content]');
      var error = browser.querySelector('[data-tt-rest-error]');
      var grid = browser.querySelector('[data-tt-rest-grid]');
      var empty = browser.querySelector('[data-tt-rest-empty]');
      var pagination = browser.querySelector('[data-tt-rest-pagination]');
      var search = browser.querySelector('[data-tt-rest-search]');
      var difficulty = browser.querySelector('[data-tt-rest-difficulty]');
      var difficultyWrap = browser.querySelector('[data-tt-rest-difficulty-wrap]');
      var topic = browser.querySelector('[data-tt-rest-topic]');
      var topicWrap = browser.querySelector('[data-tt-rest-topic-wrap]');
      var reset = browser.querySelector('[data-tt-rest-reset]');
      var resultCount = browser.querySelector('[data-tt-rest-result-count]');
      var resultQuestions = browser.querySelector('[data-tt-rest-result-questions]');
      var totalQuizzes = browser.querySelector('[data-tt-rest-quiz-total]');
      var totalQuestions = browser.querySelector('[data-tt-rest-question-total]');
      var practiceSummary = document.getElementById('tt-practice-summary');
      var heroQuizCount = document.getElementById('tt-exam-quiz-count');
      var heroQuestionCount = document.getElementById('tt-exam-question-count');
      var heroDiffMix = document.getElementById('tt-exam-diff-mix');
      var items = [];
      var currentPage = 1;

      function setLoading(isLoading) {
        if (skeleton) {
          skeleton.hidden = !isLoading;
          skeleton.setAttribute('aria-busy', isLoading ? 'true' : 'false');
        }
        if (content) content.hidden = isLoading;
        if (error) error.hidden = true;
      }

      function setError() {
        if (skeleton) skeleton.hidden = true;
        if (content) content.hidden = true;
        if (error) error.hidden = false;
        if (practiceSummary) practiceSummary.textContent = 'Unable to load quizzes';
        if (heroDiffMix) heroDiffMix.textContent = 'Unavailable';
      }

      function formatCount(count, singular, plural) {
        return count === 1 ? ('1 ' + singular) : (String(count) + ' ' + plural);
      }

      function fillSelect(select, wrap, options) {
        if (!select || !wrap) return;
        var keys = Object.keys(options || {});
        if (!keys.length) {
          wrap.hidden = true;
          return;
        }
        keys.forEach(function (key) {
          var option = document.createElement('option');
          option.value = key;
          option.textContent = options[key];
          select.appendChild(option);
        });
        wrap.hidden = false;
      }

      function matches(item) {
        var query = search ? (search.value || '').trim().toLowerCase() : '';
        var diff = difficulty ? (difficulty.value || '').trim().toLowerCase() : '';
        var selectedTopic = topic ? (topic.value || '').trim().toLowerCase() : '';
        if (query && String(item.search || '').indexOf(query) === -1) return false;
        if (diff && String(item.difficulty || '').toLowerCase() !== diff) return false;
        if (selectedTopic && String(item.topicKey || '').toLowerCase() !== selectedTopic) return false;
        return true;
      }

      function renderCard(item, number) {
        var diff = item.difficulty || 'Mixed';
        var diffKey = item.difficultyKey || 'na';
        var topicText = item.topic || 'General';
        var questions = item.questions ? formatCount(item.questions, 'question', 'questions') : 'Questions soon';
        var duration = item.duration || 'Flexible';

        return ''
          + '<article class="tt-rest-quiz-card">'
          + '  <div class="tt-rest-quiz-card__top">'
          + '    <span class="tt-rest-quiz-card__num">' + escHtml(number) + '</span>'
          + '    <span class="tt-diff tt-diff--' + escHtml(diffKey) + '">' + escHtml(diff) + '</span>'
          + '  </div>'
          + '  <h3 class="tt-rest-quiz-card__title"><a href="' + escHtml(item.url || '#') + '">' + escHtml(item.title || 'Quiz') + '</a></h3>'
          + (item.excerpt ? '<p class="tt-rest-quiz-card__desc">' + escHtml(item.excerpt) + '</p>' : '')
          + '  <div class="tt-rest-quiz-card__meta">'
          + '    <span>' + escHtml(questions) + '</span>'
          + '    <span>' + escHtml(topicText) + '</span>'
          + '    <span>' + escHtml(duration) + '</span>'
          + '  </div>'
          + '  <a class="tt-rest-quiz-card__cta" href="' + escHtml(item.url || '#') + '">Start practice</a>'
          + '</article>';
      }

      function renderPager(totalPages) {
        if (!pagination) return;
        pagination.innerHTML = '';
        if (totalPages <= 1) {
          pagination.hidden = true;
          return;
        }

        pagination.hidden = false;
        ['Prev', 'Next'].forEach(function () {});

        function addButton(label, page, disabled, current) {
          var button = document.createElement('button');
          button.type = 'button';
          button.className = 'tt-quiz-page-btn';
          button.textContent = label;
          button.disabled = !!disabled;
          if (current) button.setAttribute('aria-current', 'page');
          button.addEventListener('click', function () {
            currentPage = page;
            render();
          });
          pagination.appendChild(button);
        }

        addButton('Prev', Math.max(1, currentPage - 1), currentPage === 1, false);
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
          addButton(String(i), i, false, i === currentPage);
        }
        addButton('Next', Math.min(totalPages, currentPage + 1), currentPage === totalPages, false);
      }

      function updateGlobalStats(data) {
        var stats = data.stats || {};
        var filters = data.filters || {};
        var quizCount = Number(stats.quizCount || items.length) || 0;
        var questionCount = Number(stats.questionCount || 0) || 0;
        var diffLabels = Object.keys(filters.difficulty || {}).map(function (key) {
          return filters.difficulty[key];
        });

        if (totalQuizzes) totalQuizzes.textContent = String(quizCount);
        if (totalQuestions) totalQuestions.textContent = String(questionCount);
        if (heroQuizCount) heroQuizCount.textContent = String(quizCount);
        if (heroQuestionCount) heroQuestionCount.textContent = String(questionCount);
        if (heroDiffMix) heroDiffMix.textContent = diffLabels.length ? diffLabels.slice(0, 3).join(', ') : 'Mixed';
        if (practiceSummary) practiceSummary.textContent = quizCount + ' quizzes - ' + questionCount + ' questions';
      }

      function render() {
        var filtered = items.filter(matches);
        var filteredQuestions = filtered.reduce(function (sum, item) {
          return sum + (Number(item.questions || 0) || 0);
        }, 0);
        var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * perPage;
        var pageItems = filtered.slice(start, start + perPage);

        if (grid) {
          grid.innerHTML = pageItems.map(function (item, index) {
            return renderCard(item, start + index + 1);
          }).join('');
        }

        if (resultCount) resultCount.textContent = formatCount(filtered.length, 'quiz', 'quizzes');
        if (resultQuestions) resultQuestions.textContent = formatCount(filteredQuestions, 'question', 'questions');
        if (empty) empty.hidden = filtered.length > 0;
        renderPager(totalPages);
      }

      function bindControls() {
        [search, difficulty, topic].forEach(function (control) {
          if (!control) return;
          control.addEventListener(control === search ? 'input' : 'change', function () {
            currentPage = 1;
            render();
          });
        });
        if (reset) {
          reset.addEventListener('click', function () {
            if (search) search.value = '';
            if (difficulty) difficulty.value = '';
            if (topic) topic.value = '';
            currentPage = 1;
            render();
          });
        }
      }

      if (!endpoint) {
        setError();
        return;
      }

      setLoading(true);
      fetch(endpoint, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json',
          'X-WP-Nonce': (cfg.nonce || '').toString()
        }
      })
        .then(function (response) {
          if (!response.ok) throw new Error('HTTP ' + response.status);
          return response.json();
        })
        .then(function (data) {
          items = Array.isArray(data.items) ? data.items : [];
          fillSelect(difficulty, difficultyWrap, (data.filters || {}).difficulty || {});
          fillSelect(topic, topicWrap, (data.filters || {}).topic || {});
          updateGlobalStats(data);
          bindControls();
          setLoading(false);
          render();
        })
        .catch(setError);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initHorizontalAccordions();
    initQuizBrowsers();
    initRestQuizBrowsers();
  });
})();
