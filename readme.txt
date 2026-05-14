10Tracker
=========

Contributors: 10tracker
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Premium WordPress theme for 10tracker.com — competitive exam MCQ practice platform powered by the ExamKit v2 plugin.

Description
-----------

10Tracker is a meticulously crafted WordPress theme purpose-built for edtech / exam preparation platforms.
It works hand-in-glove with the ExamKit v2 plugin and provides:

* Premium dark-navy / white design system
* Full ExamKit CSS integration (exam list, quiz list, quiz attempt, my-attempts)
* Mobile-first responsive layout with drawer nav
* Custom page templates: front-page, single exam, single quiz, my-progress
* Exam category strip, hero quiz demo card
* Stats cards, testimonials section, how-it-works steps
* Footer with 4 columns, social icons
* Customizer controls for hero heading, sub-text, CTA button
* Clean token-driven CSS (custom properties) — easy to rebrand

Installation
------------

1. Install and activate the **ExamKit v2** plugin first.
2. Upload the `10tracker-theme` folder to `/wp-content/themes/` or install via Appearance → Themes → Add New → Upload.
3. Activate the theme.
4. Go to Appearance → Menus and assign:
   - A menu to **Primary Navigation**
   - Menus to the three footer locations
5. Go to Appearance → Customize → Hero Section to set your headline and CTA.
6. Create a page with the template **My Attempts / Progress** and link it from your nav.

ExamKit Shortcodes Supported
------------------------------

[ek_exam_list]        — grid of all exams (used on homepage & archive)
[ek_quiz_list]        — table of quizzes for an exam (auto on single exam)
[ek_quiz]             — timed MCQ attempt UI (auto on single quiz)
[ek_my_attempts]      — user's attempt history table

Changelog
---------

1.0.0 — Initial release
