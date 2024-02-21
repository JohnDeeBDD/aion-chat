<?php
/*
Plugin Name: Aion Chat
Plugin URI: https://ioncity.ai/ion-chat
Description: The Singularity is here.
Version: 1.1
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace AionChat;
//die("AionChat");

global $AionChatProtocal;
$AionChatProtocal = \get_option("aion-chat-protocol");

require_once(plugin_dir_path(__FILE__) . 'src/AionChat/autoloader.php');
\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
\add_filter( 'wp_is_application_passwords_available', '__return_true' );



//DevMode::enable();
Plugin::enable();
Comment::enable_interaction();
User::enable();
Conversation::enable_aion_conversation_cpt();
\register_activation_hook(__FILE__, '\AionChat\ActivationHook::do_activation_hook');


//require_once(plugin_dir_path(__FILE__) . 'src/update-checker/plugin-update-checker.php');
//use EmailTunnel\ActivationAttemptPage;use AionChatMothership\Conversations;use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
//$myUpdateChecker = PucFactory::buildUpdateChecker(
//    'https://ioncity.ai/wp-content/uploads/details.json',
 //   __FILE__, //Full path to the main plugin file or functions.php.
  //  'aion-chat'
//);

//\add_action("init", function(){$x = User::is_user_an_Aion(5);var_dump($x);die();});
