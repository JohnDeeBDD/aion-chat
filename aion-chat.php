<?php
/*
Plugin Name: Aion Chat
Plugin URI: https://aion.garden
Description: The Singularity is here.
Version: 3
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

add_filter('comment_flood_filter', '__return_false');
add_filter('duplicate_comment_id', '__return_false');
add_filter('wp_is_application_passwords_available', '__return_true');
add_action('admin_menu', '\AionChat\Plugin::do_create_admin_page');
add_action('comment_post', '\AionChat\Comment::action_comment_post', 10, 1);
add_action('init', '\AionChat\User::add_aion_role');
Conversation::enable_aion_conversation_cpt();
ExampleConversation::enablePublishExampleConversations();
Functions::enableFunctionCall();
register_activation_hook(__FILE__, '\AionChat\ActivationHook::do_activation_hook');

require_once(plugin_dir_path(__FILE__) . 'src/update-checker/plugin-update-checker.php');

use EmailTunnel\ActivationAttemptPage;
use AionChatMothership\Conversations;
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use function add_action;
use function add_filter;
use function is_singular;
use function register_activation_hook;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://aion.garden/wp-content/uploads/details.json',
    __FILE__,
    'aion-chat'
);

\add_action('wp_enqueue_scripts', function () {
    if (\is_singular("aion-conversation")) {
        \wp_register_script(
            'aion-conversation-cpt',
            plugin_dir_url(__FILE__) . '/src/AionChat/aion-conversation-cpt.js', // here is the JS file
            ['jquery', 'wp-api', 'heartbeat'],
            '1.0',
            true
        );
        \wp_enqueue_script('aion-conversation-cpt');
    }
});

if(isset($_GET['model'])){
   /// die("xxx");
}

\add_action('comment_form', '\AionChat\add_custom_comment_button');

function add_custom_comment_button() {
    // Define the custom button HTML
    $custom_button = '<p><p class="comment-form-custom-button">
                        <button type="button" id = "aion-chat-dall-e-3-button">Fetch Image</button><br /><button type="button" id = "aion-chat-do-something-button">Do Something</button>
                      </p></p>';

    // Echo the custom button
    echo $custom_button;
}


// Hook the methods to the appropriate WordPress actions and filters
//add_filter('comment_text', ['AionChat\CommentButton', 'add_button_after_comment'], 10, 3);
//add_action('wp_footer', ['AionChat\CommentButton', 'my_custom_button_script']);