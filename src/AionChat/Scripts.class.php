<?php

namespace AionChat;

class Scripts {

    public static function do_on_WordPress_action__wp_enqueue_scripts() {
        if (\is_singular("aion-conversation")) {

            // Register and enqueue the aion-conversation-cpt.js script
            \wp_register_script(
                'aion-conversation-cpt',
                \plugin_dir_url(__FILE__) . 'aion-conversation-cpt.js', // The JS file
                ['jquery', 'wp-api', 'heartbeat'],
                '1.0',
                true
            );
            \wp_enqueue_script('aion-conversation-cpt');

            // Retrieve the current post ID safely
            $post_id = get_queried_object_id();
            if ( $post_id && is_numeric( $post_id ) ) {

                // Localize script with nonce and post ID
                \wp_localize_script(
                    'aion-conversation-cpt',
                    'aion_chat_data',
                    array(
                        'nonce' => \wp_create_nonce("aion-chat-action"), // Security nonce
                        'post_id' => $post_id // Current post ID
                    )
                );
            }

            // Register and enqueue the aion-chat-dialectic.js script
            \wp_register_script(
                'aion-chat-dialectic',
                \plugin_dir_url(__FILE__) . 'aion-chat-dialectic.js', // The JS file
                ['jquery', 'wp-api', 'wp-api-fetch'],
                '1.0',
                true
            );
            \wp_enqueue_script('aion-chat-dialectic');
        }
    }
}
