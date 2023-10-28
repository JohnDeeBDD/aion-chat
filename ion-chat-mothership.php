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

use function IonChat\is_ion_mentioned;

require_once (plugin_dir_path(__FILE__). 'src/IonChatMothership/autoloader.php');

\add_filter('comment_flood_filter', '__return_false');
\add_filter('duplicate_comment_id', '__return_false');
//die("ionchatms");

global $IonChatProtocal;
$IonChatProtocal = "mothership";

//Connections::enable_ion_connections_cpt();
TrafficController::enable_prompt_incoming();
Comment::enable_interaction();
Email::enable_receiveing();



//Servers page
if (isset($_GET['ion-dev'])) {
    $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
    $IPs = json_decode($file);
    $dev1IP = $IPs[0];
    $dev2IP = $IPs[1];
    echo("<a href = 'http://$dev1IP/wp-admin/' target = '_blank'>Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/wp-admin' target = '_blank'>Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?option=ion-chat-log' target = '_blank'>ion-chat-log</a><br />");
    echo("<a href = 'http://$dev1IP/?option=ion-chat-protocol' target = '_blank'>Mothership Protocol</a><br />");
    echo("<a href = 'http://$dev2IP/?option=ion-chat-protocol' target = '_blank'>Remote Protocol</a><br />");
    echo("<a href = 'http://$dev1IP/?option=ion-chat-up-bus' target = '_blank'>Up Bus Mothership</a><br />");
    echo("<a href = 'http://$dev2IP/?option=ion-chat-up-bus' target = '_blank'>Up Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?option=curl_debug' target = '_blank'>Curl Debug</a><br />");
    echo("<a href = 'http://$dev1IP/?option=down_bus' target = '_blank'>Down Bus MS</a><br />");
    echo("<a href = 'http://$dev2IP/?option=down_bus' target = '_blank'>Down Bus Remote</a><br />");
    echo("<a href = 'http://$dev1IP/?option=debug_info' target = '_blank'>Debug</a><br />");
    die();
}

