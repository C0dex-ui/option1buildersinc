<?php
/**
 * Option 1 Builders Local bootstrap. Functions only — call o1b_bootstrap().
 */
if (!defined('ABSPATH')) {
  exit;
}

function o1b_src_root() {
  return 'C:/Users/ADMIN/Documents/WEBSITE_DEVELOPMENT/KEVIN/Option 1 Builders/images';
}

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

function o1b_media($key) {
  $map = get_option('o1b_media_map', []);
  return isset($map[$key]) && is_numeric($map[$key]) ? (int) $map[$key] : 0;
}

function o1b_media_url($key) {
  $id = o1b_media($key);
  return $id ? wp_get_attachment_url($id) : '';
}

function o1b_page_id($key) {
  $ids = get_option('o1b_page_ids', []);
  return isset($ids[$key]) ? (int) $ids[$key] : 0;
}

function o1b_register_media_file($key, $rel) {
  $path = trailingslashit(o1b_src_root()) . $rel;
  if (!file_exists($path)) {
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
  $upload = wp_upload_dir();
  $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
  $dest = trailingslashit($upload['path']) . 'o1b-' . $key . '.' . $ext;
  if (!copy($path, $dest)) {
    return 0;
  }
  $filetype = wp_check_filetype($dest);
  $id = wp_insert_attachment([
    'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
    'post_title' => $key,
    'post_content' => '',
    'post_status' => 'inherit',
    'post_name' => 'media-' . $key,
  ], $dest);
  if (is_wp_error($id) || !$id) {
    return 0;
  }
  wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $dest));
  $map[$key] = (int) $id;
  update_option('o1b_media_map', $map);
  return (int) $id;
}

function o1b_import_media() {
  $out = [];
  foreach (o1b_media_files() as $key => $rel) {
    $out[$key] = o1b_register_media_file($key, $rel);
  }
  $logo = o1b_media('logo');
  if ($logo) {
    set_theme_mod('custom_logo', $logo);
  }
  return $out;
}

function o1b_ensure_page($title, $slug, $parent_id = 0) {
  $path = $parent_id ? (get_page_uri($parent_id) . '/' . $slug) : $slug;
  $found = get_page_by_path($path, OBJECT, 'page');
  if ($found) {
    wp_update_post([
      'ID' => $found->ID,
      'post_title' => $title,
      'post_status' => 'publish',
      'post_parent' => (int) $parent_id,
      'post_name' => $slug,
    ]);
    return (int) $found->ID;
  }
  return (int) wp_insert_post([
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_title' => $title,
    'post_name' => $slug,
    'post_parent' => (int) $parent_id,
  ]);
}

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
    o1b_menu_add_item($menu_id, $item, $page_ids, 0);
  }
  return $menu_id;
}

function o1b_menu_add_item($menu_id, $item, $page_ids, $parent_item = 0) {
  $path = trim((string) ($item['path'] ?? ''), '/');
  $hash = '';
  if (strpos($path, '#') !== false) {
    [$path, $hash] = explode('#', $path, 2);
    $hash = '#' . $hash;
  }
  $key = $path === '' ? 'home' : basename($path);
  $page_id = (int) ($page_ids[$key] ?? 0);
  if (!$page_id) {
    return 0;
  }
  $url = get_permalink($page_id) . ($hash !== '#' ? $hash : '');
  $id = wp_update_nav_menu_item($menu_id, 0, [
    'menu-item-title' => $item['title'],
    'menu-item-object' => 'page',
    'menu-item-object-id' => $page_id,
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
    'menu-item-parent-id' => (int) $parent_item,
    'menu-item-url' => $url,
  ]);
  foreach ($item['children'] ?? [] as $child) {
    o1b_menu_add_item($menu_id, $child, $page_ids, $id);
  }
  return (int) $id;
}

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

function o1b_bootstrap() {
  update_option('blogname', 'Option 1 Builders');
  update_option('blogdescription', 'Artificial Grass Installation in Encino');
  update_option('permalink_structure', '/%postname%/');
  update_option('elementor_cpt_support', ['page', 'post']);
  update_option('elementor_disable_color_schemes', 'yes');
  update_option('elementor_disable_typography_schemes', 'yes');

  $pages = [
    'home' => ['Home', 'home'],
    'services' => ['Services', 'services'],
    'projects' => ['Projects', 'projects'],
    'about-us' => ['About Us', 'about-us'],
    'blog' => ['Blog', 'blog'],
    'contact-us' => ['Contact Us', 'contact-us'],
  ];
  $ids = [];
  foreach ($pages as $key => [$title, $slug]) {
    $ids[$key] = o1b_ensure_page($title, $slug);
    update_post_meta($ids[$key], '_wp_page_template', 'elementor_header_footer');
    update_post_meta($ids[$key], '_elementor_edit_mode', 'builder');
  }
  update_option('o1b_page_ids', $ids);
  update_option('show_on_front', 'page');
  update_option('page_on_front', $ids['home']);

  $sample = get_page_by_path('sample-page');
  if ($sample) {
    wp_update_post(['ID' => $sample->ID, 'post_status' => 'draft']);
  }

  $child_pages = [
    'artificial-grass-installation' => 'Artificial Grass Installation',
    'paver-installation' => 'Paver Installation',
    'landscape-design-installation' => 'Landscape Design & Installation',
    'stepping-stones-pathways' => 'Stepping Stones & Pathways',
    'concrete-dg-gravel' => 'Concrete, DG & Gravel',
    'irrigation-drainage' => 'Irrigation & Drainage',
    'vinyl-fencing' => 'Vinyl Fencing',
  ];
  foreach ($child_pages as $slug => $title) {
    $ids[$slug] = o1b_ensure_page($title, $slug, $ids['services']);
    update_post_meta($ids[$slug], '_wp_page_template', 'elementor_header_footer');
    update_post_meta($ids[$slug], '_elementor_edit_mode', 'builder');
  }
  update_option('o1b_page_ids', $ids);

  o1b_build_menu('o1b-primary', 'Option 1 Primary', [
    [
      'title' => 'Services',
      'path' => '/services/',
      'children' => [
        ['title' => 'Artificial Grass Installation', 'path' => '/services/artificial-grass-installation/'],
        ['title' => 'Paver Installation', 'path' => '/services/paver-installation/'],
        ['title' => 'Landscape Design & Installation', 'path' => '/services/landscape-design-installation/'],
        ['title' => 'Stepping Stones & Pathways', 'path' => '/services/stepping-stones-pathways/'],
        ['title' => 'Concrete, DG & Gravel', 'path' => '/services/concrete-dg-gravel/'],
        ['title' => 'Irrigation & Drainage', 'path' => '/services/irrigation-drainage/'],
        ['title' => 'Vinyl Fencing', 'path' => '/services/vinyl-fencing/'],
      ],
    ],
    ['title' => 'Projects', 'path' => '/projects/'],
    ['title' => 'About Us', 'path' => '/about-us/'],
    ['title' => 'Blog', 'path' => '/blog/'],
    ['title' => 'Contact', 'path' => '/contact-us/'],
  ], $ids);

  o1b_build_menu('o1b-footer-services', 'Option 1 Footer Services', [
    ['title' => 'Artificial Grass Installation', 'path' => '/services/artificial-grass-installation/'],
    ['title' => 'Paver Installation', 'path' => '/services/paver-installation/'],
    ['title' => 'Landscape Design & Installation', 'path' => '/services/landscape-design-installation/'],
    ['title' => 'Stepping Stones & Pathways', 'path' => '/services/stepping-stones-pathways/'],
    ['title' => 'Concrete, DG & Gravel', 'path' => '/services/concrete-dg-gravel/'],
    ['title' => 'Irrigation & Drainage', 'path' => '/services/irrigation-drainage/'],
    ['title' => 'Vinyl Fencing', 'path' => '/services/vinyl-fencing/'],
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

  $media = o1b_import_media();
  $kit = o1b_apply_kit();
  flush_rewrite_rules(false);

  return [
    'pages' => $ids,
    'media' => $media,
    'kit' => $kit,
    'theme' => wp_get_theme()->get_stylesheet(),
  ];
}
