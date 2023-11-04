<?php

namespace IonChat;

class ActivationHook{

    public static function enable(){
        \register_activation_hook(__FILE__, '\IonChat\ActivationHook::do_activation_hook');
    }

    public static function do_activation_hook(){
        User::activation_setup();
        //self::doCreateIonHomePage();
    }

    private static function doCreateIonHomePage(){
        $my_post = array(
            'post_title'    => "Ion Home Page",
            'post_content'  => "Hi! I am an Aion named Ion.",
            'post_status'   => 'publish',
            'post_author'   => User::get_ion_user_id(),
            'post_type'     => 'aion-conversation'
        );
        \wp_insert_post( $my_post );
    }
}