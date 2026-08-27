(function () {
  'use strict';

  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Cancels the failsafe in the inline head script.
  document.documentElement.setAttribute('data-anim-ready', '');

  /* ---------- mobile nav ---------- */
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('nav');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });

    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open menu');
      }
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 900 && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open menu');
      }
    });
  }

  /* ---------- one FAQ answer open at a time ---------- */
  var faqItems = document.querySelectorAll('.faq__item');
  faqItems.forEach(function (item) {
    item.addEventListener('toggle', function () {
      if (!item.open) return;
      faqItems.forEach(function (other) {
        if (other !== item) other.open = false;
      });
    });
  });

  /* ---------- header condenses past the hero ---------- */
  var header = document.getElementById('siteHeader');
  if (header) {
    var stuck = false;
    var onScroll = function () {
      var should = window.pageYOffset > 60;
      if (should !== stuck) {
        stuck = should;
        header.classList.toggle('is-stuck', stuck);
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- scroll reveal ---------- */
  var revealEls = Array.prototype.slice.call(document.querySelectorAll('[data-reveal]'));

  var showAll = function () {
    revealEls.forEach(function (el) { el.classList.add('is-in'); });
  };

  if (reduced || !('IntersectionObserver' in window)) {
    showAll();
  } else {
    // Siblings in the same container cascade rather than landing together.
    var groups = [];
    revealEls.forEach(function (el) {
      var group = null;
      for (var i = 0; i < groups.length; i++) {
        if (groups[i].parent === el.parentNode) { group = groups[i]; break; }
      }
      if (!group) { group = { parent: el.parentNode, items: [] }; groups.push(group); }
      group.items.push(el);
    });

    groups.forEach(function (group) {
      if (group.items.length < 2) return;
      group.items.forEach(function (el, i) {
        el.style.setProperty('--d', (i * 0.09).toFixed(2) + 's');
      });
    });

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-in');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    revealEls.forEach(function (el) { observer.observe(el); });
  }

  /* ---------- stat counters ---------- */
  var stats = Array.prototype.slice.call(document.querySelectorAll('.stat__num'));

  function runCounter(el) {
    var original = el.textContent.trim();
    var parsed = original.match(/^(\D*)([\d,]+(?:\.\d+)?)(.*)$/);
    if (!parsed) return;

    var prefix = parsed[1];
    var raw = parsed[2];
    var suffix = parsed[3];
    var target = parseFloat(raw.replace(/,/g, ''));
    var decimals = (raw.split('.')[1] || '').length;
    var grouped = raw.indexOf(',') > -1;

    if (isNaN(target)) return;

    var format = function (n) {
      var s = n.toFixed(decimals);
      if (grouped) s = s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      return prefix + s + suffix;
    };

    // Restoring the original string (rather than a formatted value) keeps the
    // rendered figure byte-identical to the markup and to the schema.
    var settle = function () { el.textContent = original; };

    var duration = 1500;
    var startTime = Date.now();
    // If rAF stalls or never fires, the real number still lands.
    var failsafe = setTimeout(settle, duration + 400);

    var tick = function () {
      var p = Math.min((Date.now() - startTime) / duration, 1);
      if (p >= 1) {
        clearTimeout(failsafe);
        settle();
        return;
      }
      el.textContent = format(target * (1 - Math.pow(1 - p, 3)));
      requestAnimationFrame(tick);
    };

    el.textContent = format(0);
    requestAnimationFrame(tick);
  }

  if (stats.length && !reduced && 'IntersectionObserver' in window && window.requestAnimationFrame) {
    var statObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        runCounter(entry.target);
        statObserver.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    stats.forEach(function (el) { statObserver.observe(el); });
  }
})();
