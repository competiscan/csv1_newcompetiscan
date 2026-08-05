// Opens the shared "Get In Touch" contact modal (CF7 form) from any Contact Us
// trigger, site-wide. Triggers: elements with class .cs-contact-btn, or any
// link/button whose text is exactly "Contact Us". Closes on the X, the overlay
// backdrop, or Escape.
(function () {
  'use strict';

  var overlay = document.querySelector('[data-cs-contact-modal]');
  if (!overlay) return;

  function setOpen(open) {
    overlay.classList.toggle('is-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
    if (open) {
      var first = overlay.querySelector('input, textarea');
      if (first) { try { first.focus(); } catch (e) {} }
    }
  }

  function isTrigger(el) {
    if (!el) return false;
    if (el.classList && el.classList.contains('cs-contact-btn')) return true;
    var txt = (el.textContent || '').trim().toLowerCase();
    var tag = el.tagName;
    if ((tag === 'A' || tag === 'BUTTON') && txt === 'contact us') return true;
    return false;
  }

  // Delegate so it also covers header/footer links rendered by the theme.
  document.addEventListener('click', function (e) {
    var el = e.target;
    while (el && el !== document.body) {
      if (isTrigger(el)) {
        // Don't hijack elements explicitly wired to Calendly.
        if (el.hasAttribute && el.hasAttribute('data-cs-calendly')) return;
        e.preventDefault();
        setOpen(true);
        return;
      }
      el = el.parentElement;
    }
  });

  overlay.querySelectorAll('[data-cs-contact-close]').forEach(function (b) {
    b.addEventListener('click', function () { setOpen(false); });
  });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) setOpen(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('is-open')) setOpen(false);
  });

  // Close automatically after a successful CF7 submission.
  document.addEventListener('wpcf7mailsent', function () {
    setTimeout(function () { setOpen(false); }, 2000);
  });

  // Deep link: opening a page at #contact shows the contact modal.
  if (window.location.hash === '#contact') {
    setOpen(true);
  }
  window.addEventListener('hashchange', function () {
    if (window.location.hash === '#contact') { setOpen(true); }
  });
})();
