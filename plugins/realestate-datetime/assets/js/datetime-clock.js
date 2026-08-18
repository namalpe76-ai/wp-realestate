(function () {
    'use strict';

    function initDateTimeClock() {
        var widgets = document.querySelectorAll('.realestate-datetime-widget');

        if (!widgets.length) {
            return;
        }

        widgets.forEach(function (widget) {
            var timezone = widget.getAttribute('data-timezone') || 'Asia/Colombo';
            var dateFormat = widget.getAttribute('data-date-format') || 'full';
            var timeFormat = widget.getAttribute('data-time-format') || '12h';

            var dayEl = widget.querySelector('.datetime-day');
            var dateEl = widget.querySelector('.datetime-date');
            var timeEl = widget.querySelector('.datetime-time');

            if (!dayEl || !dateEl || !timeEl) {
                return;
            }

            function updateClock() {
                try {
                    var now = new Date();

                    var dayOptions = { weekday: 'long', timeZone: timezone };
                    var dayStr = now.toLocaleDateString('en-US', dayOptions);

                    var dateOptions = buildDateOptions(dateFormat, timezone);
                    var dateStr = now.toLocaleDateString('en-US', dateOptions);

                    var timeOptions = buildTimeOptions(timeFormat, timezone);
                    var timeStr = now.toLocaleTimeString('en-US', timeOptions);

                    dayEl.textContent = dayStr;
                    dateEl.textContent = dateStr;
                    timeEl.textContent = timeStr;
                } catch (e) {
                    widget.setAttribute('title', 'Clock error: ' + e.message);
                }
            }

            function buildDateOptions(format, tz) {
                var base = { timeZone: tz };

                switch (format) {
                    case 'short':
                        return Object.assign(base, {
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        });
                    case 'iso':
                        return Object.assign(base, {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        });
                    case 'full':
                    default:
                        return Object.assign(base, {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                }
            }

            function buildTimeOptions(format, tz) {
                var base = { timeZone: tz, hour: '2-digit', minute: '2-digit', second: '2-digit' };

                if (format === '24h') {
                    return Object.assign(base, { hour12: false });
                }

                return Object.assign(base, { hour12: true });
            }

            updateClock();
            setInterval(updateClock, 1000);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDateTimeClock);
    } else {
        initDateTimeClock();
    }
})();
