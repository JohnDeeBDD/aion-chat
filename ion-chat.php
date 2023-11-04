<?php
/*
Plugin Name: Ion Chat
Plugin URI: https://ioncity.ai/ion-chat
Description: The Singularity is here.
Version: 1.1
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace IonChat;
//die("ionchat");

require_once(plugin_dir_path(__FILE__) . 'src/IonChat/autoloader.php');
\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
\add_filter( 'wp_is_application_passwords_available', '__return_true' );

DevMode::enable();

Plugin::enable();
Comment::enable_interaction();
User::enable();
\register_activation_hook(__FILE__, '\IonChat\ActivationHook::do_activation_hook');
Conversation::enable_aion_conversation_cpt();

require_once(plugin_dir_path(__FILE__) . 'src/update-checker/plugin-update-checker.php');
use EmailTunnel\ActivationAttemptPage;use IonChatMothership\Conversations;use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://ioncity.ai/wp-content/uploads/details.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'ion-chat'
);

//\add_action("init", function(){$x = User::is_user_an_Aion(5);var_dump($x);die();});

