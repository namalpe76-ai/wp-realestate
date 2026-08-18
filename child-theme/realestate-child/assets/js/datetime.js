/**
 * 11AA Real Estate — Live Date & Time (Asia/Colombo)
 */
(function () {
  'use strict';

  var container = document.getElementById('realestate-datetime');
  if (!container) return;

  var tz = container.getAttribute('data-timezone') || 'Asia/Colombo';
  var use24 = container.getAttribute('data-24h') === 'true';

  var dateOpts = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    timeZone: tz,
  };

  var timeOpts = {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: !use24,
    timeZone: tz,
  };

  var dateFormatter = null;
  var timeFormatter = null;

  try {
    dateFormatter = new Intl.DateTimeFormat('en-US', dateOpts);
    timeFormatter = new Intl.DateTimeFormat('en-US', timeOpts);
  } catch (e) {
    container.innerHTML = '<span class="re-dt-fallback">' + new Date().toLocaleString() + '</span>';
    return;
  }

  function render() {
    var now = new Date();
    var dateStr = dateFormatter.format(now);
    var timeStr = timeFormatter.format(now);
    container.innerHTML =
      '<div class="re-dt-date">' + dateStr + '</div>' +
      '<div class="re-dt-time">' + timeStr + '</div>';
  }

  render();
  setInterval(render, 1000);
})();
