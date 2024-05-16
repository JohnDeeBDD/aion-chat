<?php
/*
Plugin Name: Aion Chat
Plugin URI: https://aion.garden
Description: The Singularity is here.
Version: 1.1
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2024, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace AionChat;

//die("AionChat");

global $AionChatProtocal;
$AionChatProtocal = \get_option("aion-chat-protocol");

require_once(plugin_dir_path(__FILE__) . 'src/AionChat/autoloader.php');

global $Servers;
$Servers = new Servers();

\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
\add_filter('wp_is_application_passwords_available', '__return_true' );

//$modeStrategy = "dev";
$modeStrategy = "prod";


//Servers::loadServerGlobalVariablesFromJSON($modeStrategy);
Plugin::setupProtocol($modeStrategy);
\add_action('admin_menu', '\AionChat\Plugin::do_create_admin_page');
Comment::enable_interaction();
User::enable();
Conversation::enable_aion_conversation_cpt();
Conversation::enableStubConversations();
Functions::enableFunctionCall();

\register_activation_hook(__FILE__, '\AionChat\ActivationHook::do_activation_hook');

require_once(plugin_dir_path(__FILE__) . 'src/update-checker/plugin-update-checker.php');
use EmailTunnel\ActivationAttemptPage;use AionChatMothership\Conversations;use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://aion.garden/wp-content/uploads/details.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'aion-chat'
);