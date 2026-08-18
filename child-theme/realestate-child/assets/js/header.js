/**
 * 11AA Real Estate — Sticky Header & Mobile Menu
 */
(function () {
  'use strict';

  var header = document.querySelector('.re-site-header');
  if (!header) return;

  var scrollThreshold = 50;

  function onScroll() {
    if (window.scrollY > scrollThreshold) {
      header.classList.add('re-header-sticky', 're-header-scrolled');
    } else {
      header.classList.remove('re-header-sticky', 're-header-scrolled');
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  var toggle = document.querySelector('.re-mobile-toggle');
  var nav = document.querySelector('.re-primary-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('re-nav-open');
      toggle.classList.toggle('active', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', function (e) {
      if (!header.contains(e.target) && nav.classList.contains('re-nav-open')) {
        nav.classList.remove('re-nav-open');
        toggle.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
})();
