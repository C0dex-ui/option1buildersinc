<?php
/**
 * Import Elementor JSON and Elementor Pro header/footer. Call o1b_import_all().
 */
if (!defined('ABSPATH')) {
  exit;
}

function o1b_resolve_placeholders($data) {
  if (is_array($data)) {
    foreach ($data as $k => $v) {
      $data[$k] = o1b_resolve_placeholders($v);
    }
    return $data;
  }
  if (!is_string($data)) {
    return $data;
  }
  if (preg_match('/^\{\{media:([a-z0-9-]+)\}\}$/', $data, $m)) {
    return o1b_media($m[1]);
  }
  if (preg_match('/^\{\{media_url:([a-z0-9-]+)\}\}$/', $data, $m)) {
    return o1b_media_url($m[1]);
  }
  if (strpos($data, '{{media_url:') !== false) {
    return preg_replace_callback('/\{\{media_url:([a-z0-9-]+)\}\}/', function ($m) {
      return o1b_media_url($m[1]);
    }, $data);
  }
  return $data;
}

function o1b_save_el($post_id, $elements, $type = 'wp-page') {
  update_post_meta($post_id, '_elementor_edit_mode', 'builder');
  update_post_meta($post_id, '_elementor_template_type', $type);
  update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($elements)));
  update_post_meta($post_id, '_elementor_version', defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '3.0.0');
  update_post_meta($post_id, '_wp_page_template', 'elementor_header_footer');
  update_post_meta($post_id, '_elementor_page_settings', ['hide_title' => 'yes']);
}

function o1b_pro_theme_conditions($id, $location) {
  $all = get_option('elementor_pro_theme_builder_conditions', []);
  if (!is_array($all)) {
    $all = [];
  }
  if (!isset($all[$location]) || !is_array($all[$location])) {
    $all[$location] = [];
  }
  foreach (array_keys($all[$location]) as $other) {
    if ((int) $other !== (int) $id) {
      unset($all[$location][$other]);
    }
  }
  $all[$location][(string) $id] = ['include/general'];
  update_option('elementor_pro_theme_builder_conditions', $all);
}

function o1b_save_pro_template($type, $title, $elements) {
  $found = get_posts([
    'post_type' => 'elementor_library',
    'title' => $title,
    'numberposts' => 1,
    'post_status' => 'any',
  ]);
  $id = $found ? (int) $found[0]->ID : (int) wp_insert_post([
    'post_title' => $title,
    'post_status' => 'publish',
    'post_type' => 'elementor_library',
  ]);
  wp_update_post(['ID' => $id, 'post_status' => 'publish']);
  wp_set_object_terms($id, $type, 'elementor_library_type');
  o1b_save_el($id, $elements, $type);
  update_post_meta($id, '_elementor_conditions', ['include/general']);
  o1b_pro_theme_conditions($id, $type);
  return $id;
}

function o1b_read_template($name) {
  $path = WP_CONTENT_DIR . '/uploads/o1b-elementor/' . $name . '.json';
  if (!file_exists($path)) {
    return new WP_Error('missing', $path);
  }
  $json = json_decode(file_get_contents($path), true);
  if (!is_array($json) || empty($json['content'])) {
    return new WP_Error('invalid', $name);
  }
  return o1b_resolve_placeholders($json['content']);
}

function o1b_purge_live_caches() {
  if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
  }
  if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
  }
  if (class_exists('\LiteSpeed\Purge')) {
    \LiteSpeed\Purge::purge_all();
  }
  do_action('litespeed_purge_all');
}

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
    $id = $doc ? (int) $doc->get_main_id() : 0;
  }
  if (!$id) {
    return new WP_Error('create', $title);
  }
  wp_set_object_terms($id, $type, 'elementor_library_type');
  o1b_save_el($id, $elements, $type);
  $settings = array_merge(['hide_title' => 'yes'], $preview);
  update_post_meta($id, '_elementor_page_settings', $settings);
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
  o1b_purge_live_caches();
  return $out;
}

function o1b_import_all() {
  $out = [];
  $pages = [
    'home' => 'home',
    'about-us' => 'about-us',
    'services' => 'services',
    'artificial-grass-installation' => 'artificial-grass-installation',
    'paver-installation' => 'paver-installation',
    'landscape-design-installation' => 'landscape-design-installation',
    'stepping-stones-pathways' => 'stepping-stones-pathways',
    'concrete-dg-gravel' => 'concrete-dg-gravel',
    'irrigation-drainage' => 'irrigation-drainage',
    'vinyl-fencing' => 'vinyl-fencing',
    'projects' => 'projects',
    'contact-us' => 'contact-us',
  ];
  foreach ($pages as $file => $key) {
    $elements = o1b_read_template($file);
    if (is_wp_error($elements)) {
      $out[$file] = $elements->get_error_message();
      continue;
    }
    $id = o1b_page_id($key);
    if (!$id) {
      $out[$file] = 'missing page';
      continue;
    }
    o1b_save_el($id, $elements, 'wp-page');
    $out[$file] = $id;
  }

  $header = o1b_read_template('header');
  $footer = o1b_read_template('footer');
  if (!is_wp_error($header)) {
    $out['header'] = o1b_save_pro_template('header', 'Option 1 Site Header', $header);
  } else {
    $out['header'] = $header->get_error_message();
  }
  if (!is_wp_error($footer)) {
    $out['footer'] = o1b_save_pro_template('footer', 'Option 1 Site Footer', $footer);
  } else {
    $out['footer'] = $footer->get_error_message();
  }

  o1b_purge_live_caches();
  return $out;
}
