/* Writing Timeline: load more + year chips (progressive enhancement). */
(function () {
  var root = document.getElementById('timeline');
  if (!root || !window.fetch) { return; }
  var ep = root.getAttribute('data-endpoint');
  var next = parseInt(root.getAttribute('data-next'), 10) || 0;
  var mode = root.getAttribute('data-mode');
  var moreWrap = document.querySelector('.tl-more');
  var link = document.querySelector('.tl-more__link');
  var btn = document.querySelector('.tl-more__button');
  var status = document.querySelector('.tl-more__status');
  var chips = document.querySelectorAll('.tl-chip');
  var busy = false;
  var filter = 'all';

  if (btn && link) { link.hidden = true; btn.hidden = false; }

  function sections() { return Array.prototype.slice.call(root.querySelectorAll('.tl-year')); }
  function section(y) { return root.querySelector('.tl-year[data-year="' + y + '"]'); }
  function complete(y) {
    var s = section(y);
    return !!s && s.querySelectorAll('.tl-post').length >= parseInt(s.getAttribute('data-count'), 10);
  }
  function say(msg) { if (status) { status.textContent = msg; } }

  function merge(html) {
    var tpl = document.createElement('template');
    tpl.innerHTML = html;
    Array.prototype.slice.call(tpl.content.querySelectorAll('.tl-year')).forEach(function (sec) {
      var y = parseInt(sec.getAttribute('data-year'), 10);
      var ex = section(y);
      if (!ex) {
        var before = sections().filter(function (s) { return parseInt(s.getAttribute('data-year'), 10) < y; })[0];
        if (filter !== 'all' && String(y) !== filter) { sec.hidden = true; }
        root.insertBefore(sec, before || null);
        return;
      }
      var ol = ex.querySelector('.tl-days');
      Array.prototype.slice.call(sec.querySelectorAll('.tl-day')).forEach(function (d) {
        var day = d.getAttribute('data-day');
        if (ol.querySelector('.tl-day[data-day="' + day + '"]')) { return; }
        var b = Array.prototype.slice.call(ol.children).filter(function (c) { return c.getAttribute('data-day') < day; })[0];
        ol.insertBefore(d, b || null);
      });
    });
    watch();
  }

  function get(qs) {
    return fetch(ep + (ep.indexOf('?') > -1 ? '&' : '?') + qs, { headers: { Accept: 'application/json' } })
      .then(function (r) { if (!r.ok) { throw new Error(r.status); } return r.json(); });
  }

  function setBusy(on) {
    busy = on;
    if (btn) {
      btn.disabled = on;
      btn.setAttribute('aria-busy', on ? 'true' : 'false');
      btn.textContent = on ? 'Loading\u2026' : 'Load more';
    }
  }

  function syncMore() {
    if (!moreWrap) { return; }
    var show = filter === 'all' && next > 0;
    if (btn) { btn.hidden = !show; }
  }

  function loadMore() {
    if (busy || !next) { return; }
    setBusy(true);
    say('Loading more articles');
    get('page=' + next).then(function (j) {
      merge(j.html);
      next = j.next || 0;
      setBusy(false);
      syncMore();
      say(next ? 'Loaded ' + j.posts + ' more articles' : 'All articles loaded');
    }).catch(function () {
      setBusy(false);
      say('Could not load more articles. Please try again.');
    });
  }

  function show(y) {
    filter = y;
    sections().forEach(function (s) { s.hidden = !(y === 'all' || s.getAttribute('data-year') === y); });
    Array.prototype.forEach.call(chips, function (c) {
      var on = c.getAttribute('data-year') === y;
      c.classList.toggle('is-active', on);
      if (on) { c.setAttribute('aria-current', 'true'); } else { c.removeAttribute('aria-current'); }
    });
    syncMore();
    markInView();
  }

  var bar = document.querySelector('.tl-filter');
  var list = document.querySelector('.tl-filter__list');
  function offset() {
    var nav = document.querySelector('.site-header nav') || document.querySelector('.site-header');
    var head = nav ? nav.getBoundingClientRect().bottom : 70;
    return Math.max(0, head) + (bar ? bar.offsetHeight : 0) + 12;
  }

  function jump(el) {
    if (!el) { return; }
    var top = el.getBoundingClientRect().top + window.scrollY - offset();
    window.scrollTo({ top: Math.max(0, top) });
  }

  /* Highlight the year currently in view. */
  var io = null;
  var visible = {};
  function markInView() {
    var cur = sections().filter(function (s) { return !s.hidden && visible[s.getAttribute('data-year')]; })[0];
    var y = cur ? cur.getAttribute('data-year') : null;
    Array.prototype.forEach.call(chips, function (c) {
      var on = y !== null && c.getAttribute('data-year') === y;
      if (on && !c.classList.contains('is-inview') && list) {
        var l = c.offsetLeft, r = l + c.offsetWidth;
        if (l < list.scrollLeft || r > list.scrollLeft + list.clientWidth) { list.scrollLeft = l - 24; }
      }
      c.classList.toggle('is-inview', on);
    });
  }
  function watch() {
    if (!('IntersectionObserver' in window)) { return; }
    if (io) { io.disconnect(); }
    io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { visible[en.target.getAttribute('data-year')] = en.isIntersecting; });
      markInView();
    }, { rootMargin: '-' + offset() + 'px 0px -55% 0px', threshold: 0 });
    sections().forEach(function (s) { io.observe(s); });
  }

  function pickYear(y) {
    if (complete(y)) {
      show(y);
      history.replaceState(null, '', '#y' + y);
      jump(section(y));
      return;
    }
    if (busy) { return; }
    setBusy(true);
    say('Loading ' + y);
    get('y=' + y).then(function (j) {
      merge(j.html);
      setBusy(false);
      show(y);
      history.replaceState(null, '', '#y' + y);
      jump(section(y));
      say('Showing ' + j.posts + ' articles from ' + y);
    }).catch(function () {
      setBusy(false);
      say('Could not load ' + y + '. Please try again.');
    });
  }

  if (btn) { btn.addEventListener('click', loadMore); }
  watch();

  Array.prototype.forEach.call(chips, function (c) {
    c.addEventListener('click', function (e) {
      var y = c.getAttribute('data-year');
      if (y === 'all') {
        if (mode === 'year') { return; } // server-rendered single year: let the link reload the full page.
        e.preventDefault();
        show('all');
        history.replaceState(null, '', location.pathname + location.search);
        jump(root);
        return;
      }
      e.preventDefault();
      pickYear(y);
    });
  });

  var m = /^#y(\d{4})$/.exec(location.hash);
  if (m && mode !== 'year') { pickYear(m[1]); }
})();
