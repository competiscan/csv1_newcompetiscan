/* =========================================================
   Competiscan — main.js
   ========================================================= */
document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Mobile menu ---------- */
  var hamburger = document.querySelector('.hamburger');
  var mobileMenu = document.querySelector('.mobile-menu');
  var mobileClose = document.querySelector('.mobile-menu-close');

  function openMenu(){ mobileMenu.classList.add('open'); document.body.style.overflow='hidden'; }
  function closeMenu(){ mobileMenu.classList.remove('open'); document.body.style.overflow=''; }

  if (hamburger) hamburger.addEventListener('click', openMenu);
  if (mobileClose) mobileClose.addEventListener('click', closeMenu);

  /* Mobile submenu accordion (Solutions / About Us) */
  document.querySelectorAll('.mobile-nav-item.has-sub .mobile-nav-link').forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var item = link.closest('.mobile-nav-item');
      var wasOpen = item.classList.contains('open');
      document.querySelectorAll('.mobile-nav-item.has-sub').forEach(function (i) { i.classList.remove('open'); });
      if (!wasOpen) item.classList.add('open');
    });
  });

  /* ---------- Desktop mega-drop (hover with click fallback for touch) ---------- */
  document.querySelectorAll('.nav-item.has-dropdown').forEach(function (item) {
    var trigger = item.querySelector('.nav-link');
    var timeout;
    item.addEventListener('mouseenter', function () {
      clearTimeout(timeout);
      document.querySelectorAll('.nav-item.open').forEach(function (i) { if (i !== item) i.classList.remove('open'); });
      item.classList.add('open');
    });
    item.addEventListener('mouseleave', function () {
      timeout = setTimeout(function () { item.classList.remove('open'); }, 150);
    });
    trigger.addEventListener('click', function (e) {
      if (window.innerWidth <= 768) return;
      e.preventDefault();
      item.classList.toggle('open');
    });
  });

  /* ---------- FAQ accordion ----------
     Moved to the shared, event-delegated handler in assets/js/faq.js so it works
     on every page (and with multiple FAQ sections). Not handled here to avoid
     double-toggling. */

  /* ---------- Insights: filter tabs + result count + reset ---------- */
  var insightsBar = document.querySelector('.filter-bar');
  if (insightsBar && insightsBar.querySelector('.filter-pills .pill')) {
    var tabPills = insightsBar.querySelectorAll('.filter-pills .pill');
    var filterItems = document.querySelectorAll('.filter-item');
    var countEl = insightsBar.querySelector('.results-count');
    var resetEl = insightsBar.querySelector('.filter-reset');
    var searchField = insightsBar.querySelector('.search-box input');
    var searchParam = new URLSearchParams(window.location.search).get('insight_s');
    var hasActiveSearch = !!(searchParam && searchParam.length);

    var activeFilter = function () {
      var a = insightsBar.querySelector('.filter-pills .pill.active');
      return a ? (a.getAttribute('data-filter') || 'all') : 'all';
    };
    // The Reset button is tied to the SEARCH only — a submitted search term or text
    // currently typed in the field. Selecting a tab/category must NOT show it.
    var isActive = function () {
      var typed = searchField ? searchField.value.trim() : '';
      return hasActiveSearch || typed.length > 0;
    };
    // Reset is shown only when a search term is active, hidden otherwise.
    var syncReset = function () {
      if (resetEl) {
        resetEl.classList.toggle('is-visible', isActive());
      }
    };
    var applyTab = function (filter) {
      tabPills.forEach(function (p) {
        p.classList.toggle('active', p.getAttribute('data-filter') === filter);
      });
      filterItems.forEach(function (it) {
        var show = (filter === 'all' || it.getAttribute('data-type') === filter);
        it.style.display = show ? '' : 'none';
      });
      // "Showing X results" is only meaningful for All + Articles.
      if (countEl) {
        countEl.style.display = (filter === 'all' || filter === 'articles') ? '' : 'none';
      }
      syncReset();
    };

    tabPills.forEach(function (p) {
      p.addEventListener('click', function () {
        applyTab(this.getAttribute('data-filter') || 'all');
      });
    });

    if (resetEl) {
      resetEl.addEventListener('click', function (e) {
        if (!hasActiveSearch) {
          // No search in the URL — reset fully on the client, no reload needed.
          e.preventDefault();
          if (searchField) { searchField.value = ''; }
          insightsBar.classList.remove('search-open');
          applyTab('all'); // resets tab + count + hides the reset via syncReset()
        }
        // With an active search, follow the link to the base URL to restore all results.
      });
    }

    // Keep the reset in sync while typing a term too.
    if (searchField) {
      searchField.addEventListener('input', syncReset);
    }

    syncReset();
  }

  /* ---------- Search toggle (search.png two-state behavior) ---------- */
  var filterBar = document.querySelector('.filter-bar');
  var searchToggleBtn = document.querySelector('.search-toggle');
  var filterToggleBtn = document.querySelector('.filter-toggle');

  if (filterBar && searchToggleBtn) {
    searchToggleBtn.addEventListener('click', function () {
      var willOpen = !filterBar.classList.contains('search-open');
      filterBar.classList.toggle('search-open', willOpen);
      searchToggleBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
      if (willOpen) {
        var input = filterBar.querySelector('.search-box input');
        if (input) setTimeout(function () { input.focus(); }, 50);
      }
    });
  }
  if (filterBar && filterToggleBtn) {
    filterToggleBtn.addEventListener('click', function () {
      if (filterBar.classList.contains('search-open')) {
        filterBar.classList.remove('search-open');
      }
    });
  }

  /* ---------- Slick sliders ---------- */
$('.testi-track').slick({
    slidesToShow:1,
    slidesToScroll:1,
    infinite:true,
    arrows:true,
    variableWidth:true,
    autoplay:true,
    prevArrow:$('.carousel-arrow.prev'),
    nextArrow:$('.carousel-arrow.next'),

    responsive:[
        {
            breakpoint:768,
            settings:{
                variableWidth:true
            }
        }
    ]
});

$('.tracking-slider').slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    infinite: true,
    arrows: true,
    prevArrow: $('.carousel-arrow2.prev'),
    nextArrow: $('.carousel-arrow2.next'),
    responsive: [
        
        {
            breakpoint: 767,
            settings: {
                slidesToShow:1,
            slidesToScroll:1,
            variableWidth:true,
            centerMode:false
            }
        },
    ]
});

/* Header scrolled-state flag.
   rAF-throttled + passive so the scroll listener never blocks or thrashes layout,
   and the class only toggles when the state actually changes — this keeps the
   sticky header perfectly smooth on Windows instead of glitching every scroll tick. */
(function () {
  var headerEl = document.querySelector('.site-header');
  if (!headerEl) return;
  var isScrolled = false;
  var ticking = false;
  function applyHeaderState() {
    var scrolled = window.pageYOffset > 20;
    if (scrolled !== isScrolled) {
      headerEl.classList.toggle('header-fixed', scrolled);
      isScrolled = scrolled;
    }
    ticking = false;
  }
  window.addEventListener('scroll', function () {
    if (!ticking) {
      window.requestAnimationFrame(applyHeaderState);
      ticking = true;
    }
  }, { passive: true });
  applyHeaderState();
})();



/* Duplicate the partner logos so the marquee loops seamlessly.
   Guarded: .marquee-row only exists on the home page. */
const row = document.querySelector('.marquee-row');
if (row) row.innerHTML += row.innerHTML;
});


// document.addEventListener('DOMContentLoaded', function () {
//   const header = document.querySelector('.site-header');

//   if (!header) return;

//   function checkScroll() {
//     if (window.scrollY > 20) {
//       header.classList.add('header-fixed');
//     } else {
//       header.classList.remove('header-fixed');
//     }
//   }

//   window.addEventListener('scroll', checkScroll, {
//     passive: true
//   });

//   checkScroll();
// });