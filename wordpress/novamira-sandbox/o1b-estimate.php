<?php
if (!defined('ABSPATH')) {
  exit;
}

add_action('init', function () {
  register_post_type('o1b_lead', [
    'labels' => ['name' => 'Estimate leads', 'singular_name' => 'Estimate lead'],
    'public' => false,
    'show_ui' => true,
    'supports' => ['title', 'editor'],
    'menu_icon' => 'dashicons-email-alt',
  ]);
}, 5);

function o1b_estimate_email() {
  return 'info.option1builders@gmail.com';
}

function o1b_estimate_services() {
  return [
    'Artificial grass installation',
    'Paver patio or walkway',
    'Paver or concrete driveway',
    'Full yard transformation',
    'Stepping stones, DG or gravel',
    'Irrigation or drainage',
    'Vinyl fencing',
    'Not sure — need advice',
  ];
}

function o1b_estimate_handle() {
  if (empty($_POST['o1b_estimate_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['o1b_estimate_nonce'])), 'o1b_estimate')) {
    return new WP_Error('nonce', 'Please try again.');
  }
  $name = isset($_POST['o1b_name']) ? sanitize_text_field(wp_unslash($_POST['o1b_name'])) : '';
  $phone = isset($_POST['o1b_phone']) ? sanitize_text_field(wp_unslash($_POST['o1b_phone'])) : '';
  $email = isset($_POST['o1b_email']) ? sanitize_email(wp_unslash($_POST['o1b_email'])) : '';
  $city = isset($_POST['o1b_city']) ? sanitize_text_field(wp_unslash($_POST['o1b_city'])) : '';
  $service = isset($_POST['o1b_service']) ? sanitize_text_field(wp_unslash($_POST['o1b_service'])) : '';
  $details = isset($_POST['o1b_details']) ? sanitize_textarea_field(wp_unslash($_POST['o1b_details'])) : '';
  if ($name === '' || $phone === '' || $email === '' || !is_email($email)) {
    return new WP_Error('fields', 'Name, phone, and a valid email are required.');
  }
  $body = "Name: $name\nPhone: $phone\nEmail: $email\nCity: $city\nService: $service\n\n$details\n";
  $id = wp_insert_post([
    'post_type' => 'o1b_lead',
    'post_status' => 'private',
    'post_title' => $name . ' — ' . $service,
    'post_content' => $body,
  ]);
  wp_mail(o1b_estimate_email(), 'Estimate request from option1buildersinc.com', $body, ['Reply-To: ' . $email]);
  return $id;
}

add_shortcode('o1b_estimate', function () {
  $notice = '';
  if (!empty($_POST['o1b_estimate_submit'])) {
    $result = o1b_estimate_handle();
    if (is_wp_error($result)) {
      $notice = '<p class="form__note">' . esc_html($result->get_error_message()) . '</p>';
    } else {
      $notice = '<p class="form__note">We reply during business hours. No obligation, no pressure.</p>';
    }
  }
  $options = '';
  foreach (o1b_estimate_services() as $service) {
    $options .= '<option value="' . esc_attr($service) . '">' . esc_html($service) . '</option>';
  }
  $uid = uniqid('o1b_');
  return $notice . '<form class="form o1b-form" method="post">'
    . wp_nonce_field('o1b_estimate', 'o1b_estimate_nonce', true, false)
    . '<div class="field"><label for="' . $uid . 'name">Full name</label><input id="' . $uid . 'name" name="o1b_name" type="text" autocomplete="name" required></div>'
    . '<div class="field"><label for="' . $uid . 'phone">Phone</label><input id="' . $uid . 'phone" name="o1b_phone" type="tel" autocomplete="tel" required></div>'
    . '<div class="field"><label for="' . $uid . 'email">Email</label><input id="' . $uid . 'email" name="o1b_email" type="email" autocomplete="email" required></div>'
    . '<div class="field"><label for="' . $uid . 'city">Project city</label><input id="' . $uid . 'city" name="o1b_city" type="text" placeholder="Encino, Sherman Oaks, Tarzana…"></div>'
    . '<div class="field"><label for="' . $uid . 'service">Service needed</label><select id="' . $uid . 'service" name="o1b_service">' . $options . '</select></div>'
    . '<div class="field"><label for="' . $uid . 'details">Project details</label><textarea id="' . $uid . 'details" name="o1b_details" rows="4" placeholder="Approximate square footage, front or back yard, timeline…"></textarea></div>'
    . '<button class="btn btn--gold btn--block" type="submit" name="o1b_estimate_submit" value="1">Request Free Estimate</button>'
    . '<p class="form__note">We reply during business hours. No obligation, no pressure.</p>'
    . '</form>';
});
