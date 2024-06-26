<?php

namespace AionChat;

class Ping{

    public static function doPing($api_key){
            //this action is happening on the remote.
            global $AionChat_mothership_url;
            $response = \wp_remote_post($AionChat_mothership_url . "/wp-json/aion-chat/v1/ping", array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array(),
                    'body' => array(
                        'data' => \serialize([
                            'remote-site-url' => \site_url(),
                            'ion-api-key'   => $api_key
                        ]),
                    )
                )
            );
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: \AionChat\Ping27 $error_message";
                die();
            }
            return $response;

        }

}