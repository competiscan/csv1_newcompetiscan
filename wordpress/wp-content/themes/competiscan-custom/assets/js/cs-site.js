// Shared interactions for the rebuilt inner pages — ported from the source
// js/site.js: FAQ accordion, gated white-paper download form, and the Calendly
// demo popup. Scoped to the .cs-x1 page wrapper so it never touches the theme
// header/footer behaviour.
(function () {
  'use strict';

  var root = document.querySelector('.cs-x1');
  if (!root) return;

  // --- FAQ accordion (exclusive, +/- icon) ---------------------------------
  function accIcon(btn, open) {
    var spans = btn.querySelectorAll('span');
    for (var i = spans.length - 1; i >= 0; i--) {
      var t = spans[i].textContent.trim();
      if (t === '+' || t === '−') { spans[i].textContent = open ? '−' : '+'; return; }
    }
  }
  root.querySelectorAll('[data-cs-acc-btn]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var group = btn.getAttribute('data-cs-acc-btn');
      var panel = btn.parentElement.querySelector('[data-cs-acc-panel]');
      if (!panel) return;
      var opening = panel.hidden;
      root.querySelectorAll('[data-cs-acc-btn="' + group + '"]').forEach(function (other) {
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

  // --- Gated white-paper form (starts the PDF download on submit) ------------
  root.querySelectorAll('[data-cs-whitepaper]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) { form.reportValidity(); return; }
      var link = document.createElement('a');
      link.href = form.getAttribute('data-cs-wpfile');
      link.download = '';
      document.body.appendChild(link);
      link.click();
      link.remove();
      form.reset();
    });
  });

  // --- Mockup forms (no backend) -------------------------------------------
  root.querySelectorAll('[data-cs-form]').forEach(function (f) {
    f.addEventListener('submit', function (e) { e.preventDefault(); });
  });

  // --- Circular carousel (deliverables) -------------------------------------
  // Root [data-cs-crsl] with [data-cs-crsl-slide] slides stacked in one grid
  // cell (crossfade + slide). Controls (prev/next/dots) live in the same section.
  root.querySelectorAll('[data-cs-crsl]').forEach(function (crsl) {
    var slides = Array.prototype.slice.call(crsl.querySelectorAll('[data-cs-crsl-slide]'));
    if (slides.length < 2) return;
    var scope = crsl.closest('section') || root;
    var dots = Array.prototype.slice.call(scope.querySelectorAll('[data-cs-crsl-dot]'));
    var index = 0;
    function show(n) {
      index = ((n % slides.length) + slides.length) % slides.length;
      slides.forEach(function (s, i) {
        var active = i === index;
        s.style.opacity = active ? '1' : '0';
        s.style.transform = active ? 'translateX(0)' : 'translateX(24px)';
        s.style.pointerEvents = active ? 'auto' : 'none';
        s.setAttribute('aria-hidden', String(!active));
      });
      dots.forEach(function (d, i) {
        var active = i === index;
        d.style.background = active ? 'rgb(0,171,171)' : 'transparent';
        d.style.borderColor = active ? 'rgb(0,171,171)' : 'rgba(0,75,129,0.35)';
      });
    }
    scope.querySelectorAll('[data-cs-crsl-prev]').forEach(function (b) { b.addEventListener('click', function () { show(index - 1); }); });
    scope.querySelectorAll('[data-cs-crsl-next]').forEach(function (b) { b.addEventListener('click', function () { show(index + 1); }); });
    dots.forEach(function (d, i) { d.addEventListener('click', function () { show(i); }); });
    show(0);
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
      if (window.Calendly) { window.Calendly.initPopupWidget({ url: CALENDLY_URL }); }
    });
  }
  root.querySelectorAll('[data-cs-calendly]').forEach(function (el) {
    el.addEventListener('click', function (e) { e.preventDefault(); openCalendly(); });
  });

  // --- White-paper CF7: per-page submit label + open the PDF on success ------
  // The button label is set from the wrapper's data-cs-btn (so the same form can
  // read "Download..." or "Access..." per page). On a SUCCESSFUL submission
  // (wpcf7mailsent fires only after validation passes and the email is sent) the
  // wrapper's data-cs-pdf is opened in a new tab. Validation/email are untouched.
  root.querySelectorAll('.cs-cf7[data-cs-btn]').forEach(function (wrap) {
    var label = wrap.getAttribute('data-cs-btn');
    var submit = wrap.querySelector('.wpcf7-submit');
    if (label && submit) { submit.value = label; }
  });

  // Open the PDF once validation passes. wpcf7mailsent = validated + emailed OK;
  // wpcf7mailfailed = validated but the mail transport failed (e.g. SMTP not
  // configured locally). Both mean the submission passed validation, so the
  // white paper opens in either case — matching the source HTML, which opened
  // the PDF on a valid submit. It never fires on wpcf7invalid / wpcf7spam.
  var csOpenWhitePaper = function (e) {
    var scope = e && e.target ? e.target.closest('.cs-cf7[data-cs-pdf]') : null;
    if (!scope) { return; }
    var pdf = scope.getAttribute('data-cs-pdf');
    if (pdf) { window.open(pdf, '_blank'); }
  };
  document.addEventListener('wpcf7mailsent', csOpenWhitePaper);
  document.addEventListener('wpcf7mailfailed', csOpenWhitePaper);
})();
