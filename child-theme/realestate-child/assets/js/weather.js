/**
 * 11AA Real Estate — Weather Widget (client-side fallback)
 * Primary weather loading is handled by the realestate-weather plugin.
 * This file provides a lightweight fallback if the plugin is not active.
 */
(function () {
  'use strict';

  var container = document.getElementById('realestate-weather');
  if (!container) return;

  if (container.querySelector('.re-weather-card')) return;

  container.innerHTML =
    '<div class="re-weather-loading">' +
      '<div class="re-weather-spinner"></div>' +
      '<p>Loading weather data…</p>' +
    '</div>';
})();
