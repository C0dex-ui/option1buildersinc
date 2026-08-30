<?php
/**
 * Theme Builder archive + single post templates, plus blog article CSS.
 */
if (!defined('ABSPATH')) {
  exit;
}

add_action('wp_enqueue_scripts', function () {
  wp_register_style('o1b-tb', false, [], '1.2.0');
  wp_enqueue_style('o1b-tb');
  wp_add_inline_style('o1b-tb', o1b_tb_blog_css());
}, 60);

function o1b_tb_blog_css() {
  return <<<'CSS'
body.blog #content,
body.blog .site-main,
body.archive #content,
body.archive .site-main,
body.single-post #content,
body.single-post .site-main,
body.blog .elementor-location-archive,
body.archive .elementor-location-archive,
body.single-post .elementor-location-single{
  margin:0!important;padding:0!important;max-width:none!important;
}
body.blog .page-header,
body.archive .page-header,
body.single-post .entry-header,
body.blog .page-title,
body.archive .page-title{display:none!important}
.elementor-location-archive .elementor-widget-archive-posts .elementor-posts-container,
.elementor-location-archive .elementor-widget-archive-posts .elementor-grid{
  display:grid!important;grid-template-columns:1fr!important;gap:28px!important;
}
.elementor-location-archive article.elementor-post{
  display:grid!important;grid-template-columns:280px minmax(0,1fr)!important;
  align-items:center!important;gap:36px!important;
  background:#fff!important;border:1px solid #d9d9d6!important;
  border-radius:4px!important;padding:0!important;margin:0!important;
  width:100%!important;height:auto!important;min-height:220px;
  box-shadow:0 3px 10px rgba(0,0,0,.08)!important;overflow:hidden;
}
.elementor-location-archive .o1b-archive-posts .elementor-grid-item,
.elementor-location-archive .o1b-archive-posts .elementor-post{
  display:flex!important;flex-direction:column!important;height:100%;
  transition:transform .35s ease,box-shadow .35s ease;
}
.elementor-location-archive .o1b-archive-posts .elementor-post:hover{
  transform:translateY(-6px);box-shadow:0 22px 40px rgba(32,35,33,.14)!important;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__thumbnail__link,
.elementor-location-archive .o1b-archive-posts .elementor-post__thumbnail{
  display:block;margin:0!important;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__thumbnail img{
  width:100%;height:168px;object-fit:cover;display:block;
}
.elementor-location-archive .o1b-archive-posts .elementor-post:not(:has(.elementor-post__thumbnail img))::before{
  content:"";display:block;height:168px;
  background:#202321 url(https://option1buildersinc.com/wp-content/uploads/o1b-src/o1b-hero.webp) center/cover;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__text{
  padding:16px 18px 18px!important;display:flex;flex-direction:column;flex:1;gap:0;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__title{
  margin:0 0 8px!important;font-size:16px!important;line-height:1.3!important;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__title a{
  color:#292e2e!important;text-decoration:none;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__title a:hover{color:#b79a61!important}
.elementor-location-archive .o1b-archive-posts .elementor-post__meta-data{
  margin:0 0 10px!important;color:#b79a61!important;font-size:11px!important;
  letter-spacing:1.4px;text-transform:uppercase;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__excerpt{
  margin:0 0 14px!important;color:#383d3e;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__excerpt p{
  margin:0!important;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__excerpt:empty,
.elementor-location-archive .o1b-archive-posts .elementor-post__meta-data:empty{display:none}
.elementor-location-archive .o1b-archive-posts .elementor-post__read-more{
  display:inline-block!important;margin-top:auto!important;align-self:flex-start;
  background:#202321;color:#fff!important;padding:10px 16px;border-radius:4px;
  text-decoration:none!important;font-size:12px!important;letter-spacing:.5px;text-transform:uppercase;
}
.elementor-location-archive .o1b-archive-posts .elementor-post__read-more:hover{
  background:#b79a61;color:#fff!important;
}
.elementor-location-archive .elementor-pagination{margin-top:36px}
.elementor-location-archive .elementor-pagination .page-numbers.current,
.elementor-location-archive .elementor-pagination .page-numbers:hover{color:#b79a61}
.elementor-location-single .o1b-post-article{
  max-width:820px;margin-left:auto;margin-right:auto;
}
.elementor-location-single .o1b-post-photo{margin:0 0 28px}
.elementor-location-single .o1b-post-photo:not(:has(img)){display:none}
.elementor-location-single .o1b-post-photo img{
  width:100%;height:auto;max-height:460px;object-fit:cover;display:block;border-radius:4px;
}
.elementor-location-single .o1b-post-meta{
  margin:0 0 22px;justify-content:center;
}
.elementor-location-single .o1b-post-body{max-width:760px;margin:0 auto}
.elementor-location-single .o1b-post-body p{margin:0 0 1.15em}
.elementor-location-single .o1b-post-body p:first-child{font-size:1.08em}
.elementor-location-single .o1b-post-body h2,
.elementor-location-single .o1b-post-body h3{
  font-family:Montserrat,Arial,sans-serif;color:#292e2e;
  text-transform:uppercase;letter-spacing:.4px;margin:1.6em 0 .6em;
}
.elementor-location-single .o1b-post-body ul,
.elementor-location-single .o1b-post-body ol{margin:0 0 1.2em;padding-left:1.2em}
.elementor-location-single .o1b-post-body li{margin:0 0 .45em}
.elementor-location-single .o1b-post-body blockquote{
  margin:1.4em 0;padding:8px 0 8px 20px;border-left:3px solid #b79a61;color:#555a59;
}
.elementor-location-single .o1b-post-body img,
.elementor-location-single .o1b-post-body figure{
  max-width:100%;height:auto;border-radius:4px;margin:1.2em 0;
}
.elementor-location-single .o1b-post-body a{color:#202321}
.elementor-location-single .o1b-post-body a:hover{color:#b79a61}
.elementor-location-single .elementor-widget-theme-post-excerpt:empty{display:none}
@media (max-width:1024px){
  .elementor-location-archive .o1b-archive-posts{--grid-columns:2}
  .elementor-location-archive .o1b-archive-posts .elementor-posts-container,
  .elementor-location-archive .o1b-archive-posts .elementor-grid{
    grid-template-columns:repeat(2,minmax(0,1fr))!important;
  }
}
@media (max-width:767px){
  .elementor-location-archive .o1b-archive-posts{--grid-columns:1}
  .elementor-location-archive .o1b-archive-posts .elementor-posts-container,
  .elementor-location-archive .o1b-archive-posts .elementor-grid{
    grid-template-columns:1fr!important;
  }
  .elementor-location-archive .o1b-archive-posts .elementor-post__thumbnail img,
  .elementor-location-archive .o1b-archive-posts .elementor-post:not(:has(.elementor-post__thumbnail img))::before{
    height:200px;
  }
}
CSS;
}

if (!function_exists('o1b_save_pro_theme')) {
  function o1b_save_pro_theme($type, $title, $elements, $conditions, $preview = []) {
    $documents = \Elementor\Plugin::$instance->documents;
    $found = get_posts([
      'post_type' => 'elementor_library',
      'title' => $title,
      'numberposts' => 1,
      'post_status' => 'any',
    ]);
    if ($found) {
      $id = (int) $found[0]->ID;
      wp_update_post(['ID' => $id, 'post_status' => 'publish']);
      $doc = $documents->get($id);
    } else {
      $doc = $documents->create($type, [
        'post_title' => $title,
        'post_status' => 'publish',
      ]);
      $id = ($doc && !is_wp_error($doc)) ? (int) $doc->get_main_id() : 0;
    }
    if (!$id) {
      return new WP_Error('create', $title);
    }
    wp_set_object_terms($id, $type, 'elementor_library_type');
    o1b_save_el($id, $elements, $type);
    update_post_meta($id, '_elementor_page_settings', array_merge(['hide_title' => 'yes'], $preview));
    $cm = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager();
    $saved = $cm->save_conditions($id, $conditions);
    if (!$saved) {
      $flat = [];
      foreach ($conditions as $row) {
        $flat[] = rtrim(implode('/', $row), '/');
      }
      update_post_meta($id, '_elementor_conditions', $flat);
      $location = ($type === 'archive') ? 'archive' : 'single';
      $all = get_option('elementor_pro_theme_builder_conditions', []);
      if (!is_array($all)) {
        $all = [];
      }
      if (!isset($all[$location]) || !is_array($all[$location])) {
        $all[$location] = [];
      }
      foreach (array_keys($all[$location]) as $other) {
        if ((int) $other !== $id) {
          unset($all[$location][$other]);
        }
      }
      $all[$location][(string) $id] = $flat;
      update_option('elementor_pro_theme_builder_conditions', $all);
      $cm->get_cache()->regenerate();
    }
    return $id;
  }
}

if (!function_exists('o1b_import_blog_templates')) {
  function o1b_import_blog_templates() {
    $out = [];
    $archive = o1b_read_template('blog-archive');
    $single = o1b_read_template('blog-single');
    if (is_wp_error($archive)) {
      $out['archive'] = $archive->get_error_message();
    } else {
      $out['archive'] = o1b_save_pro_theme(
        'archive',
        'Option 1 Blog Archive',
        $archive,
        [['include', 'archive']],
        ['preview_type' => 'archive/recent_posts']
      );
    }
    if (is_wp_error($single)) {
      $out['single'] = $single->get_error_message();
    } else {
      $latest = get_posts(['post_type' => 'post', 'posts_per_page' => 1, 'post_status' => 'publish']);
      $preview = ['preview_type' => 'single/post'];
      if ($latest) {
        $preview['preview_id'] = (int) $latest[0]->ID;
      }
      $out['single'] = o1b_save_pro_theme(
        'single-post',
        'Option 1 Single Post',
        $single,
        [['include', 'singular', 'post']],
        $preview
      );
    }
    if (function_exists('o1b_purge_live_caches')) {
      o1b_purge_live_caches();
    }
    return $out;
  }
}
