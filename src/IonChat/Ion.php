<?php

namespace IonChat;

class Ion
{

    public $mothershipUrl = "http://18.220.165.172";
    public $route = '/wp-json/ion/message';
    public $applicationPassword;

    public static function areHumansStuttering($thread_id): bool {
        return false;
    }

    public static function getIons($thead_id): array {
        return [3];
    }

    public static function enableChatWordPressSitesW_BetterMessagesPlugin(): bool
    {
        \add_action('better_messages_message_sent', function ($message) {
            $IonMessage = new IonMessage();
            $IonMessage->sendToMothership($message);
        }, 10, 1);
        return true;
    }

    public function enableConnections(): bool{
        \add_action('init', function () {
            // Check if the current user is an admin
            if (\current_user_can('activate_plugins')) {
                // Registering the custom post type
                \register_post_type('ion-connection',
                    array(
                        'labels' => array(
                            'name' => __('Connections'),
                            'singular_name' => __('Connection')
                        ),
                        'public' => true,
                        'has_archive' => true,
                        'supports' => array('title', 'editor', 'thumbnail'),
                        'show_in_menu' => true,
                    )
                );
            }
        });
    }
}