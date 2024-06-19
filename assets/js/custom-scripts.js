jQuery(document).ready(function($) {
  // On click clear button all user
  $(document).on('click', 'a.clear-all-notifications', function(e) {
    e.preventDefault();
    var currId = $(this).data('currentuid');
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'lt_clear_notification',
            currentId : currId,
        },
        success: function(response) {
          if (response.success) {
            $('.user-notifications').empty();
          } else {
              alert('Failed to clear all notifications.');
          }
        }
    });
  });

  // On click clear button for admin
  $(document).on('click', 'a.admin-clear-all-notifications', function(e) {
    e.preventDefault();
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'lt_admin_clear_notification',
        },
        success: function(response) {
          if (response.success) {
            $('.admin-notifications').empty();
          } else {
              alert('Failed to clear all notifications.');
          }
        }
    });
  });
});