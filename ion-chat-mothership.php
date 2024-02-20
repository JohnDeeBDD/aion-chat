<?php
/*
Plugin Name: Ion Chat Mothership
Plugin URI: https://generalchicken.guru
Description: The Singularity is here.
Version: 1.0
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/


namespace IonChatMothership;

use function IonChat\is_ion_mentioned;

require_once (plugin_dir_path(__FILE__). 'src/IonChatMothership/autoloader.php');

\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
//die("ionchatms");


global $IonChatProtocal;
$IonChatProtocal = "mothership";

//Connections::enable_ion_connections_cpt();
TrafficController::enable_prompt_incoming();
Comment::enable_interaction();
Email::enable_receiveing();
Ping::enableReceivePing();
User::enable_user_edit_app_passwords_screen();
User::enable();

//Servers page
if (isset($_GET['ion-dev'])) {
    $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
    $IPs = json_decode($file);
    $dev1IP = $IPs[0];
    $dev2IP = $IPs[1];
    echo("<a href = 'http://$dev1IP/wp-admin/' target = '_blank'>Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/wp-admin' target = '_blank'>Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?option=ion-chat-log' target = '_blank'>ion-chat-log</a><br />");
    echo("<a href = 'http://$dev1IP/?option=ion-chat-protocol' target = '_blank'>Mothership Protocol</a><br />");
    echo("<a href = 'http://$dev2IP/?option=ion-chat-protocol' target = '_blank'>Remote Protocol</a><br />");
    echo("<a href = 'http://$dev1IP/?option=ion-chat-up-bus' target = '_blank'>Up Bus Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/?option=ion-chat-up-bus' target = '_blank'>Up Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?option=curl_debug' target = '_blank'>Curl Debug</a><br />");
    echo("<a href = 'http://$dev1IP/?option=down_bus' target = '_blank'>Down Bus MS</a><br />");
    echo("<a href = 'http://$dev2IP/?option=down_bus' target = '_blank'>Down Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?option=debug_info' target = '_blank'>Debug</a><br />");
    die();
}

// Hook into the user profile edit screen






// Enqueue scripts and styles (if necessary)
// add_action('admin_enqueue_scripts', 'ionchatmothership_enqueue_scripts');

// Handle data submission
\add_action('personal_options_update', '\IonChatMothership\ionchatmothership_save_custom_user_profile_fields');
\add_action('edit_user_profile_update', '\IonChatMothership\ionchatmothership_save_custom_user_profile_fields');

function ionchatmothership_save_custom_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Update user meta or perform other actions
    // update_user_meta($user_id, 'remote_site_url', $_POST['remote_site_url']);
}

