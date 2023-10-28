<?php

namespace IonChat;

class ActivationHook{

    public static function enable(){
        \register_activation_hook(__FILE__, '\IonChat\ActivationHook::do_activation_hook');
    }

    public static function do_activation_hook(){
        $existing_user = \get_user_by('email', User::get_ion_email());
        if ($existing_user) {
            return;
        }
        $username = "Ion";
        if (\username_exists($username)) {
            $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        }
        $password = \wp_generate_password();
        $user_id = \wp_create_user($username, $password, User::get_ion_email());
        $user = new \WP_User($user_id);
        $user->set_role('subscriber');
        \update_user_meta($user_id, 'first_name', 'Carlton');
        \update_user_meta($user_id, 'last_name', 'Young');
        \update_user_meta($user_id, 'description', 'I am an Aion, named Ion, nice to meet you! Get a Aion for your website at https://ioncity.ai.');
        \update_user_meta($user_id, 'user_url', 'https://ioncity.ai');
        \wp_new_user_notification($user_id, null, 'both');
        User::assign_aion_role_to_user($user_id);

        return "New Aion user created";
    }
}