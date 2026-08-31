<?php
/**
 * Elementor 4 nested-container layout fix.
 * Nested containers have no .e-con-inner; widget css_classes often do not render.
 */
if (!defined('ABSPATH')) {
  exit;
}

add_action('wp_enqueue_scripts', function () {
  wp_add_inline_style('o1b-chrome', o1b_el4_css());
}, 50);

function o1b_el4_css() {
  return <<<'CSS'
.o1b-cards-7.e-con,
.o1b-cards-7 > .e-con-inner{flex-wrap:wrap!important;align-items:stretch!important}
.o1b-cards-7.e-con > .e-con,
.o1b-cards-7 > .e-con-inner > .e-con{
  --width:calc((100% - 60px) / 3)!important;
  width:calc((100% - 60px) / 3)!important;
  max-width:calc((100% - 60px) / 3)!important;
  flex:0 0 calc((100% - 60px) / 3)!important;
  --flex-grow:0!important;
}
.o1b-cards-7.e-con > .e-con:nth-child(7),
.o1b-cards-7 > .e-con-inner > .e-con:nth-child(7){margin-left:0;margin-right:auto}
.o1b-highlights.e-con > .e-con,
.o1b-highlights > .e-con-inner > .e-con{
  --width:100%!important;width:100%!important;max-width:none!important;min-width:0!important;
  flex:none!important;
}
.o1b-hero-points,
.o1b-hero .elementor-widget-text-editor:has(p > span + span),
.o1b-page-hero .elementor-widget-text-editor:has(p > span + span){
  margin:40px 0 0!important;max-width:none!important
}
.o1b-hero-points p,
.o1b-hero .elementor-widget-text-editor p:has(> span + span),
.o1b-page-hero .elementor-widget-text-editor p:has(> span + span){
  display:flex;flex-wrap:wrap;justify-content:center;margin:0!important;
  color:#e3e0d8!important;font-size:13.5px!important;letter-spacing:.4px;
}
.o1b-hero-points span,
.o1b-hero .elementor-widget-text-editor p:has(> span + span) span,
.o1b-page-hero .elementor-widget-text-editor p:has(> span + span) span{
  padding:0 26px;border-left:1px solid rgba(255,255,255,.28)
}
.o1b-hero-points span:first-child,
.o1b-hero .elementor-widget-text-editor p:has(> span + span) span:first-child,
.o1b-page-hero .elementor-widget-text-editor p:has(> span + span) span:first-child{border-left:0}
.o1b-hero-points strong,
.o1b-hero .elementor-widget-text-editor p:has(> span + span) strong,
.o1b-page-hero .elementor-widget-text-editor p:has(> span + span) strong{color:#fff;font-weight:700}
@media (max-width:1080px){
  .o1b-cards-7.e-con > .e-con,
  .o1b-cards-7 > .e-con-inner > .e-con{
    --width:calc((100% - 30px) / 2)!important;
    width:calc((100% - 30px) / 2)!important;
    max-width:calc((100% - 30px) / 2)!important;
    flex:0 0 calc((100% - 30px) / 2)!important;
  }
}
@media (max-width:900px){
  .o1b-hero-points p,
  .o1b-hero .elementor-widget-text-editor p:has(> span + span),
  .o1b-page-hero .elementor-widget-text-editor p:has(> span + span){flex-direction:column;gap:9px}
  .o1b-hero-points span,
  .o1b-hero .elementor-widget-text-editor p:has(> span + span) span,
  .o1b-page-hero .elementor-widget-text-editor p:has(> span + span) span{padding:0;border-left:0}
}
@media (max-width:780px){
  .elementor .e-con.e-flex.o1b-highlights,
  .elementor .e-con.e-flex.o1b-highlights > .e-con-inner{
    grid-template-columns:1fr!important;
  }
  .o1b-cards-7.e-con > .e-con,
  .o1b-cards-7 > .e-con-inner > .e-con{
    --width:100%!important;width:100%!important;max-width:100%!important;
    flex:0 0 100%!important;margin-left:0;margin-right:0;
  }
}
.o1b-watermark-left.o1b-wm-services > .e-con-inner > .elementor-widget-text-editor{
  max-width:880px!important;width:100%!important;
  margin-left:auto!important;margin-right:auto!important;
}
.o1b-watermark-left.o1b-wm-services > .e-con-inner > .elementor-widget-heading:not(.elementor-absolute) p.elementor-heading-title{
  margin:0 0 12px!important;
}
.o1b-watermark-left.o1b-wm-services > .e-con-inner > .elementor-widget-heading:not(.elementor-absolute) h2.elementor-heading-title{
  margin:0 0 28px!important;
}
.o1b-watermark-left.o1b-wm-services > .e-con-inner > .elementor-widget-image{
  max-width:880px!important;width:100%!important;
  margin:28px auto 48px!important;
}
.elementor .e-con.e-flex.o1b-highlights,
.elementor .e-con.e-flex.o1b-highlights > .e-con-inner{margin-top:28px!important}
.o1b-mini{overflow:visible}
.o1b-mini .elementor-heading-title{margin:0 0 10px!important;color:#292e2e!important}
.o1b-mini .elementor-widget-text-editor,
.o1b-mini .elementor-widget-text-editor p{margin:0!important;color:#383d3e!important}
@media (max-width:900px){
  body{padding-bottom:calc(58px + env(safe-area-inset-bottom,0px))}
}
.o1b-service-media--frame,
.o1b-svc-frame .o1b-wm-services > .e-con-inner > .elementor-widget-image,
body.page-artificial-grass-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image,
body.page-paver-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image,
body.page-stepping-stones-pathways .o1b-wm-services > .e-con-inner > .elementor-widget-image{
  aspect-ratio:4/3!important;overflow:hidden!important;border-radius:4px;
  width:100%!important;max-width:880px!important;
  margin:28px auto 48px!important;
}
.o1b-service-media--frame .elementor-widget-container,
.o1b-service-media--frame figure,
body.page-artificial-grass-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image .elementor-widget-container,
body.page-paver-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image .elementor-widget-container,
body.page-stepping-stones-pathways .o1b-wm-services > .e-con-inner > .elementor-widget-image .elementor-widget-container,
body.page-artificial-grass-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image figure,
body.page-paver-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image figure,
body.page-stepping-stones-pathways .o1b-wm-services > .e-con-inner > .elementor-widget-image figure{
  height:100%!important;width:100%!important;overflow:hidden!important;
}
.o1b-service-media--frame img,
body.page-artificial-grass-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image img,
body.page-paver-installation .o1b-wm-services > .e-con-inner > .elementor-widget-image img,
body.page-stepping-stones-pathways .o1b-wm-services > .e-con-inner > .elementor-widget-image img{
  width:100%!important;height:100%!important;max-height:none!important;
  object-fit:cover!important;object-position:center!important;
}
.elementor-location-header .elementor-nav-menu--main .sub-menu .elementor-item,
.elementor-location-header .elementor-nav-menu--main .sub-menu a{
  color:#fff!important;background:transparent!important;border-bottom:none!important;
  font-size:12px!important;letter-spacing:.6px!important;padding:10px 20px!important;
  white-space:nowrap;text-transform:none!important;
}
.elementor-location-header .elementor-nav-menu--main .sub-menu .elementor-item:hover,
.elementor-location-header .elementor-nav-menu--main .sub-menu .elementor-item.elementor-item-active,
.elementor-location-header .elementor-nav-menu--main .sub-menu a:hover{
  color:#fff!important;background:rgba(183,154,97,.14)!important;border-bottom:none!important;
}
@media (max-width:900px){
  .o1b-header-nav{overflow:visible!important;position:static!important}
  .o1b-header-nav .elementor-widget-nav-menu,
  .o1b-header-nav .elementor-widget-container,
  .o1b-header-nav .elementor-nav-menu-wrapper,
  .elementor-location-header .elementor-widget-nav-menu{
    position:static!important;overflow:visible!important;max-width:none!important;
  }
  .o1b-header-bar,.o1b-header-bar > .e-con-inner{overflow:visible!important;position:relative!important}
  .elementor-location-header .elementor-nav-menu--dropdown,
  .elementor-location-header .elementor-nav-menu--dropdown.elementor-nav-menu__container{
    position:absolute!important;left:0!important;right:0!important;top:100%!important;
    width:auto!important;max-width:none!important;min-width:0!important;
    margin:0!important;padding:8px 24px 20px!important;
    background:#202321!important;z-index:90!important;
    border-top:1px solid rgba(255,255,255,.12)!important;
    box-shadow:0 18px 40px rgba(0,0,0,.4)!important;
    max-height:min(80vh,760px)!important;overflow-x:hidden!important;overflow-y:auto!important;
  }
  .elementor-location-header .elementor-nav-menu--dropdown .elementor-item,
  .elementor-location-header .elementor-nav-menu--dropdown a{
    display:block!important;width:100%!important;padding:14px 0!important;
    color:#fff!important;background:transparent!important;white-space:normal!important;
    border-bottom:none!important;
  }
  .elementor-location-header .elementor-nav-menu--dropdown .sub-menu{
    position:static!important;transform:none!important;
    width:100%!important;min-width:0!important;max-width:none!important;
    padding:0 0 10px 12px!important;background:transparent!important;
    box-shadow:none!important;border:none!important;
  }
  .elementor-location-header .elementor-nav-menu--dropdown .sub-menu .elementor-item,
  .elementor-location-header .elementor-nav-menu--dropdown .sub-menu a{
    padding:10px 0!important;font-size:12px!important;white-space:normal!important;
    color:#fff!important;text-transform:none!important;
  }
}
a.o1b-card,a.e-con.o1b-card,.elementor a.e-con.e-flex.o1b-card{
  color:inherit;text-decoration:none;cursor:pointer;
}
CSS;
}
