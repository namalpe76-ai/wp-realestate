/**
 * 11AA Real Estate — Analytics Counter Animation
 * Animates stat numbers when they scroll into view.
 */
(function () {
  'use strict';

  var counters = document.querySelectorAll('.re-stat-number[data-count]');
  if (!counters.length) return;

  function animateCounter(el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target)) return;

    var duration = 2000;
    var start = performance.now();

    function step(now) {
      var progress = Math.min((now - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 4);
      el.textContent = Math.floor(eased * target).toLocaleString() + '+';
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = target.toLocaleString() + '+';
      }
    }

    requestAnimationFrame(step);
  }

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    counters.forEach(function (c) { observer.observe(c); });
  } else {
    counters.forEach(animateCounter);
  }
})();
