# Listing Type Notification
# This is notification plugin for admin and user notification for custom post type creation from the front side of your website.
# You can donwload this plugin zip and upload it as a wordpress plugin
Log in to Your WordPress Admin Dashboard

Go to yourdomain.com/wp-admin and log in with your credentials.
Navigate to Plugins Section

On the left sidebar, click on "Plugins" and then "Add New."
Upload the Plugin

Click on the "Upload Plugin" button at the top of the page.
Click "Choose File" and select the plugin ZIP file from your computer.
Click the "Install Now" button.
Activate the Plugin

Once the plugin is uploaded and installed, click the "Activate Plugin" link.

This is custom notification plugin.

Put the below code in ajax function on events like submit, change status etc

listing_create_notification($post_id, $message, $userid);

$postid = put your custom post type post id
$message = put here custom message
$userid = put here user id 

The data will add in the database using above function.

Use below shortcode for showing admin/ user notification

For User Notification shortcode : [user_listing_notifications]

For Admin Notification Shortcode : [all_user_notifications]
