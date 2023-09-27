<?php
/*
Plugin Name: Ion Chat
Plugin URI: https://generalchicken.guru
Description: The Singularity is near.
Version: 1.0
Author: johndee
Author URI: http://URI_Of_The_Plugin_Author
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/


die("IONCHAT!");
use IonChat\IonUser;

require __DIR__ . '/vendor/autoload.php';
require_once (plugin_dir_path(__FILE__) . "/src/IonChat/Ion.php");
require_once (plugin_dir_path(__FILE__) . "/src/IonChat/IonMessage.php");
require_once (plugin_dir_path(__FILE__) . "/src/IonChat/IonUser.php");

$Ion = new \IonChat\Ion;
$Ion->enableChatWordPressSitesW_BetterMessagesPlugin();
$Ion->enableConnections();

// set ?IonMessage=1 to see the message
if(isset($_GET['IonMessage'])){
    //\update_site_option("nope", "nope");
    echo ( \var_export ( \get_site_option('IonMessage')) );
    die();
}


// Hook to replace the standard WP_User with IonUser

add_filter('wp_user', 'replace_wp_user', 10, 2);