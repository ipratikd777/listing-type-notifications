<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Delete custom tables

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_listing_notifications");

// Delete plugin options or settings if any
// delete_option('your_plugin_option');
