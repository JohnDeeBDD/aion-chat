<?php

namespace AionChat;

class Scripts{

    public static function do_on_WordPress_action__wp_enqueue_scripts(){
        if (\is_singular("aion-conversation")) {

            \wp_register_script(
                'aion-conversation-cpt',
                \plugin_dir_url(__FILE__) . 'aion-conversation-cpt.js', // here is the JS file
                ['jquery', 'wp-api', 'heartbeat'],
                '1.0',
                true
            );
            \wp_enqueue_script('aion-conversation-cpt');
            \wp_localize_script( 'aion-conversation-cpt', 'aion_chat_nonce',
                array(
                    'nonce' => \wp_create_nonce("aion-chat-action"),
                )
            );

        }
    }
}