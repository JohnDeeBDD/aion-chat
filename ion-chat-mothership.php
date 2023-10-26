<?php
/*
Plugin Name: Ion Chat Mothership
Plugin URI: https://generalchicken.guru
Description: The Singularity is here.
Version: 1.0
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/


namespace IonChatMothership;

require_once (plugin_dir_path(__FILE__). 'src/IonChatMothership/autoloader.php');

\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
//die("ionchatms");

global $IonChatProtocal;
$IonChatProtocal = "mothership";

Connections::activate_ion_connections_cpt();

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

\add_action('rest_api_init', function () {
    \register_rest_route(
        "ion-chat/v1",
        "email",
        array(
            'methods' => ['POST', 'GET'],
            'callback' => function ($args) {
                $my_post = array(
                    'post_title'    => "Incoming Email",
                    'post_content'  => \var_export($args, true),
                    'post_status'   => 'draft',
                    'post_author'   => \IonChat\User::get_ion_user_id()
                );
                \wp_insert_post( $my_post );
                return "Gotcha.";
            },
            'permission_callback' => function () {
                return true;
            },
        )
    );
});




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

