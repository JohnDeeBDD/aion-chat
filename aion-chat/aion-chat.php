<?php
/*
Plugin Name: Aion Chat
Plugin URI: https://aion.garden
Description: The Singularity is here.
Version: 1.2
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2024, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace AionChat;

//die("AionChat");

global $AionChatProtocal;
$AionChatProtocal = "remote_node";

require_once(plugin_dir_path(__FILE__) . 'src/AionChat/autoloader.php');

global $Servers;
$Servers = new Servers();

\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
\add_filter('wp_is_application_passwords_available', '__return_true' );
\add_action('admin_menu', '\AionChat\Plugin::do_create_admin_page');
\add_action('comment_post', '\AionChat\Comment::route_comments', 10, 1);
\add_action('init', '\AionChat\User::add_aion_role');
Conversation::enable_aion_conversation_cpt();
ExampleConversation::enablePublishExampleConversations();
Functions::enableFunctionCall();


\register_activation_hook(__FILE__, '\AionChat\ActivationHook::do_activation_hook');

require_once(plugin_dir_path(__FILE__) . 'src/update-checker/plugin-update-checker.php');
use EmailTunnel\ActivationAttemptPage;use AionChatMothership\Conversations;use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://aion.garden/wp-content/uploads/details.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'aion-chat'
);


if(isset($_GET['q'])){
    \add_action("init", function () {
        echo(
            DirectQuestion::ask(
                "In Greek mythology, who was Asclepius?"
            )
        );
        die();
    });
}