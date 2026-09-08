// About Us page interactions — ported from the source js/site.js.
// Covers the FAQ accordion (exclusive open/close, +/- icon) and the Calendly
// demo popup used by the hero and Connect call-to-action buttons.
(function () {
  'use strict';

  // --- FAQ accordion --------------------------------------------------------
  function accIcon(btn, open) {
    var spans = btn.querySelectorAll('span');
    for (var i = spans.length - 1; i >= 0; i--) {
      var t = spans[i].textContent.trim();
      if (t === '+' || t === '−') {
        spans[i].textContent = open ? '−' : '+';
        return;
      }
    }
  }

  document.querySelectorAll('[data-cs-acc-btn]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var group = btn.getAttribute('data-cs-acc-btn');
      var panel = btn.parentElement.querySelector('[data-cs-acc-panel]');
      if (!panel) return;
      var opening = panel.hidden;
      document.querySelectorAll('[data-cs-acc-btn="' + group + '"]').forEach(function (other) {
        if (other === btn) return;
        var p = other.parentElement.querySelector('[data-cs-acc-panel]');
        if (p) p.hidden = true;
        other.setAttribute('aria-expanded', 'false');
        accIcon(other, false);
      });
      panel.hidden = !opening;
      btn.setAttribute('aria-expanded', String(opening));
      accIcon(btn, opening);
    });
  });

  // --- Calendly popup (Schedule a Demo) -------------------------------------
  var CALENDLY_URL = 'https://calendly.com/competiscan/competiscan-demo';
  var assetsReady = false;

  function loadCalendlyAssets(cb) {
    if (assetsReady) { cb(); return; }
    var css = document.createElement('link');
    css.rel = 'stylesheet';
    css.href = 'https://assets.calendly.com/assets/external/widget.css';
    document.head.appendChild(css);
    var js = document.createElement('script');
    js.src = 'https://assets.calendly.com/assets/external/widget.js';
    js.async = true;
    js.onload = function () { assetsReady = true; cb(); };
    document.body.appendChild(js);
  }

  function openCalendly() {
    loadCalendlyAssets(function () {
      if (window.Calendly) {
        window.Calendly.initPopupWidget({ url: CALENDLY_URL });
      }
    });
  }

  document.querySelectorAll('.cs-about [data-cs-calendly], .cs-about .cs-contact-btn').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      openCalendly();
    });
  });
})();
