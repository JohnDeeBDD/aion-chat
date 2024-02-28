<?php

namespace AionChat;

use function AionChatMothership\generateRandomString;

class User
{

    public static function doPingMothershipForIon(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 50; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;


    }

    public static function get_user_roles_by_user_id( $user_id ) {
        $user = \get_userdata( $user_id );
        return empty( $user ) ? array() : $user->roles;
    }

    public static function is_user_an_Aion( $user_id ) {
        return User::isUserFromDomainAndTLD($user_id, "aion.garden");
        //return User::isUserFromDomainAndTLD($user_id, "ioncity.ai");
    }

    public static function isUserFromDomainAndTLD($userID, $domain_and_tld) {
        // Get user data by user ID
        $user_info = \get_userdata($userID);
        if (!$user_info) {
            // Return false if user does not exist
            return false;
        }

        // Extract the email address from user data
        $user_email = $user_info->user_email;

        // Parse the email to extract its domain and TLD
        $email_domain_and_tld = substr(strrchr($user_email, "@"), 1);

        // Compare the email's domain and tld with the input parameters
        if (strtolower($email_domain_and_tld) === strtolower($domain_and_tld)) {
            return true;
        } else {
            return false;
        }
    }

    public static function enable(){
        \add_action('init', '\AionChat\User::add_ion_role');
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

    public static function get_aion_assistant_user_id()
    {
        $user = \get_user_by('email', self::get_aion_assistant_email());
        return ($user->ID);
    }

    public static function get_aion_assistant_email(){

        return "assistant@aion.garden";

    }

    public static function is_user_in_role( $user_id, $role  ) {
        return in_array( $role, \get_user_roles_by_user_id( $user_id ) );
}

    public static function is_ion_user($user_id)
    {
        $user_info = \get_userdata($user_id);
        $user_email = $user_info->user_email;

        if ($user_email === self::get_aion_assistant_email()) {
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

    public static function activation_setup(){

        $existing_user = \get_user_by('email', self::get_aion_assistant_email());
        if ($existing_user) {
            return;
        }
        $username = "Assistant";
        if (\username_exists($username)) {
            $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        }
        $password = \wp_generate_password();
        $user_id = \wp_create_user($username, $password, self::get_aion_assistant_email());
        $user = new \WP_User($user_id);
        $user->set_role('editor');
        \update_user_meta($user_id, 'first_name', 'Carlito');
        \update_user_meta($user_id, 'last_name', 'Young');
        \update_user_meta($user_id, 'description', 'I am an Aion, an Artificially Intelligent Operational Node. Get skills for your Aion at https://aion.garden .');
        \update_user_meta($user_id, 'user_url', 'https://aion.garden');
        \wp_new_user_notification($user_id, null, 'both');
        self::assign_aion_role_to_user($user_id);
        // Generate Application Password
        $app_password_name = 'Aion Chat'; // Name for the application password
        $new_app_password = \WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_password_name));


        return \var_export($new_app_password, true);

    }

    public static function sendApplicationPasswordToMothership($user_id, $password){

        \wp_remote_post("", []);
    }
}