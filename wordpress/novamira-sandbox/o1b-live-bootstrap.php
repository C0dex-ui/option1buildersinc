<?php
/**
 * Live bootstrap for option1buildersinc.com. Functions only — call o1b_live_bootstrap().
 * Drafts Skyline pages, creates new pages on the same slugs, rebuilds menus.
 */
if (!defined('ABSPATH')) {
  exit;
}

if (!function_exists('o1b_media')) {
  function o1b_media($key) {
    $map = get_option('o1b_media_map', []);
    return isset($map[$key]) && is_numeric($map[$key]) ? (int) $map[$key] : 0;
  }
}

if (!function_exists('o1b_media_url')) {
  function o1b_media_url($key) {
    $id = o1b_media($key);
    return $id ? wp_get_attachment_url($id) : '';
  }
}

if (!function_exists('o1b_page_id')) {
  function o1b_page_id($key) {
    $ids = get_option('o1b_page_ids', []);
    return isset($ids[$key]) ? (int) $ids[$key] : 0;
  }
}

if (!function_exists('o1b_media_files')) {
  function o1b_media_files() {
    return [
      'logo' => 'logo-header.png',
      'hero' => 'hero.webp',
      'showcase' => 'showcase.mp4',
      'project-manager' => 'team/project-manager.jpg',
      'project-01' => 'projects/project-01.jpg',
      'project-02' => 'projects/project-02.jpg',
      'project-03' => 'projects/project-03.jpg',
      'project-04' => 'projects/project-04.jpg',
      'project-05' => 'projects/project-05.jpg',
      'project-06' => 'projects/project-06.jpg',
      'project-09' => 'projects/project-09.jpg',
      'project-10' => 'projects/project-10.jpg',
      'badge-google' => 'badges/google.png',
      'badge-houzz' => 'badges/houzz.png',
      'badge-bbb' => 'badges/bbb.png',
      'badge-angi' => 'badges/angi.webp',
      'badge-top-pro' => 'badges/top-pro.webp',
      'badge-yelp' => 'badges/yelp.png',
      'award-angi-2025' => 'awards/angi-2025.png',
      'award-best-remodeler' => 'awards/best-remodeler.png',
      'award-excellence-2026' => 'awards/excellence-2026.png',
      'award-houzz-2023' => 'awards/houzz-2023.png',
      'award-remodel' => 'awards/remodel-award.png',
      'award-trusted' => 'awards/trusted-excellence.png',
      'partner-orco' => 'partners/orco.png',
      'partner-belgard' => 'partners/belgard.png',
      'partner-angelus' => 'partners/angelus-block.png',
      'partner-turf' => 'partners/turf-distributors.png',
      'partner-ewing' => 'partners/ewing.png',
      'partner-siteone' => 'partners/siteone.png',
      'partner-nds' => 'partners/nds.png',
      'partner-rainbird' => 'partners/rain-bird.png',
      'partner-hunter' => 'partners/hunter.png',
    ];
  }
}

function o1b_register_uploaded_media($key, $abs_path) {
  if (!$abs_path || !file_exists($abs_path)) {
    return 0;
  }
  $map = get_option('o1b_media_map', []);
  if (!is_array($map)) {
    $map = [];
  }
  if (!empty($map[$key]) && get_post((int) $map[$key])) {
    return (int) $map[$key];
  }
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';
  $filetype = wp_check_filetype($abs_path);
  $id = wp_insert_attachment([
    'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
    'post_title' => $key,
    'post_content' => '',
    'post_status' => 'inherit',
    'post_name' => 'media-' . $key,
  ], $abs_path);
  if (is_wp_error($id) || !$id) {
    return 0;
  }
  if (strpos((string) ($filetype['type'] ?? ''), 'video/') !== 0) {
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $abs_path));
  }
  $map[$key] = (int) $id;
  update_option('o1b_media_map', $map);
  return (int) $id;
}

function o1b_attach_uploaded_src() {
  $root = WP_CONTENT_DIR . '/uploads/o1b-src';
  $out = [];
  foreach (o1b_media_files() as $key => $rel) {
    $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
    $path = $root . '/o1b-' . $key . '.' . $ext;
    $out[$key] = o1b_register_uploaded_media($key, $path);
  }
  $logo = o1b_media('logo');
  if ($logo) {
    set_theme_mod('custom_logo', $logo);
  }
  return $out;
}

if (!function_exists('o1b_build_menu')) {
function o1b_build_menu($slug, $name, $items, $page_ids) {
  $menu = wp_get_nav_menu_object($slug);
  if (!$menu) {
    $by_name = wp_get_nav_menu_object($name);
    if ($by_name) {
      wp_update_term((int) $by_name->term_id, 'nav_menu', ['slug' => $slug]);
      $menu = wp_get_nav_menu_object($slug);
    }
  }
  $menu_id = $menu ? (int) $menu->term_id : (int) wp_create_nav_menu($name);
  if (!$menu && $menu_id && !is_wp_error($menu_id)) {
    wp_update_term($menu_id, 'nav_menu', ['slug' => $slug]);
  }
  if ($menu_id && !is_wp_error($menu_id)) {
    foreach (wp_get_nav_menu_items($menu_id) ?: [] as $item) {
      wp_delete_post($item->ID, true);
    }
  }
  foreach ($items as $item) {
    $path = trim((string) ($item['path'] ?? ''), '/');
    $hash = '';
    if (strpos($path, '#') !== false) {
      [$path, $hash] = explode('#', $path, 2);
      $hash = '#' . $hash;
    }
    $key = $path === '' ? 'home' : $path;
    $page_id = (int) ($page_ids[$key] ?? 0);
    if (!$page_id) {
      continue;
    }
    $url = get_permalink($page_id) . ($hash !== '#' ? $hash : '');
    wp_update_nav_menu_item($menu_id, 0, [
      'menu-item-title' => $item['title'],
      'menu-item-object' => 'page',
      'menu-item-object-id' => $page_id,
      'menu-item-type' => 'post_type',
      'menu-item-status' => 'publish',
      'menu-item-url' => $url,
    ]);
  }
  return $menu_id;
}
}

if (!function_exists('o1b_apply_kit')) {
function o1b_apply_kit() {
  $kit_id = (int) get_option('elementor_active_kit');
  if (!$kit_id) {
    return ['ok' => false];
  }
  $meta = get_post_meta($kit_id, '_elementor_page_settings', true);
  if (!is_array($meta)) {
    $meta = [];
  }
  $meta['system_colors'] = [
    ['_id' => 'primary', 'title' => 'Primary', 'color' => '#202321'],
    ['_id' => 'secondary', 'title' => 'Secondary', 'color' => '#111111'],
    ['_id' => 'text', 'title' => 'Text', 'color' => '#383d3e'],
    ['_id' => 'accent', 'title' => 'Accent', 'color' => '#b79a61'],
  ];
  $meta['custom_colors'] = [
    ['_id' => 'heading', 'title' => 'Heading', 'color' => '#292e2e'],
    ['_id' => 'tint', 'title' => 'Tint', 'color' => '#f8f8f6'],
    ['_id' => 'line', 'title' => 'Line', 'color' => '#d9d9d6'],
    ['_id' => 'gold', 'title' => 'Gold', 'color' => '#b79a61'],
  ];
  $meta['system_typography'] = [
    [
      '_id' => 'primary',
      'title' => 'Primary Headline',
      'typography_typography' => 'custom',
      'typography_font_family' => 'Montserrat',
      'typography_font_weight' => '700',
      'typography_text_transform' => 'uppercase',
      'typography_line_height' => ['unit' => 'em', 'size' => 1.18],
    ],
    [
      '_id' => 'secondary',
      'title' => 'Secondary Headline',
      'typography_typography' => 'custom',
      'typography_font_family' => 'Montserrat',
      'typography_font_weight' => '700',
    ],
    [
      '_id' => 'text',
      'title' => 'Body Text',
      'typography_typography' => 'custom',
      'typography_font_family' => 'Montserrat',
      'typography_font_weight' => '400',
      'typography_line_height' => ['unit' => 'em', 'size' => 1.75],
    ],
    [
      '_id' => 'accent',
      'title' => 'Accent Text',
      'typography_typography' => 'custom',
      'typography_font_family' => 'Montserrat',
      'typography_font_weight' => '600',
    ],
  ];
  $meta['container_width'] = ['unit' => 'px', 'size' => 1240];
  $meta['viewport_md'] = 768;
  $meta['viewport_lg'] = 1025;
  update_post_meta($kit_id, '_elementor_page_settings', $meta);
  return ['ok' => true, 'kit' => $kit_id];
}
}

function o1b_draft_skyline_pages() {
  $slugs = ['home', 'services', 'projects', 'about-us', 'blog', 'contact-us'];
  $protected = get_option('o1b_page_ids', []);
  $protected_ids = is_array($protected) ? array_map('intval', array_values($protected)) : [];
  $out = [];
  foreach ($slugs as $slug) {
    $found = get_page_by_path($slug, OBJECT, 'page');
    if (!$found) {
      $out[$slug] = 'missing';
      continue;
    }
    if (in_array((int) $found->ID, $protected_ids, true) && $found->post_status === 'publish') {
      $out[$slug] = ['kept' => (int) $found->ID];
      continue;
    }
    $new_slug = $slug . '-old';
    $taken = get_page_by_path($new_slug, OBJECT, 'page');
    if ($taken && (int) $taken->ID !== (int) $found->ID) {
      $new_slug = $slug . '-old-' . $found->ID;
    }
    wp_update_post([
      'ID' => $found->ID,
      'post_name' => $new_slug,
      'post_status' => 'draft',
    ]);
    $out[$slug] = ['id' => (int) $found->ID, 'slug' => $new_slug];
  }
  return $out;
}

function o1b_create_live_pages() {
  $pages = [
    'home' => ['Home', 'home'],
    'services' => ['Services', 'services'],
    'projects' => ['Projects', 'projects'],
    'about-us' => ['About Us', 'about-us'],
    'blog' => ['Blog', 'blog'],
    'contact-us' => ['Contact Us', 'contact-us'],
  ];
  $ids = get_option('o1b_page_ids', []);
  if (!is_array($ids)) {
    $ids = [];
  }
  foreach ($pages as $key => [$title, $slug]) {
    if (!empty($ids[$key]) && get_post((int) $ids[$key]) && get_post_status((int) $ids[$key]) === 'publish') {
      continue;
    }
    $found = get_page_by_path($slug, OBJECT, 'page');
    if ($found && $found->post_status === 'publish') {
      $ids[$key] = (int) $found->ID;
    } else {
      $ids[$key] = (int) wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
      ]);
    }
    update_post_meta($ids[$key], '_wp_page_template', 'elementor_header_footer');
    update_post_meta($ids[$key], '_elementor_edit_mode', 'builder');
  }
  update_option('o1b_page_ids', $ids);
  return $ids;
}

function o1b_draft_skyline_footer() {
  $id = 164;
  $post = get_post($id);
  if (!$post || $post->post_type !== 'elementor_library') {
    return ['ok' => false, 'reason' => 'missing'];
  }
  wp_update_post(['ID' => $id, 'post_status' => 'draft']);
  delete_post_meta($id, '_elementor_conditions');
  $all = get_option('elementor_pro_theme_builder_conditions', []);
  if (is_array($all) && isset($all['footer'][(string) $id])) {
    unset($all['footer'][(string) $id]);
    update_option('elementor_pro_theme_builder_conditions', $all);
  }
  return ['ok' => true, 'id' => $id];
}

function o1b_live_bootstrap() {
  update_option('blogname', 'Option 1 Builders');
  update_option('blogdescription', 'Artificial Grass Installation in Encino');
  update_option('permalink_structure', '/%postname%/');
  update_option('elementor_cpt_support', ['page', 'post']);
  update_option('elementor_disable_color_schemes', 'yes');
  update_option('elementor_disable_typography_schemes', 'yes');

  $drafted = o1b_draft_skyline_pages();
  $ids = o1b_create_live_pages();
  update_option('show_on_front', 'page');
  update_option('page_on_front', $ids['home']);
  if (!empty($ids['blog'])) {
    update_option('page_for_posts', $ids['blog']);
  }

  o1b_build_menu('o1b-primary', 'Option 1 Primary', [
    ['title' => 'Services', 'path' => '/services/'],
    ['title' => 'Projects', 'path' => '/projects/'],
    ['title' => 'About Us', 'path' => '/about-us/'],
    ['title' => 'Blog', 'path' => '/blog/'],
    ['title' => 'Contact', 'path' => '/contact-us/'],
  ], $ids);

  o1b_build_menu('o1b-footer-services', 'Option 1 Footer Services', [
    ['title' => 'Artificial Grass Installation', 'path' => '/services/#turf'],
    ['title' => 'Paver Installation', 'path' => '/services/#pavers'],
    ['title' => 'Landscape Design & Installation', 'path' => '/services/#design'],
    ['title' => 'Stepping Stones & Pathways', 'path' => '/services/#stones'],
    ['title' => 'Concrete, DG & Gravel', 'path' => '/services/#finishes'],
    ['title' => 'Irrigation & Drainage', 'path' => '/services/#irrigation'],
  ], $ids);

  o1b_build_menu('o1b-footer-pages', 'Option 1 Footer Pages', [
    ['title' => 'Home', 'path' => '/'],
    ['title' => 'Services', 'path' => '/services/'],
    ['title' => 'Projects', 'path' => '/projects/'],
    ['title' => 'About Us', 'path' => '/about-us/'],
    ['title' => 'Blog', 'path' => '/blog/'],
    ['title' => 'Contact Us', 'path' => '/contact-us/'],
  ], $ids);

  $locations = get_theme_mod('nav_menu_locations', []);
  $primary = wp_get_nav_menu_object('o1b-primary');
  if ($primary) {
    $locations['menu-1'] = (int) $primary->term_id;
    $locations['primary'] = (int) $primary->term_id;
    set_theme_mod('nav_menu_locations', $locations);
  }

  $media = o1b_attach_uploaded_src();
  $kit = o1b_apply_kit();
  $footer = o1b_draft_skyline_footer();
  flush_rewrite_rules(false);

  return [
    'drafted' => $drafted,
    'pages' => $ids,
    'media' => $media,
    'kit' => $kit,
    'skyline_footer' => $footer,
    'theme' => wp_get_theme()->get_stylesheet(),
    'front' => (int) get_option('page_on_front'),
    'urls' => [
      'home' => home_url('/'),
      'services' => get_permalink($ids['services']),
      'projects' => get_permalink($ids['projects']),
      'about-us' => get_permalink($ids['about-us']),
      'blog' => get_permalink($ids['blog']),
      'contact-us' => get_permalink($ids['contact-us']),
    ],
  ];
}
