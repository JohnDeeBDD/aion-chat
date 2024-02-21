<?php

namespace AionChatMothership;

class Ping{


    public static function enableReceivePing(){
        \add_action('ion_chat_ping_incoming', '\AionChatMothership\Ping::ping_incoming', 10, 1);
        \add_action('rest_api_init', function () {
            \register_rest_route(
                "aion-chat/v1",
                "ping",
                array(
                    'methods' => ['POST', 'GET'],
                    'callback' => function ($args) {
                        return \AionChatMothership\Ping::ping_incoming($args);
                    },
                    'permission_callback' => function () {
                        return true;
                    },
                )
            );
        });
    }

    public static function ping_incoming($args){
     //   \update_user_meta( \AionChat\User::get_ion_user_id(), "aion-chat-ping", var_export($args, true));
    }


}