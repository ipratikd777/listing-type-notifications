<?php
/**
 * Plugin Name: Listing Type Notifications
 * Description: A plugin to create posts via a custom form and display notifications.
 * Version: 1.0
 * Author: ME
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
include(plugin_dir_path(__FILE__) . 'includes/functions.php');

// Activation hook
register_activation_hook(__FILE__, 'ltype_activate_plugin');

function ltype_activate_plugin() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();


  $sql1 = "CREATE TABLE {$wpdb->prefix}custom_listing_notifications (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      post_id mediumint(9) NOT NULL,
      message text NOT NULL,
      status varchar(50) NOT NULL,
      adstatus varchar(50) NOT NULL,
      user_id mediumint(9) NOT NULL,
      created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  
  dbDelta($sql1);

  
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'ltype_deactivate_plugin');

function ltype_deactivate_plugin() {
    // Actions to perform on deactivation, if any
}

// Uninstall hook (this is optional, as the uninstall.php file is auto-detected if named correctly)
register_uninstall_hook(__FILE__, 'ltype_uninstall_plugin');

function ltype_uninstall_plugin() {
    global $wpdb;

    // Delete custom tables
    
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_listing_notifications");

    // Delete plugin options or settings if any
    // delete_option('your_plugin_option');
}
// Enqueue scripts
add_action('wp_enqueue_scripts', 'ltn_enqueue_scripts');

function ltn_enqueue_scripts() {
    wp_enqueue_style('ltn-custom-stylesheet', plugin_dir_url(__FILE__) . 'assets/css/custom-style.css');
    wp_enqueue_script('ltn-custom-scripts', plugin_dir_url(__FILE__) . 'assets/js/custom-scripts.js', array('jquery'), '1.0', true);
    wp_localize_script('ltn-custom-scripts', 'ajaxurl', admin_url('admin-ajax.php'));
}