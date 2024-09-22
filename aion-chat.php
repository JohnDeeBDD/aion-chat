<?php
/*
Plugin Name: Aion Chat
Plugin URI: https://aion.garden
Description: The Singularity is here.
Version: 10
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2024, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace AionChat;

//die("AionChat");

global $AionChatProtocol;
$AionChatProtocol = "remote_node";

require_once(plugin_dir_path(__FILE__) . 'src/AionChat/autoloader.php');

global $Servers;
$Servers = new Servers();

//Setup WordPress filters to accommodate the plugin:
\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
\add_filter('wp_is_application_passwords_available', '__return_true');

//Admin Page:
\add_action('admin_menu', '\AionChat\Plugin::do_create_admin_page');

//Aion user role:
\add_action('init', '\AionChat\User::add_aion_role');

//Aion Conversation Custom Post Type:
Conversation::enable_aion_conversation_cpt();
Functions::enableFunctionCall();
ActionButtonManager::enable_custom_comment_buttons();

//Main plugin starting point is when someone makes a comment on an Aion conversation CPT:
\add_filter('preprocess_comment', '\AionChat\Comment::before_comment_post');
\add_action('comment_post', '\AionChat\Dialectic::converse', 5, 1);
\add_action('comment_post', '\AionChat\Comment::do_on_WordPress_action__comment_post', 10, 1);
\add_action('wp_enqueue_scripts', '\AionChat\Scripts::do_on_WordPress_action__wp_enqueue_scripts');


ExampleConversation::enablePublishExampleConversations();



\register_activation_hook(__FILE__, '\AionChat\ActivationHook::do_activation_hook');
require_once(plugin_dir_path(__FILE__) . 'src/update-checker/plugin-update-checker.php');
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
use function register_meta;

PucFactory::buildUpdateChecker(    'https://aion.garden/wp-content/uploads/details.json',__FILE__,'aion-chat');

add_action( 'init', 'AionChat\custom_register_post_meta' );

function custom_register_post_meta() {
    $args = [
        'auth_callback' => '__return_true',
        'type'          => 'boolean',
        'single'        => true,
        'show_in_rest'  => true,
    ];

    \register_meta( 'aion-conversation', '_aion_chat_system_instructions', $args );
}

\add_filter( 'is_protected_meta', '__return_false' );

if(isset($_GET['x'])){
    $x = Dialectic::is_dialectical_situation($_GET['x']);
    var_dump($x);die("xxxxxx");
}