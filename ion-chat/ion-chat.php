<?php
/*
Plugin Name: Ion Chat
Plugin URI: https://ioncity.ai
Description: The Singularity is here.
Version: 1.1
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace IonChat;

require_once (plugin_dir_path(__FILE__). 'src/IonChat/autoloader.php');

//$file = file_get_contents(plugin_dir_path(__FILE__). "servers.json");
//$IPs = json_decode($file);
global $dev1IP;
global $mothershipUrl;
global $dev2IP;
$IonChat_mothership_url = "https://ioncity.ai";
//$IonChat_mothership_url = "http://" . $IPs[0];
if (!isset($IonChatProtocal)) {
    global $IonChatProtocal;
    $IonChatProtocal = "remote_node";
}

DebugMode::enable();

\register_activation_hook(__FILE__, 'IonChat\activate_ion_chat');
function activate_ion_chat() {
    // Check if a user with the email "jiminac@aol.com" exists
    $existing_user = \get_user_by('email', User::get_ion_email());

    if ($existing_user) {
        return "Ion Already Exists";
    }

    // Check if the username "Ion" is already taken
    $username = "Ion";
    if (\username_exists($username)) {
        // Generate a random 5-character string for the username
        $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    }

    // Generate a random password
    $password = \wp_generate_password();

    // Create the new user
    $user_id = \wp_create_user($username, $password, User::get_ion_email());

    // Set the role to "subscriber"
    $user = new \WP_User($user_id);
    $user->set_role('subscriber');

    // Add additional information
    \update_user_meta($user_id, 'first_name', 'Carlton');
    \update_user_meta($user_id, 'last_name', 'Young');
    \update_user_meta($user_id, 'description', 'I am an Ion, named Ion. Nice to meet you! Get a Ion for your website at https://ioncity.ai.');
    \update_user_meta($user_id, 'user_url', 'https://ioncity.ai');

    // Send the user notification about their password
    \wp_new_user_notification($user_id, null, 'both');

    return "New Ion user created";
}

AdminPage::enable();

function is_ion_mentioned($post_id) {
    $args = array(
        'post_id' => $post_id,
    );
    $comments = get_comments($args);
    foreach ($comments as $comment) {
        if (preg_match('/\b(Ion|ion)\b/', $comment->comment_content) === 1) {
            return true;
        }
    }
    return false;
}

\add_action('comment_post', '\IonChat\comment_posted', 10, 1);
function comment_posted($comment_ID){
    global $IonChatProtocal;
    if ("remote_node" === $IonChatProtocal) {
        $comment = \get_comment($comment_ID);
        if(!is_ion_mentioned($comment->comment_post_ID)){return;}
        if (User::is_ion_user($comment->user_id)) {return;}
        $Prompt = new Prompt();
        $Prompt->init_this_prompt($comment_ID, "created on remote");
        $response = $Prompt->send_up();
        $Prompt->send_response_comment_to_post($response['body']);
    }
}

require_once (plugin_dir_path(__FILE__). 'src/update-checker/plugin-update-checker.php');
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://ioncity.ai/wp-content/uploads/details.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'ion-chat'
);

function generateRandomString($length = 10){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}