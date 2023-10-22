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

use function IonChat\get_ion_user_id;
//die();

//require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChatMothership/autoloader.php");
require_once (plugin_dir_path(__FILE__). 'vendor/autoload.php');
//require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php");
\add_filter('comment_flood_filter', '__return_false');

\add_action("init", function(){
    //Connections::force_get_ion_connection_id("https://ioncity.ai");
   //echo (Connections::slave_remote_post(666, "https://ass"));
   //die();
});

global $IonChatProtocal;
$IonChatProtocal = "mothership";
\update_option('ion-chat-protocol', "mothership");

Connections::activate_ion_connections_cpt();
//$DevMode = new DevMode();

\add_action('ion_prompt_incoming', '\IonChatMothership\TrafficController::prompt_incoming', 10, 1);
\add_action('rest_api_init', function () {
    \register_rest_route(
        "ion-chat/v1",
        "ion-prompt",
        array(
            'methods' => ['POST', 'GET'],
            'callback' => function ($args) {
                $Prompt = \unserialize($args["prompt"]);
                \update_option("ion-chat-up-bus", $Prompt);

                //this function passes the prompt to ion_prompt_incoming on the next page load
                //\wp_schedule_single_event(time(), "ion_prompt_incoming", [$Prompt]);

                return \IonChatMothership\TrafficController::prompt_incoming($Prompt);

                //return "Prompt received. Status 200";
            },
            'permission_callback' => function () {
                return true;
            },
        )
    );
});

//\add_action('comment_post', 'IonChatMothership\comment_posted', 10, 1);

function comment_posted($comment_ID) {
    // Retrieve the comment object
    $comment = get_comment($comment_ID);

    // Retrieve the user_id of the comment author
    $user_id = $comment->user_id;

    // Check if the user is an "ion user"
    if (\IonChat\User::is_ion_user($user_id)) {
        return; // Exit the function if the user is an "ion user"
    }

    $Prompt = new Prompt();

    $Prompt->init_this_prompt($comment_ID, "created-on-mothership");


    //echo '<pre>';
    //\var_dump($Prompt);
    //echo '</pre>';die();

    $Prompt->response = $Prompt->send_self_to_ChatGPT();

    if (isset($Prompt->response['choices'][0]['message']['content'])) {
        $Prompt->response = $Prompt->response['choices'][0]['message']['content'];
    } else {
        $Prompt->response = \var_export($Prompt, true);
    }

    post_comment_to_post($user_id, $Prompt->post_id, $Prompt->response);
    return ($Prompt->response);
}

function post_comment_to_post($user_id, $post_id, $comment_content) {
    $Ion_user_id = \IonChat\User::get_ion_user_id();
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

//Servers page
if (isset($_GET['ion-dev'])) {
    $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
    $IPs = json_decode($file);
    $dev1IP = $IPs[0];
    $dev2IP = $IPs[1];
    echo("<a href = 'http://$dev1IP/wp-admin/' target = '_blank'>Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/wp-admin' target = '_blank'>Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?ion-chat-log=1' target = '_blank'>ion-chat-log</a><br />");
    echo("<a href = 'http://$dev1IP/?ion-chat-protocol=1' target = '_blank'>Mothership Protocol</a><br />");
    echo("<a href = 'http://$dev2IP/?ion-chat-protocol=1' target = '_blank'>Remote Protocol</a><br />");
    echo("<a href = 'http://$dev1IP/?ion-chat-up-bus=1' target = '_blank'>Up Bus Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/?ion-chat-up-bus=1' target = '_blank'>Up Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?curl_debug=1' target = '_blank'>Curl Debug</a><br />");
    echo("<a href = 'http://$dev1IP/?down_bus=1' target = '_blank'>Down Bus MS</a><br />");
    echo("<a href = 'http://$dev2IP/?down_bus=1' target = '_blank'>Down Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?debug_info=1' target = '_blank'>Debug</a><br />");
    die();
}

