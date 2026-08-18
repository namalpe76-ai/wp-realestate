(function ($) {
    'use strict';

    var refreshInterval = null;

    function initWeatherWidget() {
        bindRefreshButtons();
        startAutoRefresh();
    }

    function bindRefreshButtons() {
        $(document).on('click', '.weather-refresh-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            refreshWeather($btn);
        });
    }

    function refreshWeather($btn) {
        var $widget = $btn.closest('.realestate-weather-widget');

        $btn.addClass('spinning');

        $widget.html(
            '<div class="weather-loading">' +
            '<div class="weather-spinner"></div>' +
            '<p>Loading weather data...</p>' +
            '</div>'
        );

        $.post(realestateWeather.ajax_url, {
            action: 'realestate_weather_refresh',
            nonce: realestateWeather.nonce
        })
        .done(function (response) {
            if (response.success) {
                updateWeatherDisplay($widget, response.data);
            } else {
                showError($widget, response.data || 'Failed to load weather data.');
            }
        })
        .fail(function () {
            showError($widget, 'Network error. Please check your connection.');
        })
        .always(function () {
            $btn.removeClass('spinning');
        });
    }

    function updateWeatherDisplay($widget, data) {
        var unitSuffix = data.unit_suffix || 'C';
        var windUnit = data.wind_unit || 'm/s';
        var countryHtml = data.country
            ? '<span class="location-country">' + escapeHtml(data.country) + '</span>'
            : '';
        var feelsLikeHtml = data.feels_like
            ? '<div class="weather-detail-item">' +
              '<span class="detail-icon">&#128525;</span>' +
              '<span class="detail-label">Feels Like</span>' +
              '<span class="detail-value">' + data.feels_like + '&deg;' + escapeHtml(unitSuffix) + '</span>' +
              '</div>'
            : '';
        var updatedTime = data.last_updated_timestamp
            ? timeAgo(data.last_updated_timestamp)
            : 'just now';

        var html =
            '<div class="weather-card">' +
                '<div class="weather-header">' +
                    '<div class="weather-location">' +
                        '<span class="location-name">' + escapeHtml(data.location_name) + '</span>' +
                        countryHtml +
                    '</div>' +
                    '<button class="weather-refresh-btn" data-action="refresh" title="Refresh weather">&#8635;</button>' +
                '</div>' +
                '<div class="weather-main">' +
                    '<div class="weather-icon-temp">' +
                        '<img class="weather-icon" src="' + escapeHtml(data.icon_url) + '" alt="' + escapeHtml(data.condition) + '" width="80" height="80" />' +
                        '<div class="weather-temperature">' +
                            '<span class="temp-value">' + data.temperature + '</span>' +
                            '<span class="temp-unit">&deg;' + escapeHtml(unitSuffix) + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="weather-condition">' + escapeHtml(data.condition) + '</div>' +
                '</div>' +
                '<div class="weather-details">' +
                    '<div class="weather-detail-item">' +
                        '<span class="detail-icon">&#128167;</span>' +
                        '<span class="detail-label">Humidity</span>' +
                        '<span class="detail-value">' + data.humidity + '%</span>' +
                    '</div>' +
                    '<div class="weather-detail-item">' +
                        '<span class="detail-icon">&#127788;</span>' +
                        '<span class="detail-label">Wind</span>' +
                        '<span class="detail-value">' + data.wind_speed + ' ' + escapeHtml(windUnit) + '</span>' +
                    '</div>' +
                    feelsLikeHtml +
                '</div>' +
                '<div class="weather-footer">' +
                    '<span class="weather-updated">Updated: ' + escapeHtml(updatedTime) + '</span>' +
                '</div>' +
            '</div>';

        $widget.html(html);
    }

    function showError($widget, message) {
        var html =
            '<div class="weather-error-state">' +
                '<div class="weather-error-icon">&#9888;</div>' +
                '<p class="weather-error-message">' + escapeHtml(message) + '</p>' +
                '<button class="weather-refresh-btn" data-action="refresh">&#8635; Retry</button>' +
            '</div>';

        $widget.html(html);
    }

    function startAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        refreshInterval = setInterval(function () {
            var $widget = $('.realestate-weather-widget');
            if ($widget.length) {
                var $btn = $widget.find('.weather-refresh-btn');
                if ($btn.length && !$btn.hasClass('spinning')) {
                    refreshWeather($btn.first());
                }
            }
        }, 30 * 60 * 1000);
    }

    function timeAgo(timestamp) {
        var now = Math.floor(Date.now() / 1000);
        var diff = now - timestamp;

        if (diff < 60) {
            return 'just now';
        }
        if (diff < 3600) {
            var mins = Math.floor(diff / 60);
            return mins + ' min' + (mins > 1 ? 's' : '') + ' ago';
        }
        if (diff < 86400) {
            var hours = Math.floor(diff / 3600);
            return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
        }
        var days = Math.floor(diff / 86400);
        return days + ' day' + (days > 1 ? 's' : '') + ' ago';
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    $(document).ready(initWeatherWidget);

})(jQuery);
