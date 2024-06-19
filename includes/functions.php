<?php


function listing_create_notification($post_id, $message, $luserid) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'custom_listing_notifications';
  $wpdb->insert($table_name, array(
      'post_id' => $post_id,
      'message' => $message,
      'status' => 'unread',
      'adstatus' => 'unread',
      'user_id' => $luserid,
      'created_at' => current_time('mysql')
  ));
}

// Shortcode to display notifications
function listingType_display_user_notifications() {
  if (is_user_logged_in()) {
      global $wpdb;
      $current_user = wp_get_current_user();
      // echo '<pre>';
      // print_r($current_user);exit;
      $table_name = $wpdb->prefix . 'custom_listing_notifications';

      $notifications = $wpdb->get_results($wpdb->prepare(
          "SELECT * FROM $table_name WHERE post_id IN (
              SELECT ID FROM {$wpdb->prefix}posts WHERE post_author = %d
          ) AND status = 'unread' ORDER BY id DESC",
          $current_user->ID
      ));

      if (!empty($notifications)) {
          ob_start();
          echo '<div class="ltype-notification notification-dropdown" id="ltype-notification"><div class="dropdown-head"><h6>Notifications</h6><a class="clear-all-notifications clear-link" data-currentuid="'.$current_user->ID.'">Clear All</a></div>';
          echo '<div class="user-notifications dropdown-body">';
          foreach ($notifications as $notification) {
              $time_diff = human_time_diff(strtotime($notification->created_at), current_time('timestamp'));
              echo '<div data-nid="'.$notification->id.'" class="notification-box" ><div class="icon"></div><em>' . wp_kses_post($notification->message) . '</em> <div class="time">'. $time_diff . ' ago</div></div>';
              
              // Mark notification as read
              //$wpdb->update($table_name, array('status' => 'read'), array('id' => $notification->id));
          }
          echo '</div></div>';
          return ob_get_clean();
      } else {
          return '<div class="notification-dropdown"><div class="dropdown-head"><h6>Notifications</h6></div><div class="dropdown-body">No new notifications.</div></div>';
      }
  } else {
      return '<div class="notification-dropdown"><div class="dropdown-head"><h6>Notifications</h6></div><div class="dropdown-body">Please log in to see your notifications.</div></div>';
  }
}

add_shortcode('user_listing_notifications', 'listingType_display_user_notifications');

/*******
 * display all notification to admin shortcode
 */
function listingType_display_all_user_notifications() {
    if (current_user_can('administrator')) {
        $current_user = wp_get_current_user();

        if (is_user_logged_in() && $current_user instanceof WP_User) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'custom_listing_notifications';

        // Fetch all notifications
        $notifications = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE adstatus = 'unread' ORDER BY id DESC"
        );

        if (!empty($notifications)) {
            ob_start();
            echo '<div class="admin-notification notification-dropdown" id="lt-admin-notification"><div class="dropdown-head"><h6>Notifications</h6><a class="admin-clear-all-notifications">Clear All</a></div>';
            echo '<div class="admin-notifications dropdown-body">';
            foreach ($notifications as $notification) {
                $user_info = get_userdata($notification->user_id);
                $time_diff = human_time_diff(strtotime($notification->created_at), current_time('timestamp'));
                echo '<div data-nid="'.$notification->id.'" class="notification-box"  ><div class="icon"></div><strong>' . ( !empty ( $user_info->user_login ) ? esc_html($user_info->user_login) : '' )  . ':</strong> <em class="msg">' . wp_kses_post($notification->message) . '</em> <div class="time">'. $time_diff . ' ago</div></div>';
            }
            echo '</div></div>';
            return ob_get_clean();
        } else {
            return '<div class="notification-dropdown"><div class="dropdown-head"><h6>Notifications</h6></div><div class="dropdown-body">No new notifications.</div></div>';
        }
    }
    } else {
        return '<div class="notification-dropdown"><div class="dropdown-head"><h6>Notifications</h6></div><div class="dropdown-body">You do not have permission to view these notifications.</div></div>';
    }
}

add_shortcode('all_user_notifications', 'listingType_display_all_user_notifications');


// Handle clearing notification via AJAX For User notification
add_action('wp_ajax_lt_clear_notification', 'custom_lt_clear_notification');
add_action('wp_ajax_nopriv_lt_clear_notification', 'custom_lt_clear_notification');

function custom_lt_clear_notification() {
      global $wpdb;
      
      $currentUId = (isset($_REQUEST['currentId']) ? $_REQUEST['currentId'] : ''); // get current User id
      $current_user_id = get_current_user_id();
      if (!$current_user_id) {
          wp_send_json_error('User not logged in.');
      }

      $table_name = $wpdb->prefix . 'custom_listing_notifications';
      $post_ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_author = %d", $current_user_id));

      if (empty($post_ids)) {
          wp_send_json_error('No notifications found.');
      }

      $result = $wpdb->query("UPDATE $table_name SET status = 'read' WHERE post_id IN (" . implode(',', $post_ids) . ") AND user_id=$currentUId");

      if ($result !== false) {
          wp_send_json_success();
      } else {
          wp_send_json_error('Failed to update all notifications.');
      }
}

// Handle clearing notification via AJAX For Admin notification
add_action('wp_ajax_lt_admin_clear_notification', 'custom_lt_admin_clear_notification');
add_action('wp_ajax_nopriv_lt_admin_clear_notification', 'custom_lt_admin_clear_notification');

function custom_lt_admin_clear_notification() {
      global $wpdb;

      $current_user_id = get_current_user_id();
      if (!$current_user_id) {
          wp_send_json_error('User not logged in.');
      }
        if (current_user_can('administrator')) {
            $table_name = $wpdb->prefix . 'custom_listing_notifications';
            

            $result = $wpdb->query("UPDATE $table_name SET adstatus = 'read'");

            if ($result !== false) {
                wp_send_json_success();
            } else {
                wp_send_json_error('Failed to update all notifications.');
            }
        }
}

?>