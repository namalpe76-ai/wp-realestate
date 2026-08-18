(function () {
    'use strict';

    function animateCounter(element) {
        var target = parseInt(element.getAttribute('data-target'), 10);
        if (isNaN(target) || target <= 0) {
            element.textContent = '0+';
            return;
        }

        var duration = 2000;
        var startTime = null;
        var startValue = 0;

        function easeOutQuart(t) {
            return 1 - Math.pow(1 - t, 4);
        }

        function update(timestamp) {
            if (!startTime) {
                startTime = timestamp;
            }

            var elapsed = timestamp - startTime;
            var progress = Math.min(elapsed / duration, 1);
            var easedProgress = easeOutQuart(progress);
            var currentValue = Math.floor(startValue + (target - startValue) * easedProgress);

            element.textContent = currentValue.toLocaleString() + '+';

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = target.toLocaleString() + '+';
            }
        }

        requestAnimationFrame(update);
    }

    function initCounters() {
        var counters = document.querySelectorAll('.analytics-number[data-target]');
        if (!counters.length) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            counters.forEach(function (el) {
                animateCounter(el);
            });
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.3,
                rootMargin: '0px 0px -50px 0px',
            }
        );

        counters.forEach(function (el) {
            observer.observe(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCounters);
    } else {
        initCounters();
    }
})();
