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

  /* ---------- FAQ accordion ---------- */
  document.querySelectorAll('.faq-item').forEach(function (item) {
    var q = item.querySelector('.faq-q');
    q.addEventListener('click', function () {
      var wasOpen = item.classList.contains('active');
      document.querySelectorAll('.faq-item').forEach(function (i) { i.classList.remove('active'); });
      if (!wasOpen) item.classList.add('active');
    });
  });

  /* ---------- Insights: filter pills ---------- */
  // var pills = document.querySelectorAll('.filter-pills .pill');
  // var cards = document.querySelectorAll('.article-card');
  // pills.forEach(function (pill) {
  //   pill.addEventListener('click', function () {
  //     pills.forEach(function (p) { p.classList.remove('active'); });
  //     pill.classList.add('active');
  //     var filter = pill.dataset.filter;
  //     cards.forEach(function (card) {
  //       if (filter === 'all' || card.dataset.type === filter) {
  //         card.style.display = '';
  //       } else {
  //         card.style.display = 'none';
  //       }
  //     });
  //   });
  // });

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

$(window).on("scroll", function () {
    if ($(this).scrollTop() > 100) {
        $("header").addClass("header-fixed");
    } else {
        $("header").removeClass("header-fixed");
    }
});
/* Duplicate the partner logos so the marquee loops seamlessly.
   Guarded: .marquee-row only exists on the home page. */
const row = document.querySelector('.marquee-row');
if (row) row.innerHTML += row.innerHTML;
});
