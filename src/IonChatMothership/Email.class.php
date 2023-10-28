<?php

namespace IonChatMothership;

class Email{

    public static function enable_receiveing(){
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
    }
}