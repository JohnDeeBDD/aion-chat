<?php

namespace IonChat;

use function IonChatMothership\generateRandomString;

class User
{

    public static function get_user_roles_by_user_id( $user_id ) {
        $user = \get_userdata( $user_id );
        return empty( $user ) ? array() : $user->roles;
    }

    public static function is_user_an_Aion( $user_id ) {
        $user_info = get_userdata($user_id);
        if ($user_info) {
            $email = $user_info->user_email;
            if ($email === self::get_ion_email()){
                return true;
            }
        }
        return false;
        //$role = "aion";
        //return in_array( $role, self::get_user_roles_by_user_id( $user_id ) );
    }

    public static function enable(){
        \add_action('init', '\IonChat\User::add_ion_role');
    }
    public static function add_ion_role() {
        if (!\get_role('aion')) {
            \add_role('aion', 'Aion', array());
        }
    }


    public static function assign_aion_role_to_user($user_id) {
        // Check if user exists
        $user = \get_user_by('ID', $user_id);
        if ($user) {
            // Add 'ion' role to existing roles
            $user->add_role('aion');
        }
    }
    public static function get_ion_user_id()
    {
        $user = \get_user_by('email', self::get_ion_email());
        return ($user->ID);
    }

    public static function get_ion_email(){

        return "ion@ioncity.ai";

    }


    public static function is_ion_user($user_id)
    {
        $user_info = \get_userdata($user_id);
        $user_email = $user_info->user_email;

        if ($user_email === self::get_ion_email()) {
            return true;
        } else {
            return false;
        }
    }

    public static function force_return_user_id(string $email): int
    {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Invalid email address.');
        }

        // Check if email belongs to a user
        $user = \get_user_by('email', $email);

        // If user exists, return user ID
        if ($user) {
            return $user->ID;
        }

        // Generate random screen name and password
        $random_screen_name = self::generateRandomString();
        $random_password = self::generateRandomString();

        // Create new user
        $user_id = \wp_create_user($random_screen_name, $random_password, $email);

        // Set user role to 'subscriber'
        $user = new \WP_User($user_id);
        $user->set_role('subscriber');

        // Return new user ID
        return $user_id;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}