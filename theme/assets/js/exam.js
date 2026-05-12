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

  document.addEventListener('DOMContentLoaded', initHorizontalAccordions);
})();
