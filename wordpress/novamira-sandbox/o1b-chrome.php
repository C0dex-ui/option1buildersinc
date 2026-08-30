<?php
/**
 * Option 1 Builders chrome: fonts, header/footer polish, form, sticky CTA, SEO.
 */
if (!defined('ABSPATH')) {
  exit;
}

add_filter('run_wptexturize', '__return_false');

add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'o1b-fonts',
    'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap',
    [],
    null
  );
  wp_register_style('o1b-chrome', false, ['o1b-fonts'], '1.9.4');
  wp_enqueue_style('o1b-chrome');
  wp_add_inline_style('o1b-chrome', o1b_chrome_css());
  wp_register_script('o1b-chrome', false, ['jquery'], '1.9.4', true);
  wp_enqueue_script('o1b-chrome');
  wp_add_inline_script('o1b-chrome', o1b_chrome_js());
}, 30);

add_action('wp_head', function () {
  if (isset($_GET['elementor-preview'])) {
    return;
  }
  echo "<script>(function(){var r=document.documentElement;if(window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;r.className+=' js-anim';setTimeout(function(){if(!r.hasAttribute('data-anim-ready')){r.className=r.className.replace(/\\bjs-anim\\b/g,'');}},3000);})();</script>\n";
}, 0);

add_action('wp_head', function () {
  $titles = [
    'home' => 'Artificial Grass Installation Encino | Licensed & Insured | 15-Year Warranty',
    'about-us' => 'Landscaping Company Encino | Licensed Since 2002',
    'services' => 'Landscaping Services Encino | Turf, Pavers & Yards',
    'projects' => 'Landscaping Projects Encino | Real Yards We Built',
    'contact-us' => 'Free Estimate Encino | Licensed Option 1 Builders',
    'blog' => 'Encino Landscaping Blog | Option 1 Builders Tips',
  ];
  $descs = [
    'home' => 'Artificial grass installation in Encino and the San Fernando Valley. Licensed, insured crews, 15-year warranty. Free on-site estimate. Call 818-297-2475.',
    'about-us' => 'Option 1 Builders is a landscaping company in Encino. In-house crews since 2002, CA license #1122918, and a 15-year turf warranty. Call 818-297-2475.',
    'services' => 'Landscaping services in Encino: artificial grass, pavers, and full yards quoted as separate lines. Licensed crews. Call 818-297-2475.',
    'projects' => 'Landscaping projects in Encino and the San Fernando Valley. Artificial grass, pavers, and full yards by Option 1 Builders. Call 818-297-2475.',
    'contact-us' => 'Request a free estimate in Encino. Licensed Option 1 Builders walks the yard and sends a written scope. Call 818-297-2475.',
    'blog' => 'Encino landscaping blog from Option 1 Builders: turf bases, bid comparisons, and what Valley heat does to a cheap quote.',
  ];
  $key = o1b_current_page_key();
  if (!$key || empty($descs[$key])) {
    return;
  }
  echo '<meta name="description" content="' . esc_attr($descs[$key]) . '">' . "\n";
  echo '<link rel="canonical" href="' . esc_url(home_url($key === 'home' ? '/' : '/' . $key . '/')) . '">' . "\n";
}, 2);

add_filter('pre_get_document_title', function ($title) {
  $titles = [
    'home' => 'Artificial Grass Installation Encino | Licensed & Insured | 15-Year Warranty',
    'about-us' => 'Landscaping Company Encino | Licensed Since 2002',
    'services' => 'Landscaping Services Encino | Turf, Pavers & Yards',
    'projects' => 'Landscaping Projects Encino | Real Yards We Built',
    'contact-us' => 'Free Estimate Encino | Licensed Option 1 Builders',
    'blog' => 'Encino Landscaping Blog | Option 1 Builders Tips',
  ];
  $key = o1b_current_page_key();
  return ($key && !empty($titles[$key])) ? $titles[$key] : $title;
}, 20);

add_action('wp_footer', function () {
  $estimate = is_page('contact-us') ? '#estimate' : home_url('/contact-us/');
  echo '<div class="sticky-cta"><a href="tel:+18182972475">Call Now</a><a class="sticky-cta__alt" href="' . esc_url($estimate) . '">Free Estimate</a></div>';
}, 20);

function o1b_current_page_key() {
  if (is_front_page()) {
    return 'home';
  }
  if (is_home()) {
    return 'blog';
  }
  if (is_page()) {
    return get_post_field('post_name', get_queried_object_id());
  }
  return '';
}

function o1b_chrome_js() {
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

  var sel = [
    ".o1b-about-copy > .e-con-inner > .elementor-widget",
    ".o1b-about-copy > .e-con-inner > .o1b-pullout",
    ".o1b-about-copy > .e-con-inner > .o1b-actions",
    ".o1b-intro-media",
    ".o1b-stats",
    ".o1b-sec-intro",
    ".o1b-card",
    ".o1b-mini",
    ".o1b-partner",
    ".o1b-gallery-item",
    ".o1b-stage-wrap",
    ".o1b-grw",
    ".o1b-step",
    ".o1b-areas-col",
    ".o1b-mgr-photo",
    ".o1b-mgr-col",
    ".o1b-awards",
    ".formcard",
    ".o1b-showroom",
    ".o1b-nap",
    ".o1b-wm-faq .elementor-widget-accordion",
    ".o1b-watermark > .e-con-inner > .elementor-widget-heading:not(.elementor-absolute):not(.o1b-ghost)",
    ".o1b-watermark > .e-con-inner > .elementor-widget-text-editor",
    ".o1b-closing .o1b-actions",
    ".o1b-wm-start > .e-con-inner > .o1b-actions"
  ].join(",");

  var revealEls = Array.prototype.slice.call(document.querySelectorAll(sel)).filter(function (el, i, arr) {
    return arr.indexOf(el) === i;
  });

  var showAll = function () {
    revealEls.forEach(function (el) { el.classList.add("is-in"); });
  };

  if (reduced || !("IntersectionObserver" in window)) {
    showAll();
  } else {
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
        el.style.setProperty("--d", (i * 0.09).toFixed(2) + "s");
      });
    });
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-in");
        observer.unobserve(entry.target);
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });
    revealEls.forEach(function (el) { observer.observe(el); });
  }
})();

(function ($) {
  $(function () {
    $(".elementor-location-header .elementor-nav-menu--main").addClass("o1b-pro-nav");
  });
})(jQuery);
JS;
}

function o1b_chrome_css() {
  $stage = function_exists('o1b_media_url') ? o1b_media_url('project-01') : '';
  $css = <<<'CSS'
:root{
  --gold:#b79a61;
  --ink:#202321;
  --ink-2:#111111;
  --heading:#292e2e;
  --body:#383d3e;
  --line:#d9d9d6;
  --tint:#f8f8f6;
  --wrap:1240px;
}
html{scroll-behavior:auto;scroll-padding-top:132px;overflow-x:hidden}
body,body.elementor-page{
  font-family:'Montserrat',Arial,Helvetica,sans-serif;
  font-size:17px;line-height:1.75;color:var(--body);background:#fff;
}
.site-header, header.site-header, .hello-header{display:none!important}
body.elementor-page .page-header,
body.elementor-page .entry-header,
body.elementor-page .page-title{display:none!important}
body.elementor-page #content,
body.elementor-page .site-main,
body.elementor-page .page-content,
body.elementor-page .site-footer{
  margin:0!important;padding:0!important;max-width:none!important;
}
.elementor-location-header,
.ha-template-content-header,
.ekit-template-content-header{
  position:sticky;top:0;z-index:100;
  box-shadow:0 2px 14px rgba(0,0,0,.22);
}
.elementor-location-header .elementor-widget,
.ha-template-content-header .elementor-widget,
.ekit-template-content-header .elementor-widget,
.o1b-hero .elementor-widget,
.o1b-page-hero .elementor-widget,
.o1b-actions .elementor-widget{
  margin:0!important;
}
.elementor-location-header .elementor-widget-container,
.ha-template-content-header .elementor-widget-container,
.ekit-template-content-header .elementor-widget-container{
  padding:0!important;
}
.elementor-location-header .e-con,
.ekit-template-content-header .e-con,
.ha-template-content-header .e-con,
.elementor-location-header .e-con-inner,
.ha-template-content-header .e-con-inner,
.ekit-template-content-header .e-con-inner{
  --padding-top:0px!important;--padding-right:0px!important;
  --padding-bottom:0px!important;--padding-left:0px!important;
}
.o1b-topbar,.o1b-topbar > .e-con-inner{
  --padding-top:9px!important;--padding-bottom:9px!important;
  --padding-left:24px!important;--padding-right:24px!important;
  min-height:40px!important;
}
.o1b-topbar .elementor-widget-text-editor p{
  margin:0!important;font-size:12.5px!important;line-height:1.4!important;
  color:#c9c9c4!important;
}
.o1b-topbar .elementor-element:first-child p{color:var(--gold)!important}
.o1b-topbar a{color:#c9c9c4}
.o1b-topbar a:hover{color:var(--gold)}
.o1b-header-bar,.o1b-header-bar > .e-con-inner{
  min-height:92px!important;height:92px!important;
  flex-wrap:nowrap!important;align-items:center!important;
  justify-content:flex-start!important;
  --padding-top:0px!important;--padding-bottom:0px!important;
  --padding-left:24px!important;--padding-right:24px!important;
  padding-left:24px!important;padding-right:24px!important;
}
.elementor-location-header.o1b-header-ready .o1b-header-bar,
.elementor-location-header.o1b-header-ready .o1b-header-bar > .e-con-inner{
  transition:min-height .3s ease,height .3s ease;
}
.elementor-location-header,
.ha-template-content-header,
.ekit-template-content-header,
.ha-template-content-header .elementor,
.elementor-location-header .elementor{margin:0!important;padding:0!important}
body.elementor-page #content{margin-top:0!important;padding-top:0!important}
.o1b-header-logo{flex:0 0 auto!important;width:auto!important;min-width:150px;padding:0!important}
.elementor-location-header .elementor-widget-theme-site-logo img,
.elementor-location-header .elementor-widget-image img,
.ha-template-content-header .elementor-widget-ha-site-logo img,
.ekit-template-content-header .elementor-widget-ha-site-logo img{
  width:150px!important;max-width:150px!important;height:auto!important;
}
.o1b-header-nav{
  flex:0 0 auto!important;width:auto!important;max-width:none!important;
  margin-left:auto!important;min-width:0;padding:0!important;
}
.o1b-header-nav .elementor-widget-nav-menu,
.o1b-header-nav .elementor-widget-container,
.o1b-header-nav .elementor-nav-menu--main,
.o1b-header-nav .elementor-nav-menu-wrapper{
  width:auto!important;max-width:none!important;margin:0!important;
}
.elementor-location-header .elementor-nav-menu--main .elementor-nav-menu,
.ha-template-content-header .ha-nav-menu ul.menu,
.ekit-template-content-header .ha-nav-menu ul.menu{
  display:flex!important;flex-wrap:nowrap;gap:26px;justify-content:flex-start;
  margin:0;padding:0;width:auto!important;
}
.elementor-location-header .elementor-nav-menu--main .elementor-item,
.ha-template-content-header .ha-nav-menu .menu-item a,
.ekit-template-content-header .ha-nav-menu .menu-item a{
  font-family:'Montserrat',Arial,sans-serif!important;
  font-size:13px!important;font-weight:600!important;letter-spacing:.8px!important;
  text-transform:uppercase!important;color:#fff!important;
  padding:8px 0!important;border-bottom:2px solid transparent;
}
.elementor-location-header .elementor-nav-menu--main .elementor-item:hover,
.elementor-location-header .elementor-nav-menu--main .elementor-item.elementor-item-active,
.ha-template-content-header .ha-nav-menu .menu-item a:hover,
.ekit-template-content-header .ha-nav-menu .menu-item a:hover,
.ha-template-content-header .ha-nav-menu .current-menu-item a,
.ekit-template-content-header .ha-nav-menu .current-menu-item a{
  color:var(--gold)!important;border-bottom-color:var(--gold);
}
.elementor-location-header .elementor-nav-menu--main .menu-item-has-children,
.ha-template-content-header .ha-nav-menu .menu-item-has-children{
  position:relative;
}
.elementor-location-header .elementor-nav-menu--main .sub-menu,
.ha-template-content-header .ha-nav-menu .sub-menu{
  display:none;position:absolute;top:calc(100% + 8px);left:50%;transform:translateX(-50%);
  min-width:280px;margin:0;padding:10px 0;background:#202321;z-index:80;
  box-shadow:0 18px 40px rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);
}
.elementor-location-header .elementor-nav-menu--main .menu-item-has-children:hover > .sub-menu,
.ha-template-content-header .ha-nav-menu .menu-item-has-children:hover > .sub-menu{
  display:block;
}
.elementor-location-header .elementor-nav-menu--main .sub-menu .elementor-item,
.ha-template-content-header .ha-nav-menu .sub-menu a{
  display:block!important;padding:10px 20px!important;white-space:nowrap;font-size:12px!important;
  letter-spacing:.6px!important;border-bottom:none!important;
}
.o1b-cards-7 > .e-con-inner{flex-wrap:wrap!important;align-items:stretch!important}
.o1b-cards-7 > .e-con-inner > .e-con{
  width:calc((100% - 60px) / 3)!important;max-width:calc((100% - 60px) / 3)!important;
}
.o1b-cards-7 > .e-con-inner > .e-con:nth-child(7){margin-left:auto;margin-right:auto}
.elementor .e-con.e-flex.o1b-card-img-empty{
  background:linear-gradient(180deg,#f3f2ed 0%,#e6e4dd 100%);
}
.o1b-phone{width:auto!important;max-width:160px!important;flex:none!important;padding:0!important;margin-left:40px!important}
.o1b-phone .elementor-heading-title{margin:0!important;width:auto!important}
.o1b-phone a:hover .elementor-heading-title{color:var(--gold)!important}
.o1b-phone-label{letter-spacing:1.6px;text-transform:uppercase;color:var(--gold)!important}
.o1b-header-cta{flex:none!important;width:auto!important;padding:0!important;margin-left:0!important}
.o1b-header-cta .elementor-widget-button .elementor-button{
  min-height:46px!important;height:46px!important;padding:12px 22px!important;
  font-size:12px!important;line-height:1.2!important;letter-spacing:.6px!important;
  border-radius:40px!important;background:#fff!important;color:#111!important;
  box-shadow:none!important;
}
.elementor-location-header .elementor-menu-toggle,
.ha-nav-humberger-wrapper{display:none}
.o1b-hero,.o1b-page-hero{background-color:var(--ink);position:relative;overflow:hidden}
.o1b-hero:before,.o1b-page-hero:before{
  content:"";position:absolute;inset:0;z-index:1;pointer-events:none;
  background:linear-gradient(180deg,rgba(0,0,0,.42),rgba(0,0,0,.55));
}
.o1b-hero > .e-con-inner,.o1b-page-hero > .e-con-inner{position:relative;z-index:2}
.elementor .o1b-hero.e-con{
  min-height:708px!important;
  --padding-top:96px!important;--padding-bottom:88px!important;
  padding-block-start:96px!important;padding-block-end:88px!important;
}
.elementor .o1b-hero > .e-con-inner{
  min-height:0!important;
  --padding-top:0px!important;--padding-bottom:0px!important;
  padding-block-start:0!important;padding-block-end:0!important;
}
.elementor .o1b-page-hero.e-con{
  min-height:420px!important;
  --padding-top:72px!important;--padding-bottom:64px!important;
  padding-block-start:72px!important;padding-block-end:64px!important;
}
.elementor .o1b-page-hero > .e-con-inner{
  min-height:0!important;
  --padding-top:0px!important;--padding-bottom:0px!important;
  padding-block-start:0!important;padding-block-end:0!important;
}
.o1b-hero .elementor-heading-title,
.o1b-page-hero .elementor-heading-title{
  text-shadow:0 3px 10px rgba(0,0,0,.55);
}
.o1b-hero h1.elementor-heading-title{
  max-width:1180px;margin:0 auto 28px!important;
  font-size:clamp(1.85rem,4.1vw,3.35rem)!important;line-height:1.12!important;
  white-space:nowrap;
}
.o1b-page-hero h1.elementor-heading-title{
  max-width:920px;margin:0 auto 18px!important;
  font-size:clamp(1.7rem,3.6vw,2.85rem)!important;
}
.o1b-hero .elementor-widget-text-editor,
.o1b-page-hero .elementor-widget-text-editor{
  max-width:740px;margin-left:auto;margin-right:auto;
}
.o1b-hero-sub{margin-bottom:34px!important}
.o1b-hero-sub p{margin:0!important;color:#f0efe9!important;font-size:clamp(1rem,1.6vw,1.19rem)!important;text-shadow:0 2px 6px rgba(0,0,0,.5)}
.o1b-hero-points{margin:40px 0 0!important;max-width:none!important}
.o1b-hero-points p{
  display:flex;flex-wrap:wrap;justify-content:center;margin:0!important;
  color:#e3e0d8!important;font-size:13.5px!important;letter-spacing:.4px;
}
.o1b-hero-points span{padding:0 26px;border-left:1px solid rgba(255,255,255,.28)}
.o1b-hero-points span:first-child{border-left:0}
.o1b-hero-points strong{color:#fff;font-weight:700}
.o1b-hero .elementor-widget-image{margin:0 auto 22px!important;width:132px!important}
body.elementor-page .elementor > .e-con.e-parent > .e-con-inner{
  max-width:1192px!important;width:100%;
}
.o1b-wm-faq > .e-con-inner{max-width:832px!important}
.o1b-watermark h2.elementor-heading-title{
  font-size:clamp(1.6rem,3.2vw,2.5rem)!important;
  margin:0 0 .6em!important;
}
.o1b-sec-intro{max-width:760px;margin:-6px auto 52px!important}
.o1b-sec-intro p{margin:0!important}
.elementor .o1b-wm-about.e-con{
  --padding-top:0px!important;--padding-bottom:0px!important;
  padding-block-start:0!important;padding-block-end:0!important;
}
.elementor .o1b-wm-about > .e-con-inner{
  --padding-top:72px!important;--padding-bottom:72px!important;
  padding-block-start:72px!important;padding-block-end:72px!important;
}
.elementor .e-con.e-flex.o1b-about-grid{align-items:center!important}
.o1b-about-copy,.o1b-about-copy > .e-con-inner{
  --flex-gap:0px!important;gap:0!important;
}
.o1b-wm-about .o1b-about-copy .elementor-widget-heading{margin:0!important}
.o1b-wm-about .o1b-about-copy h2.elementor-heading-title{margin:0 0 12px!important}
.o1b-wm-about .o1b-about-copy .elementor-widget-text-editor p{margin:0 0 1em!important}
.o1b-wm-about .o1b-pullout{margin:22px 0 20px!important}
.o1b-wm-about .o1b-pullout .elementor-widget-text-editor p{margin:0 0 9px!important}
.o1b-wm-about .o1b-actions{margin-top:12px!important}
.o1b-intro-media{
  padding:0 0 46px 46px!important;
  --padding-top:0px!important;--padding-bottom:46px!important;
  --padding-left:46px!important;--padding-right:0px!important;
}
.o1b-watermark .elementor-widget,
.o1b-hero .elementor-widget,
.o1b-page-hero .elementor-widget{margin:0!important}
.o1b-h-rule .elementor-heading-title{position:relative;padding-bottom:26px}
.o1b-h-rule .elementor-heading-title:after{
  content:"";position:absolute;left:50%;bottom:0;width:96px;height:3px;
  background:var(--gold);transform:translateX(-50%);
}
.o1b-actions{
  width:100%;display:flex!important;flex-direction:row!important;flex-wrap:wrap;
  justify-content:center;align-items:center;gap:16px!important;
}
.o1b-actions .elementor-widget-button{width:auto!important;flex:none}
.o1b-actions .elementor-button,
.elementor-widget-button .elementor-button{
  display:inline-flex;align-items:center;justify-content:center;
  min-height:54px;padding:16px 34px!important;
  font-size:13.5px!important;font-weight:600!important;letter-spacing:.6px!important;
  text-transform:uppercase!important;border-radius:4px!important;
  box-shadow:0 3px 10px rgba(0,0,0,.10);
}
.o1b-header-cta .elementor-widget-button .elementor-button{
  min-height:46px!important;padding:12px 22px!important;font-size:12px!important;
  border-radius:40px!important;box-shadow:none!important;
}
.o1b-watermark,
.o1b-watermark > .e-con-inner{
  position:relative;isolation:isolate;overflow:hidden;
}
.o1b-watermark .elementor-absolute.elementor-widget-heading{
  position:absolute!important;top:35px!important;bottom:auto!important;
  left:50%!important;right:auto!important;transform:translateX(-50%)!important;
  width:auto!important;max-width:none!important;margin:0!important;
  z-index:0!important;pointer-events:none;user-select:none;
}
.o1b-watermark-left .elementor-absolute.elementor-widget-heading{
  left:0!important;right:auto!important;transform:none!important;
}
.o1b-watermark .elementor-absolute.elementor-widget-heading .elementor-heading-title{
  margin:0!important;padding:0!important;
  font-size:clamp(4rem,11vw,9.5rem)!important;font-weight:800!important;
  line-height:1!important;letter-spacing:6px!important;
  text-transform:uppercase!important;white-space:nowrap;
  color:var(--ink)!important;opacity:.05;
}
.o1b-dark .elementor-absolute.elementor-widget-heading .elementor-heading-title{
  color:#fff!important;opacity:.055;
}
.o1b-dark a:not(.elementor-button){color:#fff!important}
.o1b-dark a:not(.elementor-button):hover{color:var(--gold)!important}
.formcard a{color:var(--gold)!important}
.o1b-wm-start .elementor-absolute.elementor-widget-heading{top:50px!important}
.o1b-wm-start .elementor-absolute.elementor-widget-heading .elementor-heading-title{
  font-size:clamp(3.2rem,8.5vw,7rem)!important;
}
.o1b-watermark > .e-con-inner > .e-con,
.o1b-watermark > .e-con-inner > .elementor-element:not(.elementor-absolute){
  position:relative;z-index:1;
}
.o1b-badges{background:var(--ink)!important}
.o1b-badges,.o1b-badges > .e-con-inner{
  --padding-top:26px!important;--padding-bottom:26px!important;
}
.o1b-badges-row{
  display:flex!important;flex-wrap:wrap!important;justify-content:center;
  align-items:center;gap:26px 46px!important;
}
.o1b-badges-row .elementor-widget-image{width:auto!important;flex:none}
.o1b-badges img{
  height:52px!important;width:auto!important;object-fit:contain;
  filter:grayscale(1) brightness(0) invert(1);opacity:.82;
}
.o1b-badges img:hover{opacity:1}
.o1b-badge-color img,
.o1b-badges-row .elementor-widget-image:nth-child(4) img,
.o1b-badges-row .elementor-widget-image:nth-child(5) img{
  filter:none!important;height:62px!important;opacity:1;
}
.o1b-cards{margin-top:8px}
.o1b-minis{margin-top:30px}
.elementor .e-con.e-flex.o1b-card,
.elementor .e-con.e-flex.o1b-card-img,
.elementor .e-con.e-flex.o1b-gallery-item,
.elementor .e-con.e-flex.o1b-intro-media,
.elementor .e-con.e-flex.o1b-stage-video{
  --flex-gap:0px!important;gap:0!important;
}
.elementor .e-con.e-flex.o1b-card{
  background:#fff;border:1px solid var(--line);border-radius:4px;
  box-shadow:0 3px 10px rgba(0,0,0,.08);overflow:visible;height:100%;
  transition:box-shadow .35s ease,transform .35s ease;
}
.elementor .e-con.e-flex.o1b-card:hover{
  transform:translateY(-10px);box-shadow:0 30px 54px rgba(32,35,33,.22);
}
.elementor .e-con.e-flex.o1b-card-img{
  position:relative!important;width:100%!important;
  aspect-ratio:4/3!important;height:auto!important;min-height:0!important;
  flex:0 0 auto!important;--flex-grow:0!important;overflow:visible!important;
}
.o1b-card-img .elementor-widget-image{
  position:absolute!important;inset:0;width:100%!important;height:100%!important;
  overflow:hidden;border-radius:4px 4px 0 0;margin:0!important;
}
.o1b-card-img .elementor-widget-image:after{
  content:"";position:absolute;inset:0;pointer-events:none;
  background:linear-gradient(to top,rgba(32,35,33,.55),transparent 46%);opacity:.75;
}
.o1b-card-img .elementor-widget-image img,
.o1b-card-img img{
  width:100%!important;height:100%!important;min-height:0!important;
  object-fit:cover!important;object-position:center 45%;display:block;
}
.o1b-card-img .elementor-widget-heading{
  position:absolute!important;left:22px;bottom:-15px;z-index:3;width:auto!important
}
.o1b-card-img .elementor-heading-title{
  display:inline-block;padding:8px 16px;background:var(--gold);color:#fff!important;
  font-size:10.5px!important;font-weight:700;letter-spacing:1.8px;text-transform:uppercase;
  border-radius:2px;box-shadow:0 8px 18px rgba(32,35,33,.3);
}
.elementor .e-con.e-flex.o1b-card-body{
  flex:1 1 auto!important;--flex-grow:1!important;
  display:flex!important;flex-direction:column!important;
}
.o1b-card-body .elementor-widget-heading .elementor-heading-title{border-bottom:2px solid var(--gold);display:inline-block;padding-bottom:8px}
.o1b-card-body .elementor-widget-text-editor:last-child{
  margin-top:auto;padding:16px 18px;background:var(--tint);border-left:2px solid var(--gold);
}
.o1b-card-body .elementor-widget-text-editor:last-child p{margin:0;font-size:14.5px;line-height:1.65}
.o1b-card-body .elementor-widget-text-editor:last-child strong{
  display:block;margin-bottom:4px;font-size:11px;font-weight:600;letter-spacing:1.8px;
  text-transform:uppercase;color:var(--gold);
}
.o1b-mini{
  background:#fff!important;border:1px solid var(--line)!important;
  border-left:3px solid var(--gold)!important;border-radius:4px;height:100%;
}
.o1b-review{background:#fff;border:1px solid var(--line);border-radius:4px;position:relative;box-shadow:0 12px 28px rgba(32,35,33,.07);height:100%;transition:transform .35s ease,box-shadow .35s ease}
.o1b-review:hover{transform:translateY(-8px);box-shadow:0 26px 46px rgba(32,35,33,.15)}
.o1b-wm-reviews a{color:var(--gold)!important}
.o1b-grw,.o1b-grw .elementor-widget-shortcode{width:100%!important}
.o1b-grw .wp-google-place,.o1b-grw .grw-widget{max-width:100%}
.o1b-review:before{
  content:"\201C";position:absolute;top:-26px;left:24px;
  font-family:Georgia,'Times New Roman',serif;font-size:104px;line-height:1;
  color:var(--gold);opacity:.28;pointer-events:none;
}
.o1b-review-by p{margin:0}
.o1b-pullout{border-left:3px solid var(--gold);margin:12px 0}
.o1b-partner{
  background:#fff;border:1px solid var(--line);border-left:3px solid var(--gold)!important;
  border-radius:4px;box-shadow:0 3px 10px rgba(0,0,0,.10);margin-top:18px;
  transition:transform .4s ease,box-shadow .4s ease;
}
.o1b-partner:hover{transform:translateY(-6px);box-shadow:0 24px 42px rgba(32,35,33,.14)}
.o1b-partner-logos img{max-height:56px;width:auto;max-width:100%;object-fit:contain}
.elementor .e-con.e-flex.o1b-gallery,
.elementor .e-con.e-flex.o1b-gallery > .e-con-inner{
  display:grid!important;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:28px;
}
.o1b-gallery-wide{grid-column:span 2;grid-row:span 2}
.o1b-gallery-item{position:relative;overflow:hidden;border-radius:4px;background:#111;min-height:0;box-shadow:0 14px 30px rgba(0,0,0,.34);transition:transform .4s ease,box-shadow .4s ease}
.o1b-gallery-item:after{
  content:"";position:absolute;inset:14px;pointer-events:none;z-index:3;
  border:1px solid var(--gold);opacity:0;transform:scale(1.04);
  transition:opacity .4s ease,transform .4s ease;
}
.o1b-gallery-item:hover{transform:translateY(-10px);box-shadow:0 30px 54px rgba(0,0,0,.5)}
.o1b-gallery-item:hover img{transform:scale(1.07);opacity:.72}
.o1b-gallery-item:hover:after{opacity:1;transform:scale(1)}
.o1b-gallery-item img{width:100%;height:100%;object-fit:cover;aspect-ratio:4/3;display:block;transition:transform .7s ease,opacity .35s ease}
.o1b-gallery-wide img{aspect-ratio:4/3.05;height:100%}
.o1b-gallery-item .elementor-widget-heading{
  position:absolute!important;left:0;right:0;bottom:0;z-index:2;width:auto!important;margin:0!important;
}
.o1b-gallery-item .elementor-heading-title{
  display:block;padding:30px 22px 16px;color:#fff!important;
  background:linear-gradient(transparent,rgba(0,0,0,.85));
  font-size:13px!important;font-weight:400!important;letter-spacing:.4px;text-transform:none;
}
.o1b-stage-wrap{max-width:1100px;margin:46px auto 0;width:100%}
.o1b-stage{
  position:relative;overflow:hidden;border-radius:4px;background:#0d0f0e;
  aspect-ratio:16/9!important;min-height:0!important;height:auto!important;
  box-shadow:0 30px 60px rgba(32,35,33,.32);
}
.o1b-stage:after{
  content:"";position:absolute;z-index:0;inset:0;
  background:url("/wp-content/uploads/2026/08/o1b-project-01.jpg") center/cover no-repeat;
  filter:blur(26px) brightness(.5) saturate(1.15);transform:scale(1.18);
}
.o1b-stage:before{
  content:"";position:absolute;z-index:1;inset:0;pointer-events:none;
  background:linear-gradient(90deg,rgba(8,10,9,.72),rgba(8,10,9,.15) 32%,rgba(8,10,9,.15) 68%,rgba(8,10,9,.72));
}
.o1b-stage,.o1b-stage > .e-con-inner{
  display:flex!important;align-items:center!important;justify-content:center!important;
  --align-items:center!important;--justify-content:center!important;
  gap:40px!important;--flex-gap:40px!important;
}
.o1b-stage > .e-con,.o1b-stage > .e-con-inner{position:relative;z-index:2;height:100%;min-height:0}
.o1b-stage-side,.o1b-stage-video{position:relative;z-index:3}
.elementor .e-con.o1b-stage-side{
  max-width:320px;color:#fff;flex:1 1 0;--flex-gap:12px!important;
  display:flex!important;flex-direction:column!important;
  justify-content:center!important;--justify-content:center!important;
  align-self:center!important;--align-self:center!important;
}
.o1b-stage-video{flex:none!important;width:auto!important;height:100%!important;align-self:stretch}
.o1b-stage-video .elementor-widget-html{width:auto!important;height:100%!important;background:transparent}
.o1b-stage-video video{
  display:block;width:auto!important;max-width:none!important;height:100%!important;
  aspect-ratio:9/16;object-fit:cover;background:#000;border-radius:2px;
  box-shadow:0 0 0 1px rgba(255,255,255,.14),0 18px 44px rgba(0,0,0,.55);
}
.o1b-stage-points .elementor-widget-text-editor{position:relative;padding-left:28px!important}
.o1b-stage-points .elementor-widget-text-editor p{margin:0}
.o1b-stage-points .elementor-widget-text-editor:before{
  content:"";position:absolute;left:0;top:10px;width:9px;height:9px;background:var(--gold);transform:rotate(45deg);
}
.o1b-wm-process,.o1b-wm-process > .e-con-inner{overflow:visible}
.o1b-steps{position:relative;overflow:visible}
.o1b-steps:before{
  content:"";position:absolute;top:26px;left:12%;right:12%;height:1px;z-index:0;
  background:linear-gradient(90deg,transparent,var(--gold) 14%,var(--gold) 86%,transparent);
}
.o1b-step{
  position:relative;z-index:1;background:#fff;border:1px solid var(--line);border-radius:4px;
  box-shadow:0 12px 28px rgba(32,35,33,.08);flex:1 1 0;
}
.o1b-step > .elementor-widget-heading:first-child,
.o1b-step > .e-con-inner > .elementor-widget-heading:first-child{
  position:absolute!important;top:-26px;left:50%;transform:translateX(-50%);width:auto!important
}
.o1b-step > .elementor-widget-heading:first-child .elementor-heading-title,
.o1b-step > .e-con-inner > .elementor-widget-heading:first-child .elementor-heading-title{
  display:flex;align-items:center;justify-content:center;width:52px;height:52px;
  background:var(--gold);color:#fff!important;font-weight:700;font-size:18px!important;
  border-radius:50%;border:4px solid #fff;box-shadow:0 10px 22px rgba(183,154,97,.5);
}
.elementor .e-con.e-flex.o1b-areas,
.elementor .e-con.e-flex.o1b-areas > .e-con-inner{
  display:grid!important;grid-template-columns:repeat(4,1fr);gap:46px 30px;margin-top:46px;
}
.o1b-areas-col .elementor-widget-text-editor{position:relative;padding-left:25px!important;margin:0 0 12px!important}
.o1b-areas-col .elementor-widget-text-editor p{margin:0;font-size:15px;line-height:1.4}
.o1b-areas-col .elementor-widget-text-editor:before{
  content:"";position:absolute;left:0;top:2px;width:13px;height:17px;
  background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23b79a61'%3E%3Cpath d='M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z'/%3E%3C/svg%3E") no-repeat center/contain;
}
.o1b-wm-areas > .e-con-inner > .elementor-widget-text-editor:last-child{max-width:640px;margin:52px auto 0!important;text-align:center}
.o1b-wm-areas a{color:var(--gold);font-weight:600}
.o1b-pills .elementor-widget-heading{width:auto!important}
.o1b-pills .elementor-heading-title{
  display:inline-block;padding:8px 16px;background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.22);border-radius:40px;
  font-size:11.5px!important;font-weight:600;letter-spacing:1.4px;text-transform:uppercase;color:#e6e3da!important;
}
.o1b-mgr-col{flex:0 0 360px!important;width:360px!important;max-width:360px!important}
.o1b-mgr-photo{position:relative;padding:0 0 28px 28px;width:100%}
.o1b-mgr-rule{
  position:absolute!important;left:0;bottom:0;width:74%!important;height:60%!important;
  border:3px solid var(--gold);border-radius:4px;z-index:1;pointer-events:none;
}
.o1b-mgr-photo .elementor-widget-image{position:relative;z-index:2}
.o1b-mgr-title,.o1b-mgr-title > .e-con-inner{display:flex;flex-wrap:wrap;gap:0 .35em;align-items:baseline}
.o1b-mgr-title .elementor-widget-heading{width:auto!important}
.o1b-mgr-list .elementor-widget-text-editor{position:relative;padding-left:28px!important;margin:0 0 13px!important}
.o1b-mgr-list p{margin:0}
.o1b-mgr-list .elementor-widget-text-editor:before{
  content:"";position:absolute;left:0;top:.45em;width:11px;height:11px;
  border:2px solid var(--gold);border-radius:50%;
}
.o1b-awards{margin-top:66px;border-top:1px solid rgba(255,255,255,.14)}
.o1b-award{
  display:flex;align-items:center;justify-content:center;min-height:120px;padding:16px 12px;
  background:#fff;border-radius:4px;
}
.o1b-award img{display:block;width:auto;height:84px;object-fit:contain}
.o1b-nap{padding-left:16px;border-left:2px solid var(--gold);margin:0 0 18px}
.formcard{background:#fff;color:var(--body);position:relative;box-shadow:0 34px 68px rgba(0,0,0,.42);border-top:3px solid var(--gold)}
.formcard:before{
  content:"";position:absolute;inset:auto -18px -18px auto;width:56%;height:58%;
  border:1px solid rgba(183,154,97,.5);border-radius:4px;z-index:-1;
}
.formcard .elementor-widget-heading .elementor-heading-title{border-bottom:2px solid var(--gold);display:inline-block;padding-bottom:8px}
.o1b-form .field{margin:0 0 16px}
.o1b-form label{
  display:block;margin:0 0 6px;font-size:12px;font-weight:600;
  letter-spacing:1.2px;text-transform:uppercase;color:var(--heading);
}
.o1b-form input,.o1b-form select,.o1b-form textarea{
  width:100%;min-height:50px;padding:13px 14px;border:1px solid var(--line);
  border-radius:4px;font:inherit;font-size:15.5px;background:#fdfcf9;color:var(--ink);
}
.o1b-form textarea{min-height:110px;resize:vertical}
.form__note{margin:4px 0 0;font-size:13px;color:#8a8a84;text-align:center}
.btn{
  display:inline-flex;align-items:center;justify-content:center;text-align:center;
  min-height:54px;padding:16px 34px;font-size:13.5px;font-weight:600;letter-spacing:.6px;
  text-transform:uppercase;border:2px solid transparent;border-radius:4px;cursor:pointer;
  box-shadow:0 3px 10px rgba(0,0,0,.10);
}
.btn--gold{background:var(--gold);border-color:var(--gold);color:#fff}
.btn--gold:hover{background:var(--ink);border-color:var(--ink);color:#fff}
.btn--block{width:100%}
.sticky-cta{display:none;position:fixed;left:0;right:0;bottom:0;z-index:70;box-shadow:0 -3px 16px rgba(0,0,0,.22)}
.sticky-cta a{
  display:flex;align-items:center;justify-content:center;
  min-height:58px;padding:0 8px;background:var(--gold);color:#fff;font-weight:700;
  font-size:12px;text-transform:uppercase;
}
.sticky-cta__alt{background:var(--ink)!important;color:#fff!important}
.elementor .e-con.o1b-intro-media,
.elementor .e-con.o1b-intro-media.e-flex,
.elementor .e-con.o1b-about-media,
.elementor .e-con.o1b-about-media > .e-con-inner,
.elementor .e-con.o1b-intro-media > .e-con-inner{
  position:relative!important;z-index:2;overflow:visible!important;
  isolation:isolate;
}
.elementor .e-con.o1b-gold-rule{
  display:none!important;position:absolute!important;width:0!important;height:0!important;
  min-height:0!important;border:0!important;overflow:hidden!important;pointer-events:none;
}
.o1b-intro-media > .elementor-widget-image:not(.elementor-absolute){
  position:relative!important;z-index:2;width:100%!important;margin:0!important;
}
.o1b-intro-media > .elementor-widget-image:not(.elementor-absolute)::before{
  content:"";position:absolute;z-index:0;pointer-events:none;
  left:-26px;bottom:-26px;width:64%;height:74%;
  border:3px solid var(--gold);border-radius:4px;
}
.o1b-framed img{box-shadow:0 30px 60px rgba(32,35,33,.28)}
.o1b-framed-photo,.o1b-intro-media > .elementor-widget-image:not(.elementor-absolute) img{
  position:relative;z-index:2;
}
.o1b-framed-photo img,
.o1b-intro-media > .elementor-widget-image:not(.elementor-absolute) img{
  width:100%;border-radius:4px;box-shadow:0 30px 60px rgba(32,35,33,.28);display:block;
}
.elementor .e-con.o1b-floatcard{
  position:absolute!important;left:0!important;right:auto!important;top:auto!important;bottom:0!important;
  z-index:4!important;width:auto!important;max-width:calc(100% - 16px);height:auto!important;
  min-height:0!important;flex-grow:0!important;align-self:auto!important;
  --width:auto!important;--container-widget-width:auto!important;
  --container-widget-height:auto!important;--container-widget-flex-grow:0!important;
}
.o1b-floatcard .elementor-icon-wrapper,
.o1b-floatcard .elementor-icon{flex:none}
.o1b-floatchip,
.o1b-intro-media .elementor-absolute.elementor-widget-image,
body:not(.rtl) .o1b-intro-media .elementor-absolute.elementor-widget-image{
  position:absolute!important;right:-16px!important;left:auto!important;top:-30px!important;bottom:auto!important;
  z-index:3!important;width:150px!important;
}
.o1b-floatchip img,
.o1b-intro-media .elementor-absolute.elementor-widget-image img{
  width:150px!important;height:150px!important;object-fit:cover;
  border:6px solid #fff;border-radius:4px;box-shadow:0 20px 40px rgba(32,35,33,.26);
}
.o1b-wm-about > .e-con-inner > .e-con.e-flex{align-items:center!important}
.o1b-stats{background:transparent!important;margin-top:-46px;position:relative;z-index:6}
.o1b-stats,.o1b-stats > .e-con-inner{
  --padding-top:0px!important;--padding-bottom:0px!important;
}
.o1b-stats > .e-con-inner{
  background:var(--gold);border-radius:4px;padding:42px 34px!important;
  box-shadow:0 28px 56px rgba(32,35,33,.26);
}
.o1b-stats .elementor-counter-number-wrapper,
.o1b-stats .elementor-counter-number,
.o1b-stats .elementor-counter-number-prefix,
.o1b-stats .elementor-counter-number-suffix,
.o1b-stats .elementor-counter-title{color:#fff!important}
.o1b-stats .elementor-counter-number-wrapper{
  display:flex;justify-content:center;align-items:baseline;font-size:0;line-height:1;
}
.o1b-stats .elementor-counter-number-wrapper > *{
  font-size:clamp(1.9rem,3.4vw,2.7rem);font-weight:700;line-height:1;
}
.o1b-stats .elementor-counter-title{
  margin-top:4px;font-size:12px;letter-spacing:1.6px;text-transform:uppercase;
}
.o1b-stats .e-con.e-child{position:relative}
.o1b-stats .e-con.e-child + .e-con.e-child:before{
  content:"";position:absolute;left:-12px;top:12%;height:76%;width:1px;background:rgba(255,255,255,.38);
}
.o1b-closing,.o1b-wm-start.o1b-closing{background:var(--tint)!important;border-top:1px solid var(--line)}
.o1b-closing,.o1b-closing > .e-con-inner{
  --padding-top:80px!important;--padding-bottom:80px!important;
}
.o1b-wm-showcase .o1b-actions,.o1b-wm-projects .o1b-actions,.o1b-wm-services .o1b-actions{margin-top:52px!important}
.o1b-showroom img{border-radius:4px;box-shadow:0 18px 40px rgba(32,35,33,.14)}
.elementor-accordion-item .elementor-tab-title{
  padding:20px 54px 20px 24px!important;font-size:15.5px!important;font-weight:600!important;min-height:56px;
}
.elementor-accordion-icon,.elementor-accordion-icon-closed,.elementor-accordion-icon-opened{color:var(--gold)!important}
.elementor-tab-content{font-size:15.5px!important}
.elementor-location-footer a,.ekit-template-content-footer a,.ha-template-content-footer a{color:#a9a9a3}
.elementor-location-footer a:hover,.ekit-template-content-footer a:hover,.ha-template-content-footer a:hover{color:var(--gold)}
.elementor-accordion-item{border-color:var(--line)!important;border-radius:4px;overflow:hidden;margin:0 0 12px}
.o1b-watermark > .e-con-inner > .elementor-widget-text-editor{max-width:760px;margin-left:auto;margin-right:auto}
@media (max-width:1080px){
  .o1b-cards-7 > .e-con-inner > .e-con{
    width:calc((100% - 30px) / 2)!important;max-width:calc((100% - 30px) / 2)!important;
  }
  .elementor-location-header .elementor-nav-menu--main .elementor-nav-menu,
  .ha-template-content-header .ha-nav-menu ul.menu{gap:18px}
  .o1b-phone{margin-left:24px!important}
  .elementor-location-header .elementor-widget-theme-site-logo img{width:132px!important;max-width:132px!important}
}
@media (max-width:900px){
  html{scroll-padding-top:90px}
  .elementor-location-header .elementor-widget-theme-site-logo img,
  .ha-template-content-header .elementor-widget-ha-site-logo img,
  .ekit-template-content-header .elementor-widget-ha-site-logo img{
    width:110px!important;max-width:110px!important;
  }
  .o1b-header-logo{min-width:110px}
  .o1b-header-bar,.o1b-header-bar > .e-con-inner{
    min-height:78px!important;height:78px!important;
    padding-left:18px!important;padding-right:18px!important;
  }
  .o1b-hero,.o1b-hero > .e-con-inner{padding-bottom:128px!important}
  .o1b-header-nav{order:3;flex:0 0 auto!important;width:auto!important;max-width:50px;margin-left:0!important}
  .o1b-header-cta{order:2;margin-left:auto!important}
  .o1b-header-cta .elementor-widget-button{display:none!important}
  .o1b-phone{max-width:none!important;margin-left:0!important}
  .elementor-location-header .elementor-menu-toggle,
  .ha-nav-humberger-wrapper{
    display:flex!important;align-items:center;justify-content:center;
    width:46px;height:46px;border:1px solid rgba(255,255,255,.35);border-radius:4px;
    color:#fff!important;background:transparent!important;
  }
  .elementor-location-header .elementor-menu-toggle i,
  .ha-nav-humberger-wrapper i{color:#fff!important}
  .sticky-cta{display:grid;grid-template-columns:1fr 1fr}
  .o1b-hero h1.elementor-heading-title{font-size:1.75rem!important;white-space:normal}
  .o1b-actions{flex-direction:column!important}
  .o1b-actions .elementor-button{width:100%;max-width:340px}
  .o1b-gold-rule,
  .o1b-intro-media > .elementor-widget-image:not(.elementor-absolute)::before{display:none}
  .o1b-intro-media{
    padding:0 0 34px 0!important;
    --padding-left:0px!important;--padding-bottom:34px!important;
  }
  .elementor .e-con.e-flex.o1b-about-grid{align-items:stretch!important}
  .elementor .e-con.o1b-watermark,
  .o1b-watermark > .e-con-inner{max-width:100%!important;width:100%!important}
  .o1b-watermark > .e-con-inner{--content-width:100%!important}
  .o1b-wm-about > .e-con-inner > .e-con.e-flex,
  .elementor .e-con.e-flex.o1b-about-grid{
    flex-direction:column!important;--flex-direction:column!important;flex-wrap:wrap!important;
  }
  .o1b-wm-about > .e-con-inner > .e-con.e-flex > .e-con,
  .elementor .e-con.e-flex.o1b-about-grid > .e-con,
  .elementor .e-con.e-flex.o1b-about-grid > .e-con-inner > .e-con{
    width:100%!important;--width:100%!important;max-width:100%!important;flex-basis:100%!important;
  }
  .o1b-watermark .elementor-heading-title{white-space:normal!important}
  .o1b-floatchip{right:-10px;top:-18px;width:96px!important}
  .o1b-floatchip img{width:96px!important;height:96px!important;border-width:4px}
  .o1b-floatcard{padding:14px 18px!important}
  .o1b-partner,.o1b-partner > .e-con-inner{flex-direction:column!important;align-items:flex-start}
  .o1b-partner-cat{width:100%!important}
  .elementor .e-con.e-flex.o1b-gallery,
  .elementor .e-con.e-flex.o1b-gallery > .e-con-inner{grid-template-columns:1fr 1fr!important}
  .o1b-gallery-wide{grid-column:span 2;grid-row:auto}
  .o1b-stage{min-height:0!important;aspect-ratio:auto!important;height:auto!important}
  .o1b-stage,.o1b-stage > .e-con-inner{flex-direction:column!important;gap:26px!important}
  .o1b-stage-side{max-width:100%;flex:none}
  .o1b-stage-video{height:auto!important}
  .o1b-stage-video .elementor-wrapper,.o1b-stage-video video{max-width:250px!important;height:auto!important;width:100%!important}
  .o1b-mgr-col{flex:1 1 100%!important;width:100%!important;max-width:320px!important;margin:0 auto}
  .o1b-steps:before{display:none}
  .o1b-steps > .e-con-inner{flex-wrap:wrap!important}
  .o1b-step{flex:1 1 46%}
  .elementor .e-con.e-flex.o1b-areas,
  .elementor .e-con.e-flex.o1b-areas > .e-con-inner{grid-template-columns:1fr 1fr!important;gap:28px 20px}
  .o1b-mgr-photo{padding:0 0 18px 18px}
}
@media (max-width:780px){
  .o1b-cards-7 > .e-con-inner > .e-con{
    width:100%!important;max-width:100%!important;margin-left:0;margin-right:0;
  }
  .o1b-topbar,.o1b-topbar > .e-con-inner{
    --padding-top:7px!important;--padding-bottom:7px!important;
    min-height:0!important;
  }
  .o1b-topbar .elementor-widget-text-editor p{font-size:11.5px!important}
}
@media (max-width:520px){
  .o1b-phone-label,
  .o1b-phone .elementor-widget-heading:first-child{display:none!important}
  .o1b-phone .elementor-heading-title{font-size:14.5px!important}
  .o1b-badges img{height:38px!important}
  .o1b-badge-color img{height:46px!important}
  .o1b-badges-row{gap:20px 26px!important}
  .elementor .e-con.e-flex.o1b-gallery,
  .elementor .e-con.e-flex.o1b-gallery > .e-con-inner{grid-template-columns:1fr!important}
  .o1b-gallery-wide{grid-column:auto}
  .o1b-step{flex:1 1 100%}
  .elementor .e-con.e-flex.o1b-areas,
  .elementor .e-con.e-flex.o1b-areas > .e-con-inner{grid-template-columns:1fr!important}
}
@media (min-width:901px){
  .sticky-cta{display:none!important}
  .elementor-location-header .elementor-menu-toggle,
  .ha-nav-humberger-wrapper{display:none!important}
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
  text-transform:uppercase;letter-spacing:.4px;color:var(--heading);
}
body.blog article:not(.elementor-post),
body.archive article:not(.elementor-post){
  background:#fff;border:1px solid var(--line);border-left:3px solid var(--gold);
  border-radius:4px;padding:28px 28px 24px;margin:0 0 22px;
  box-shadow:0 3px 10px rgba(0,0,0,.08);
}
body.blog article:not(.elementor-post) .entry-title,
body.archive article:not(.elementor-post) .entry-title{
  text-align:left;text-transform:none;font-size:1.35rem;margin:0 0 8px;
}
body.blog article .entry-title a,
body.archive article .entry-title a,
body.single-post a{color:var(--heading)}
body.blog article .entry-title a:hover,
body.archive article .entry-title a:hover,
body.single-post a:hover{color:var(--gold)}
body.blog .posted-on,
body.archive .posted-on,
body.single-post .posted-on{color:#8a8a84;font-size:13px}
.o1b-hero-zoom{
  position:absolute;inset:0;z-index:0;pointer-events:none;
  background-position:center;background-size:cover;background-repeat:no-repeat;
  transform:scale(1.14);
}
.js-anim .o1b-hero-zoom{animation:heroZoom 14s cubic-bezier(.16,.62,.28,1) forwards}
@keyframes heroZoom{to{transform:scale(1)}}
@keyframes riseIn{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1;transform:none}
}
@keyframes lineGrow{
  from{width:0;opacity:0}
  to{width:96px;opacity:1}
}
@keyframes floaty{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-14px)}
}
.o1b-floatcard{animation:floaty 6s ease-in-out infinite}
.o1b-floatchip{animation:floaty 7.5s ease-in-out infinite reverse}
.js-anim .o1b-hero .elementor-widget-image,
.js-anim .o1b-hero h1.elementor-heading-title,
.js-anim .o1b-hero .o1b-hero-sub,
.js-anim .o1b-hero .o1b-actions,
.js-anim .o1b-hero .o1b-hero-points,
.js-anim .o1b-page-hero h1.elementor-heading-title,
.js-anim .o1b-page-hero .elementor-widget-text-editor,
.js-anim .o1b-page-hero .o1b-actions{
  opacity:0;
  animation:riseIn .72s cubic-bezier(.22,.61,.36,1) forwards;
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
  animation:lineGrow .7s cubic-bezier(.22,.61,.36,1) .62s backwards;
}
.js-anim .o1b-about-copy > .e-con-inner > .elementor-widget,
.js-anim .o1b-about-copy > .e-con-inner > .o1b-pullout,
.js-anim .o1b-about-copy > .e-con-inner > .o1b-actions,
.js-anim .o1b-intro-media,
.js-anim .o1b-stats,
.js-anim .o1b-sec-intro,
.js-anim .o1b-card,
.js-anim .o1b-mini,
.js-anim .o1b-partner,
.js-anim .o1b-gallery-item,
.js-anim .o1b-stage-wrap,
.js-anim .o1b-grw,
.js-anim .o1b-step,
.js-anim .o1b-areas-col,
.js-anim .o1b-mgr-photo,
.js-anim .o1b-mgr-col,
.js-anim .o1b-awards,
.js-anim .formcard,
.js-anim .o1b-showroom,
.js-anim .o1b-nap,
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
.js-anim .o1b-intro-media,
.js-anim .o1b-gallery-item,
.js-anim .o1b-stage-wrap,
.js-anim .o1b-mgr-photo,
.js-anim .formcard,
.js-anim .o1b-grw,
.js-anim .o1b-showroom{
  transform:translateY(44px) scale(.97);
}
.js-anim .is-in{
  opacity:1!important;transform:none!important;
}
.elementor-location-header.is-stuck .o1b-header-bar,
.elementor-location-header.is-stuck .o1b-header-bar > .e-con-inner{
  min-height:70px!important;height:70px!important;
}
.elementor-location-header.is-stuck .elementor-widget-theme-site-logo img{
  width:124px!important;max-width:124px!important;transition:width .3s ease,max-width .3s ease;
}
@media (prefers-reduced-motion:reduce){
  *{animation-duration:.01ms!important;animation-delay:0ms!important;transition-duration:.01ms!important;transition-delay:0ms!important;scroll-behavior:auto!important}
  .js-anim .o1b-about-copy > .e-con-inner > .elementor-widget,
  .js-anim .o1b-about-copy > .e-con-inner > .o1b-pullout,
  .js-anim .o1b-about-copy > .e-con-inner > .o1b-actions,
  .js-anim .o1b-intro-media,.js-anim .o1b-stats,.js-anim .o1b-sec-intro,
  .js-anim .o1b-card,.js-anim .o1b-mini,.js-anim .o1b-partner,
  .js-anim .o1b-gallery-item,.js-anim .o1b-stage-wrap,.js-anim .o1b-grw,
  .js-anim .o1b-step,.js-anim .o1b-areas-col,.js-anim .o1b-mgr-photo,
  .js-anim .o1b-mgr-col,.js-anim .o1b-awards,.js-anim .formcard,
  .js-anim .o1b-showroom,.js-anim .o1b-nap,
  .js-anim .o1b-wm-faq .elementor-widget-accordion,
  .js-anim .o1b-watermark > .e-con-inner > .elementor-widget-heading,
  .js-anim .o1b-watermark > .e-con-inner > .elementor-widget-text-editor,
  .js-anim .o1b-closing .o1b-actions,
  .js-anim .o1b-wm-start > .e-con-inner > .o1b-actions,
  .js-anim .o1b-hero .elementor-widget-image,
  .js-anim .o1b-hero h1.elementor-heading-title,
  .js-anim .o1b-hero .o1b-hero-sub,
  .js-anim .o1b-hero .o1b-actions,
  .js-anim .o1b-hero .o1b-hero-points,
  .js-anim .o1b-page-hero h1.elementor-heading-title,
  .js-anim .o1b-page-hero .elementor-widget-text-editor,
  .js-anim .o1b-page-hero .o1b-actions{
    opacity:1!important;transform:none!important;
  }
  .o1b-hero-zoom{transform:none!important;animation:none!important}
  .o1b-floatcard,.o1b-floatchip{animation:none!important}
}
CSS;
  if ($stage) {
    $css = str_replace(
      'url("/wp-content/uploads/2026/08/o1b-project-01.jpg")',
      'url("' . esc_url($stage) . '")',
      $css
    );
  }
  return $css;
}
