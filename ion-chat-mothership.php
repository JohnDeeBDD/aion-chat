<?php
/*
Plugin Name: Ion Chat Mothership
Plugin URI: https://generalchicken.guru
Description: The Singularity is near.
Version: 1.0
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

namespace IonChatMothership;

use IonChat\Prompt;
use function IonChat\get_ion_user_id;

require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChatMothership/autoloader.php");
require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php");

global $IonChatProtocal;
$IonChatProtocal = "mothership";
\update_option('ion-chat-protocol', "mothership");

Connections::activate_ion_connections_cpt();
$DevMode = new DevMode();

\add_action('comment_post', 'IonChatMothership\comment_posted', 10, 1);

function comment_posted($comment_ID) {
    // Retrieve the comment object
    $comment = get_comment($comment_ID);

    // Retrieve the user_id of the comment author
    $user_id = $comment->user_id;

    // Check if the user is an "ion user"
    if (\IonChat\is_ion_user($user_id)) {
        return; // Exit the function if the user is an "ion user"
    }

    $Prompt = new \IonChat\Prompt();
    $Prompt->init_this_prompt($comment_ID, "created-on-mothership");

    $Prompt->response = $Prompt->send_to_ChatGPT();

    if (isset($Prompt->response['choices'][0]['message']['content'])) {
        $Prompt->response = $Prompt->response['choices'][0]['message']['content'];
    } else {
        $Prompt->response = \var_export($Prompt, true);
    }

    post_comment_to_post($user_id, $Prompt->post_id, $Prompt->response);
    // Leave this in for debugging purposes
    \error_log(print_r($Prompt, true));
}

function post_comment_to_post($user_id, $post_id, $comment_content) {
    $Ion_user_id = \IonChat\get_ion_user_id();
    $comment_content = str_replace('```', '###TRIPLE_BACKTICK###', $comment_content);


    $comment_data = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => "Ion",
        'comment_content'      => $comment_content,
        'comment_type'         => '',
        'comment_parent'       => 0,
        'user_id'              => $Ion_user_id,
        'comment_date'         => current_time('mysql'),
        'comment_approved'     => 1,
    );

    // Insert the comment and get the comment ID
    $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        return true;
    } else {
        throw new Exception("An error occurred while posting the comment.");
    }
}