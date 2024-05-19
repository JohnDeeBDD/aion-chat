<?php

namespace AionChat;

class ActivationHook{


    public static function enable(){
        \register_activation_hook(__FILE__, '\AionChat\ActivationHook::do_activation_hook');
    }

    public static function do_activation_hook(){
        //Ping::doPing(User::activation_setup());
        User::activation_setup();
        //self::doCreateIonHomePage();
        //self::deployPosts();
       // self::pingMothership();
      //  self::setModeVariables("remote_node");
    }
}