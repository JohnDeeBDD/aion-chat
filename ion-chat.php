<?php
/*
Plugin Name: Ion Chat
Plugin URI: https://generalchicken.guru
Description: The Singularity is near.
Version: 1.0
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace IonChat;

//require_once (plugin_dir_path(__FILE__). 'src/IonChat/autoloader.php');
require_once ('/var/www/html/wp-content/plugins/ion-chat/vendor/autoload.php');

//

$file = file_get_contents(plugin_dir_path(__FILE__). "servers.json");
$IPs = json_decode($file);
$mothership_url = "http://" . $IPs[0];
$remote_url = "http://" . $IPs[1];
global $dev1IP;
global $dev2IP;
$dev1IP = $IPs[0];
$dev2IP = $IPs[1];

if (!isset($IonChatProtocal)) {
    global $IonChatProtocal;
    $IonChatProtocal = "remote_node";
}

//DebugMode::enable();

\register_activation_hook(__FILE__, 'IonChat\activate_ion_chat');
function activate_ion_chat()
{
}

AdminPage::enable();

\add_action('comment_post', '\IonChat\comment_posted', 10, 1);


function comment_posted($comment_ID)
{
    global $IonChatProtocal;
    if ("remote_node" === $IonChatProtocal) {    // Retrieve the comment object
        $comment = \get_comment($comment_ID);

        // Retrieve the user_id of the comment author
        $user_id = $comment->user_id;

        // Check if the user is an "ion user"
        if (User::is_ion_user($user_id)) {
            return;
        }
        $Prompt = new Prompt();
        $Prompt->init_this_prompt($comment_ID, "created on remote");
        $response = $Prompt->send_up();
        $Prompt->post_comment_to_post($response['body']);
    }
}

require_once (plugin_dir_path(__FILE__). 'src/plugin-update-checker/plugin-update-checker.php');

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://ioncity.ai/wp-content/uploads/details.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'ion-chat'
);

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}