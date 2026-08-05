// Shared interactions for the Competiscan site.
// Accordions (FAQ / roles), exclusive selection (Insights filters and
// pagination), testimonials carousel (Home) and mockup forms.
(function () {
  'use strict';

  // --- Accordions ----------------------------------------------------------
  // Button: [data-cs-acc-btn="<group>"]. The [data-cs-acc-panel] panel lives
  // in the same container as the button. Exclusive behavior per group:
  // opening one item closes the rest (same as the original openFaq).
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

  // --- Exclusive selection (filter chips and Insights pagination) ---------
  document.querySelectorAll('[data-cs-select]').forEach(function (el) {
    el.addEventListener('click', function () {
      var group = el.getAttribute('data-cs-select');
      document.querySelectorAll('[data-cs-select="' + group + '"]').forEach(function (other) {
        var active = other === el;
        var style = other.getAttribute(active ? 'data-cs-style-active' : 'data-cs-style-idle');
        if (style) other.setAttribute('style', style);
        var check = other.querySelector('[data-cs-check]');
        if (check) check.style.display = active ? 'inline-flex' : 'none';
      });
    });
  });

  // --- Testimonials carousel (Home) -----------------------------------------
  var track = document.querySelector('[data-cs-track]');
  if (track) {
    var index = 0;
    var max = parseInt(track.getAttribute('data-cs-max'), 10) || 0;
    var tx = track.getAttribute('data-cs-tx');
    // At the ends the arrow is disabled (dimmed via the CSS :disabled rule).
    var updateArrows = function () {
      document.querySelectorAll('[data-cs-prev]').forEach(function (b) { b.disabled = index === 0; });
      document.querySelectorAll('[data-cs-next]').forEach(function (b) { b.disabled = index === max; });
    };
    var go = function (n) {
      index = Math.max(0, Math.min(max, n));
      track.style.transform = 'translateX(' + tx.replace('{i}', index) + ')';
      track.setAttribute('data-cs-index', String(index));
      updateArrows();
    };
    document.querySelectorAll('[data-cs-prev]').forEach(function (b) {
      b.addEventListener('click', function () { go(index - 1); });
    });
    document.querySelectorAll('[data-cs-next]').forEach(function (b) {
      b.addEventListener('click', function () { go(index + 1); });
    });
    updateArrows();
  }

  // --- Circular carousel (AI Toolkit deliverables) ---------------------------
  // Root: [data-cs-crsl] with [data-cs-crsl-slide] slides stacked in the same
  // grid cell (crossfade + slide). The controls (prev/next/dots) live in the
  // same <section>. Infinite loop, manual navigation only (no autoplay).
  document.querySelectorAll('[data-cs-crsl]').forEach(function (root) {
    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-cs-crsl-slide]'));
    if (slides.length < 2) return;
    var scope = root.closest('section') || document;
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

    scope.querySelectorAll('[data-cs-crsl-prev]').forEach(function (b) {
      b.addEventListener('click', function () { show(index - 1); });
    });
    scope.querySelectorAll('[data-cs-crsl-next]').forEach(function (b) {
      b.addEventListener('click', function () { show(index + 1); });
    });
    dots.forEach(function (d, i) {
      d.addEventListener('click', function () { show(i); });
    });
    show(0);
  });

  // --- Mobile menu (burger) --------------------------------------------------
  // The [data-cs-burger] button opens the [data-cs-mobilemenu] panel
  // (full-screen overlay). Solutions / About Us are [data-cs-mmacc] accordions
  // whose panel is the next sibling. It closes with the X, with Escape or by
  // following any link in the menu.
  var burger = document.querySelector('[data-cs-burger]');
  var mobileMenu = document.querySelector('[data-cs-mobilemenu]');
  if (burger && mobileMenu) {
    var setMenuOpen = function (open) {
      mobileMenu.classList.toggle('is-open', open);
      burger.setAttribute('aria-expanded', String(open));
      document.body.style.overflow = open ? 'hidden' : '';
    };
    burger.addEventListener('click', function () { setMenuOpen(true); });
    var closeBtn = mobileMenu.querySelector('[data-cs-mobilemenu-close]');
    if (closeBtn) closeBtn.addEventListener('click', function () { setMenuOpen(false); });
    mobileMenu.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { setMenuOpen(false); });
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) setMenuOpen(false);
    });
    mobileMenu.querySelectorAll('[data-cs-mmacc]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var panel = btn.nextElementSibling;
        if (!panel) return;
        var opening = panel.hidden;
        panel.hidden = !opening;
        btn.setAttribute('aria-expanded', String(opening));
      });
    });
  }

  // --- White paper download modal ---------------------------------------------
  // The [data-cs-wpmodal-open] button opens the [data-cs-wpmodal] overlay. The
  // download only fires on form submit (Name, business email, company); the
  // PDF path lives in the form's own data-cs-wpmodal-file attribute.
  var wpModal = document.querySelector('[data-cs-wpmodal]');
  if (wpModal) {
    var setWpOpen = function (open) {
      wpModal.hidden = !open;
      // The overlay uses inline styles, so the hidden attribute is not
      // enough: display must be toggled directly.
      wpModal.style.display = open ? 'flex' : 'none';
      document.body.style.overflow = open ? 'hidden' : '';
      if (open) {
        var first = wpModal.querySelector('input');
        if (first) first.focus();
      }
    };
    document.querySelectorAll('[data-cs-wpmodal-open]').forEach(function (b) {
      b.addEventListener('click', function () { setWpOpen(true); });
    });
    wpModal.querySelectorAll('[data-cs-wpmodal-close]').forEach(function (b) {
      b.addEventListener('click', function () { setWpOpen(false); });
    });
    wpModal.addEventListener('click', function (e) {
      if (e.target === wpModal) setWpOpen(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !wpModal.hidden) setWpOpen(false);
    });
    var wpForm = wpModal.querySelector('[data-cs-wpmodal-form]');
    if (wpForm) {
      wpForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var link = document.createElement('a');
        link.href = wpForm.getAttribute('data-cs-wpmodal-file');
        link.download = '';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setWpOpen(false);
        wpForm.reset();
      });
    }
  }

  // --- Inline white paper form ----------------------------------------------
  // Gated case-study form rendered directly on the page (AI Toolkit). On submit
  // it starts the PDF download named in the form's data-cs-wpfile attribute,
  // mirroring the white paper modal above but without an overlay.
  document.querySelectorAll('[data-cs-whitepaper]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
      var link = document.createElement('a');
      link.href = form.getAttribute('data-cs-wpfile');
      link.download = '';
      document.body.appendChild(link);
      link.click();
      link.remove();
      form.reset();
    });
  });

  // --- Mockup forms (no backend yet) ----------------------------------------
  document.querySelectorAll('[data-cs-form]').forEach(function (f) {
    f.addEventListener('submit', function (e) { e.preventDefault(); });
  });

  // --- Calendly popup (Schedule a Demo) --------------------------------------
  // Every contact call to action opens the Calendly scheduling popup instead of
  // a mailto link or the legacy "Get In Touch" modal. The Calendly script and
  // stylesheet are injected once, on demand, the first time a trigger is used.
  // Triggers: .cs-contact-btn buttons (header/mobile menu), any link whose text
  // is "Contact Us" (footer), and any element marked with [data-cs-calendly]
  // (the primary action buttons on the solution pages). Career "Apply" buttons
  // and the plain footer/inline email addresses keep their mailto: behavior.
  (function () {
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

    // Triggers: contact buttons, "Contact Us" links and marked action buttons.
    document.querySelectorAll('.cs-contact-btn, [data-cs-calendly], a').forEach(function (el) {
      var isTrigger = el.classList.contains('cs-contact-btn') ||
        el.hasAttribute('data-cs-calendly') ||
        (el.tagName === 'A' && el.textContent.trim() === 'Contact Us');
      if (!isTrigger) return;
      el.addEventListener('click', function (e) {
        e.preventDefault();
        openCalendly();
      });
    });
  })();

  // --- "Get In Touch" contact modal (disabled) -------------------------------
  // Superseded by the Calendly popup above. The mockup contact form is no
  // longer built or wired to any trigger. The former implementation is kept
  // here, guarded, so it can be restored if the form is ever needed again.
  var CONTACT_MODAL_ENABLED = false;
  if (CONTACT_MODAL_ENABLED) (function () {
    var host = document.createElement('div');
    host.innerHTML =
      '<div class="cs-ctm-overlay" data-cs-ctm role="dialog" aria-modal="true" aria-label="Contact us">' +
        '<div class="cs-ctm">' +
          '<button type="button" class="cs-ctm-close" aria-label="Close" data-cs-ctm-close>' +
            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"></path></svg>' +
          '</button>' +
          '<h2 class="cs-ctm-title">Get In Touch</h2>' +
          '<p class="cs-ctm-sub">Please feel free to contact us any time. We will get back to you as soon as possible.<br>Fill all the required fields and click on the send message button to submit the form.</p>' +
          '<div class="cs-ctm-divider">' +
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 8-10 6L2 8"></path></svg>' +
          '</div>' +
          '<form data-cs-ctm-form novalidate>' +
            '<div class="cs-ctm-grid">' +
              '<label class="cs-ctm-field">' +
                '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>' +
                '<input type="text" name="name" required placeholder="Your Name" autocomplete="name">' +
              '</label>' +
              '<label class="cs-ctm-field">' +
                '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 8-10 6L2 8"></path></svg>' +
                '<input type="email" name="email" required placeholder="Your Email" autocomplete="email">' +
              '</label>' +
            '</div>' +
            '<label class="cs-ctm-field" style="margin-bottom: 18px;">' +
              '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>' +
              '<input type="tel" name="phone" placeholder="Your Phone Number" autocomplete="tel">' +
            '</label>' +
            '<label class="cs-ctm-field cs-ctm-msg">' +
              '<textarea name="message" required placeholder="Your Message"></textarea>' +
            '</label>' +
            '<div class="cs-ctm-captcha">' +
              '<div class="cs-ctm-captcha-label">Validation code:</div>' +
              '<div class="cs-ctm-captcha-body">' +
                '<canvas data-cs-ctm-canvas width="130" height="42" aria-label="Validation code image"></canvas>' +
                '<p class="cs-ctm-captcha-hint">Enter the code above here:</p>' +
                '<div class="cs-ctm-captcha-row">' +
                  '<input type="text" name="captcha" required autocomplete="off" aria-label="Validation code">' +
                  '<span class="cs-ctm-captcha-refresh">Can\'t read the image? click <a href="#" data-cs-ctm-refresh>here</a> to refresh.</span>' +
                '</div>' +
              '</div>' +
            '</div>' +
            '<p class="cs-ctm-feedback" data-cs-ctm-feedback hidden></p>' +
            '<button type="submit" class="cs-ctm-send">Send Message</button>' +
          '</form>' +
        '</div>' +
      '</div>';
    var overlay = host.firstElementChild;
    document.body.appendChild(overlay);

    var form = overlay.querySelector('[data-cs-ctm-form]');
    var canvas = overlay.querySelector('[data-cs-ctm-canvas]');
    var feedback = overlay.querySelector('[data-cs-ctm-feedback]');
    var code = '';

    function drawCode() {
      var chars = 'abcdefghjkmnpqrstuvwxyz23456789';
      code = '';
      for (var i = 0; i < 6; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
      var ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = 'rgb(255,255,255)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      // noise dots
      for (var d = 0; d < 60; d++) {
        ctx.fillStyle = 'rgba(0,30,51,' + (0.15 + Math.random() * 0.3) + ')';
        ctx.fillRect(Math.random() * canvas.width, Math.random() * canvas.height, 1.5, 1.5);
      }
      ctx.font = '700 24px Inter, sans-serif';
      ctx.textBaseline = 'middle';
      ctx.fillStyle = 'rgb(20,35,80)';
      for (var c = 0; c < code.length; c++) {
        ctx.save();
        ctx.translate(14 + c * 18, canvas.height / 2 + (Math.random() * 6 - 3));
        ctx.rotate(Math.random() * 0.3 - 0.15);
        ctx.fillText(code.charAt(c), 0, 0);
        ctx.restore();
      }
    }

    function showFeedback(kind, text) {
      feedback.hidden = false;
      feedback.setAttribute('data-kind', kind);
      feedback.textContent = text;
    }

    function setOpen(open) {
      overlay.classList.toggle('is-open', open);
      document.body.style.overflow = open ? 'hidden' : '';
      if (open) {
        feedback.hidden = true;
        drawCode();
        var first = form.querySelector('input');
        if (first) first.focus();
      }
    }

    // Openers: header/mobile menu buttons and "Contact Us" footer links.
    document.querySelectorAll('.cs-contact-btn, a').forEach(function (el) {
      var isBtn = el.classList.contains('cs-contact-btn');
      if (!isBtn && el.textContent.trim() !== 'Contact Us') return;
      el.addEventListener('click', function (e) {
        e.preventDefault();
        setOpen(true);
      });
    });

    overlay.querySelector('[data-cs-ctm-close]').addEventListener('click', function () { setOpen(false); });
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) setOpen(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && overlay.classList.contains('is-open')) setOpen(false);
    });
    overlay.querySelector('[data-cs-ctm-refresh]').addEventListener('click', function (e) {
      e.preventDefault();
      drawCode();
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!form.reportValidity()) return;
      var typed = form.captcha.value.trim().toLowerCase();
      if (typed !== code) {
        showFeedback('error', 'The validation code does not match. Please try again.');
        form.captcha.value = '';
        drawCode();
        form.captcha.focus();
        return;
      }
      showFeedback('ok', 'Thank you! Your message has been sent. We will get back to you as soon as possible.');
      form.reset();
      setTimeout(function () { setOpen(false); }, 1800);
    });
  })();
})();
