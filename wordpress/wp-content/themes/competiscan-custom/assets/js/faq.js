/* =========================================================
   Competiscan — FAQ accordion (single shared handler)

   One event-delegated listener for every FAQ on every page:
   - works no matter when/where the FAQ renders (incl. dynamic),
   - supports multiple FAQ sections on one page (each independent),
   - toggles the existing .faq-item.active class (open/close),
   - does not depend on Elementor or any other script.

   All visuals (smooth height/opacity animation, +/- icon change)
   are handled in CSS via .faq-item.active — this only flips the class.
   ========================================================= */
(function () {
  'use strict';

  document.addEventListener('click', function (e) {
    var target = e.target;
    var btn = target && target.closest ? target.closest('.faq-q') : null;
    if (!btn) {
      return;
    }

    var item = btn.closest('.faq-item');
    if (!item) {
      return;
    }

    e.preventDefault();

    // Keep exclusivity scoped to this FAQ list, so multiple FAQ sections on the
    // same page stay independent. Fall back to the item's parent if no .faq-list.
    var scope = item.closest('.faq-list') || item.parentElement || document;
    var willOpen = !item.classList.contains('active');

    // Close any other open item in the same list (matches the HTML behaviour).
    scope.querySelectorAll('.faq-item.active').forEach(function (other) {
      if (other !== item) {
        other.classList.remove('active');
        var otherBtn = other.querySelector('.faq-q');
        if (otherBtn) {
          otherBtn.setAttribute('aria-expanded', 'false');
        }
      }
    });

    // Toggle the clicked item (closed -> open, open -> close).
    item.classList.toggle('active', willOpen);
    btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  });
})();
