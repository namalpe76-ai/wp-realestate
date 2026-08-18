/**
 * 11AA Real Estate - Property Listing JavaScript
 * Handles gallery lightbox, view toggle, search, map, and sort
 */
(function () {
  'use strict';

  /* ---------- Image Gallery Lightbox ---------- */
  function initLightbox() {
    var triggers = document.querySelectorAll('.re-property-gallery-img');
    if (!triggers.length) return;

    var overlay = document.createElement('div');
    overlay.className = 're-lightbox-overlay';
    overlay.innerHTML =
      '<button class="re-lightbox-close" aria-label="Close">&times;</button>' +
      '<img class="re-lightbox-img" src="" alt="Property Image">' +
      '<button class="re-lightbox-prev" aria-label="Previous">&#10094;</button>' +
      '<button class="re-lightbox-next" aria-label="Next">&#10095;</button>';
    document.body.appendChild(overlay);

    var img = overlay.querySelector('.re-lightbox-img');
    var images = [];
    var currentIndex = 0;

    triggers.forEach(function (el, i) {
      images.push(el.getAttribute('data-full') || el.src);
      el.addEventListener('click', function () {
        currentIndex = i;
        showImage();
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    function showImage() {
      img.src = images[currentIndex];
    }

    overlay.querySelector('.re-lightbox-close').addEventListener('click', closeLightbox);
    overlay.querySelector('.re-lightbox-prev').addEventListener('click', function () {
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      showImage();
    });
    overlay.querySelector('.re-lightbox-next').addEventListener('click', function () {
      currentIndex = (currentIndex + 1) % images.length;
      showImage();
    });

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeLightbox();
    });

    document.addEventListener('keydown', function (e) {
      if (!overlay.classList.contains('active')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        showImage();
      }
      if (e.key === 'ArrowRight') {
        currentIndex = (currentIndex + 1) % images.length;
        showImage();
      }
    });

    function closeLightbox() {
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  /* ---------- Grid / List View Toggle ---------- */
  function initViewToggle() {
    var gridBtn = document.querySelector('.re-view-grid');
    var listBtn = document.querySelector('.re-view-list');
    var container = document.querySelector('.re-properties-grid');
    if (!gridBtn || !listBtn || !container) return;

    gridBtn.addEventListener('click', function () {
      container.classList.remove('re-list-view');
      container.classList.add('re-grid-view');
      gridBtn.classList.add('active');
      listBtn.classList.remove('active');
    });

    listBtn.addEventListener('click', function () {
      container.classList.remove('re-grid-view');
      container.classList.add('re-list-view');
      listBtn.classList.add('active');
      gridBtn.classList.remove('active');
    });
  }

  /* ---------- Property Search Form ---------- */
  function initSearchForm() {
    var form = document.querySelector('.re-property-search-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var params = new URLSearchParams();
      var fields = form.querySelectorAll('select, input');
      fields.forEach(function (field) {
        var val = field.value;
        var name = field.getAttribute('name');
        if (name && val) {
          params.set(name, val);
        }
      });
      var baseUrl = form.getAttribute('action') || window.location.pathname;
      window.location.href = baseUrl + '?' + params.toString();
    });
  }

  /* ---------- Sort Functionality ---------- */
  function initSort() {
    var sortSelect = document.querySelector('.re-sort-select');
    if (!sortSelect) return;

    sortSelect.addEventListener('change', function () {
      var params = new URLSearchParams(window.location.search);
      params.set('sort', this.value);
      window.location.search = params.toString();
    });

    var currentSort = new URLSearchParams(window.location.search).get('sort');
    if (currentSort) {
      sortSelect.value = currentSort;
    }
  }

  /* ---------- Google Map Init ---------- */
  function initMap() {
    var mapContainer = document.getElementById('property-map');
    if (!mapContainer) return;

    var lat = parseFloat(mapContainer.getAttribute('data-lat'));
    var lng = parseFloat(mapContainer.getAttribute('data-lng'));
    if (isNaN(lat) || isNaN(lng)) return;

    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
      mapContainer.innerHTML =
        '<p style="padding:20px;text-align:center;">Map loading failed. ' +
        '<a href="https://www.google.com/maps?q=' + lat + ',' + lng + '" target="_blank" rel="noopener">Open in Google Maps</a></p>';
      return;
    }

    var map = new google.maps.Map(mapContainer, {
      center: { lat: lat, lng: lng },
      zoom: 15,
      disableDefaultUI: false,
      zoomControl: true,
      scrollwheel: false,
    });

    new google.maps.Marker({ position: { lat: lat, lng: lng }, map: map });
  }

  /* ---------- Lazy Load Images ---------- */
  function initLazyLoad() {
    if ('loading' in HTMLImageElement.prototype) return; // native support
    var images = document.querySelectorAll('img[data-src]');
    if (!images.length) return;

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var el = entry.target;
            el.src = el.getAttribute('data-src');
            el.removeAttribute('data-src');
            observer.unobserve(el);
          }
        });
      }, { rootMargin: '200px' });

      images.forEach(function (img) {
        observer.observe(img);
      });
    } else {
      images.forEach(function (img) {
        img.src = img.getAttribute('data-src');
        img.removeAttribute('data-src');
      });
    }
  }

  /* ---------- Init ---------- */
  document.addEventListener('DOMContentLoaded', function () {
    initLightbox();
    initViewToggle();
    initSearchForm();
    initSort();
    initLazyLoad();
    initMap();
  });
})();
