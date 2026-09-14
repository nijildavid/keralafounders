document.addEventListener('DOMContentLoaded', function () {
  var btn = document.querySelector('.menu-toggle');
  var nav = document.querySelector('.navlinks');
  var icon = btn ? btn.querySelector('.menu-icon') : null;

  function setIcon(open) {
    if (icon) icon.src = 'assets/icons/' + (open ? 'close' : 'menu') + '.svg';
  }

  if (btn && nav) {
    btn.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      setIcon(open);
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        nav.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        setIcon(false);
      });
    });
  }

  // Desktop expanded/compact nav: collapse the editorial cards to a plain
  // line once the page scrolls past the hero, expand again near the top.
  var header = document.getElementById('siteHeader');
  if (header) {
    var COMPACT_AT = 48;
    var ticking = false;
    function applyCompactState() {
      header.classList.toggle('is-compact', window.scrollY > COMPACT_AT);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(applyCompactState);
        ticking = true;
      }
    }, { passive: true });
    applyCompactState();
  }
});
