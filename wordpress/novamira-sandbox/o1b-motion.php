<?php
/**
 * Header alignment + immersive entrance (Local mockup). Complements o1b-look.php.
 */
if (!defined('ABSPATH')) {
  exit;
}

add_filter('get_the_archive_title', function ($title) {
  if (is_home() && !is_front_page()) {
    $id = (int) get_option('page_for_posts');
    return $id ? get_the_title($id) : 'Blog';
  }
  return $title;
});

add_action('wp_head', function () {
  if (isset($_GET['elementor-preview'])) {
    return;
  }
  echo "<script>(function(){var r=document.documentElement;if(window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;if(r.className.indexOf('js-anim')===-1)r.className+=' js-anim';setTimeout(function(){if(!r.hasAttribute('data-anim-ready')){r.className=r.className.replace(/\\bjs-anim\\b/g,'');}},3000);})();</script>\n";
}, 0);

add_action('wp_enqueue_scripts', function () {
  wp_add_inline_style('o1b-chrome', o1b_motion_css());
  wp_add_inline_script('o1b-chrome', o1b_motion_js());
}, 40);

function o1b_motion_css() {
  return <<<'CSS'
.o1b-header-bar,.o1b-header-bar > .e-con-inner{
  justify-content:flex-start!important;
}
.o1b-header-nav{
  flex:0 0 auto!important;width:auto!important;max-width:none!important;
  margin-left:auto!important;
}
.o1b-header-nav .elementor-widget-nav-menu,
.o1b-header-nav .elementor-widget-container,
.o1b-header-nav .elementor-nav-menu--main,
.o1b-header-nav .elementor-nav-menu-wrapper{
  width:auto!important;max-width:none!important;margin:0!important;
}
.elementor-location-header .elementor-nav-menu--main .elementor-nav-menu{
  justify-content:flex-start!important;width:auto!important;
}
.o1b-phone{margin-left:40px!important}
.o1b-header-cta{margin-left:0!important;flex:none!important;width:auto!important}
@media (max-width:1080px){
  .o1b-phone{margin-left:24px!important}
}
@media (max-width:900px){
  .o1b-header-nav{margin-left:0!important;max-width:50px}
  .o1b-header-cta{margin-left:auto!important}
  .o1b-phone{margin-left:0!important}
}
.o1b-hero-zoom{
  position:absolute;inset:0;z-index:0;pointer-events:none;
  background-position:center;background-size:cover;background-repeat:no-repeat;
  transform:scale(1.14);
}
.js-anim .o1b-hero-zoom{animation:o1bHeroZoom 14s cubic-bezier(.16,.62,.28,1) forwards}
@keyframes o1bHeroZoom{to{transform:scale(1)}}
@keyframes o1bRiseIn{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:none}
}
@keyframes o1bLineGrow{
  from{width:0;opacity:0}
  to{width:96px;opacity:1}
}
@keyframes o1bFloaty{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-14px)}
}
.o1b-floatcard{animation:o1bFloaty 6s ease-in-out infinite}
.o1b-floatchip{animation:o1bFloaty 7.5s ease-in-out infinite reverse}
.js-anim .o1b-hero .elementor-widget-image,
.js-anim .o1b-hero h1.elementor-heading-title,
.js-anim .o1b-hero .o1b-hero-sub,
.js-anim .o1b-hero .o1b-actions,
.js-anim .o1b-hero .o1b-hero-points,
.js-anim .o1b-page-hero h1.elementor-heading-title,
.js-anim .o1b-page-hero .elementor-widget-text-editor,
.js-anim .o1b-page-hero .o1b-actions{
  opacity:0;
  animation:o1bRiseIn .72s cubic-bezier(.22,.61,.36,1) forwards;
}
.js-anim .o1b-hero .elementor-widget-image{animation-delay:.08s}
.js-anim .o1b-hero h1.elementor-heading-title{animation-delay:.2s}
.js-anim .o1b-hero .o1b-hero-sub{animation-delay:.34s}
.js-anim .o1b-hero .o1b-actions{animation-delay:.46s}
.js-anim .o1b-hero .o1b-hero-points{animation-delay:.58s}
.js-anim .o1b-page-hero h1.elementor-heading-title{animation-delay:.2s}
.js-anim .o1b-page-hero .elementor-widget-text-editor{animation-delay:.34s}
.js-anim .o1b-page-hero .o1b-actions{animation-delay:.46s}
.js-anim .o1b-h-rule .elementor-heading-title:after{
  animation:o1bLineGrow .7s cubic-bezier(.22,.61,.36,1) .62s backwards;
}
.js-anim .o1b-card,.js-anim .o1b-mini,.js-anim .o1b-partner,
.js-anim .o1b-gallery-item,.js-anim .o1b-intro-media,.js-anim .o1b-stage-wrap,
.js-anim .o1b-grw,.js-anim .o1b-step,.js-anim .o1b-areas-col,.js-anim .o1b-mgr-photo,
.js-anim .o1b-mgr-col,.js-anim .o1b-awards,.js-anim .formcard,.js-anim .o1b-showroom,
.js-anim .o1b-nap,.js-anim .o1b-stats,.js-anim .o1b-sec-intro,
.js-anim .o1b-about-copy > .e-con-inner > .elementor-widget,
.js-anim .o1b-about-copy > .e-con-inner > .o1b-pullout,
.js-anim .o1b-about-copy > .e-con-inner > .o1b-actions,
.js-anim .o1b-wm-faq .elementor-widget-accordion,
.js-anim .o1b-watermark > .e-con-inner > .elementor-widget-heading:not(.elementor-absolute):not(.o1b-ghost),
.js-anim .o1b-watermark > .e-con-inner > .elementor-widget-text-editor,
.js-anim .o1b-closing .o1b-actions,
.js-anim .o1b-wm-start > .e-con-inner > .o1b-actions{
  opacity:0;
  transform:translateY(34px);
  transition:opacity .75s cubic-bezier(.22,.61,.36,1),transform .75s cubic-bezier(.22,.61,.36,1);
  transition-delay:var(--d,0s);
}
.js-anim .o1b-intro-media,.js-anim .o1b-gallery-item,.js-anim .o1b-stage-wrap,
.js-anim .o1b-mgr-photo,.js-anim .formcard,.js-anim .o1b-grw,.js-anim .o1b-showroom{
  transform:translateY(44px) scale(.97);
}
.js-anim .is-in{opacity:1!important;transform:none!important}
.elementor-location-header.o1b-header-ready .o1b-header-bar,
.elementor-location-header.o1b-header-ready .o1b-header-bar > .e-con-inner{
  transition:min-height .3s ease,height .3s ease;
}
.elementor-location-header.is-stuck .o1b-header-bar,
.elementor-location-header.is-stuck .o1b-header-bar > .e-con-inner{
  min-height:70px!important;height:70px!important;
}
body.blog #content,
body.blog .site-main,
body.archive #content,
body.archive .site-main,
body.single-post #content,
body.single-post .site-main{
  margin:0 auto!important;padding:72px 24px 96px!important;
  max-width:880px!important;
}
body.blog .page-header,
body.archive .page-header,
body.single-post .entry-header{
  display:block!important;text-align:center;margin:0 0 36px;
}
body.blog .page-header .entry-title,
body.archive .page-header .entry-title,
body.single-post .entry-title{
  display:block!important;margin:0 0 12px;
  font-family:'Montserrat',Arial,sans-serif;font-weight:700;
  text-transform:uppercase;letter-spacing:.4px;color:#292e2e;
}
body.blog article:not(.elementor-post),
body.archive article:not(.elementor-post){
  background:#fff;border:1px solid #d9d9d6;border-left:3px solid #b79a61;
  border-radius:4px;padding:28px 28px 24px;margin:0 0 22px;
  box-shadow:0 3px 10px rgba(0,0,0,.08);
}
body.blog article:not(.elementor-post) .entry-title,
body.archive article:not(.elementor-post) .entry-title{
  text-align:left;text-transform:none;font-size:1.35rem;margin:0 0 8px;
}
body.elementor-page .page-header,
body.elementor-page .entry-header,
body.elementor-page .page-title{display:none!important}
@media (prefers-reduced-motion:reduce){
  .js-anim .o1b-card,.js-anim .o1b-mini,.js-anim .o1b-partner,
  .js-anim .o1b-gallery-item,.js-anim .o1b-intro-media,.js-anim .o1b-stage-wrap,
  .js-anim .o1b-grw,.js-anim .o1b-step,.js-anim .o1b-hero .elementor-widget-image,
  .js-anim .o1b-hero h1.elementor-heading-title,.js-anim .o1b-hero .o1b-hero-sub,
  .js-anim .o1b-hero .o1b-actions,.js-anim .o1b-hero .o1b-hero-points{
    opacity:1!important;transform:none!important;
  }
  .o1b-hero-zoom,.o1b-floatcard,.o1b-floatchip{animation:none!important;transform:none!important}
}
CSS;
}

function o1b_motion_js() {
  return <<<'JS'
(function () {
  var root = document.documentElement;
  root.setAttribute("data-anim-ready", "");
  if (location.search.indexOf("elementor-preview") !== -1 || document.body.classList.contains("elementor-editor-active")) {
    root.className = root.className.replace(/\bjs-anim\b/g, "");
    return;
  }
  var reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  document.querySelectorAll(".o1b-hero, .o1b-page-hero").forEach(function (el) {
    if (el.querySelector(".o1b-hero-zoom")) return;
    var st = window.getComputedStyle(el);
    var bg = st.backgroundImage;
    if (!bg || bg === "none") return;
    var layer = document.createElement("div");
    layer.className = "o1b-hero-zoom";
    layer.style.backgroundImage = bg;
    layer.style.backgroundPosition = st.backgroundPosition || "center";
    layer.style.backgroundSize = "cover";
    el.style.setProperty("background-image", "none", "important");
    el.insertBefore(layer, el.firstChild);
  });
  var header = document.querySelector(".elementor-location-header");
  if (header && !header.getAttribute("data-o1b-stick")) {
    header.setAttribute("data-o1b-stick", "1");
    var stuck = false;
    var primed = false;
    var onScroll = function () {
      var y = window.pageYOffset || document.documentElement.scrollTop || 0;
      var should = stuck ? y > 20 : y > 80;
      if (should !== stuck) {
        stuck = should;
        header.classList.toggle("is-stuck", stuck);
      }
      if (!primed) {
        primed = true;
        requestAnimationFrame(function () {
          header.classList.add("o1b-header-ready");
        });
      }
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
  }
  var sel = ".o1b-card,.o1b-mini,.o1b-partner,.o1b-gallery-item,.o1b-intro-media,.o1b-stage-wrap,.o1b-grw,.o1b-step,.o1b-areas-col,.o1b-mgr-photo,.o1b-mgr-col,.o1b-awards,.formcard,.o1b-showroom,.o1b-nap,.o1b-stats,.o1b-sec-intro,.o1b-about-copy > .e-con-inner > .elementor-widget,.o1b-about-copy > .e-con-inner > .o1b-pullout,.o1b-about-copy > .e-con-inner > .o1b-actions,.o1b-wm-faq .elementor-widget-accordion,.o1b-watermark > .e-con-inner > .elementor-widget-heading:not(.elementor-absolute):not(.o1b-ghost),.o1b-watermark > .e-con-inner > .elementor-widget-text-editor,.o1b-closing .o1b-actions,.o1b-wm-start > .e-con-inner > .o1b-actions";
  var els = Array.prototype.slice.call(document.querySelectorAll(sel)).filter(function (el, i, arr) { return arr.indexOf(el) === i; });
  if (reduced || !("IntersectionObserver" in window)) {
    els.forEach(function (el) { el.classList.add("is-in"); });
    return;
  }
  var groups = [];
  els.forEach(function (el) {
    var g = null;
    for (var i = 0; i < groups.length; i++) {
      if (groups[i].parent === el.parentNode) { g = groups[i]; break; }
    }
    if (!g) { g = { parent: el.parentNode, items: [] }; groups.push(g); }
    g.items.push(el);
  });
  groups.forEach(function (g) {
    if (g.items.length < 2) return;
    g.items.forEach(function (el, i) { el.style.setProperty("--d", (i * 0.09).toFixed(2) + "s"); });
  });
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-in");
      io.unobserve(entry.target);
    });
  }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });
  els.forEach(function (el) { io.observe(el); });
})();
JS;
}
