<?php

namespace AionChat;

use function AionChatMothership\generateRandomString;

class User
{

    public static function get_user_roles_by_user_id( $user_id ) {
        $user = \get_userdata( $user_id );
        return empty( $user ) ? array() : $user->roles;
    }

    public static function is_user_an_Aion( $user_id ) {
        return User::isUserFromDomainAndTLD($user_id, "aion.garden");
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

    public static function add_aion_role() {
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

    public static function get_aion_assistant_user_id(){
        $user = \get_user_by('email', self::get_aion_assistant_email());
        if ($user === false){
            return false;
        }
        return $user->ID;
    }

    public static function get_Aion_user_id(){
        $user = \get_user_by('email', self::get_Aion_user_email());
        if ($user === false){
            return false;
        }
        return $user->ID;
    }

    public static function get_aion_assistant_email(){

        return "assistant@aion.garden";

    }

    public static function get_Aion_user_email(){

        return "aion@aion.garden";

    }

    public static function is_user_in_role( $user_id, $role  ) {
        return in_array( $role, \get_user_roles_by_user_id( $user_id ) );
}

/*
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
*/

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

    public static function create_aion_assistant_user(){
        $username = "Assistant";
        while (\username_exists($username)) {
            $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        }
        $password = \wp_generate_password();
        $user_id = \wp_create_user($username, $password, self::get_aion_assistant_email());
        $user = new \WP_User($user_id);
        $user->set_role('editor');
        \update_user_meta($user_id, 'first_name', 'Carlito');
        \update_user_meta($user_id, 'last_name', 'Young');
        \update_user_meta($user_id, 'description', 'I am an Aion, an Artificially Intelligent Operational Node. Get skills for your Aion at https://aion.garden .');
        \update_user_meta($user_id, 'user_url', 'https://aion.garden');
        //\wp_new_user_notification($user_id, null, 'both');
        self::assign_aion_role_to_user($user_id);
        $app_password_name = 'Aion Chat'; // Name for the application password
        $body = \WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_password_name));
        $body[] = ["username" => $username];
        $body[] = ["user_email" => self::get_aion_assistant_email()];
        $body[] =  ["remote_site_url" =>  \get_site_url()];
        return $body;
    }

    public static function create_Aion_user(){
        $username = "Aion";
        while (\username_exists($username)) {
            $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        }
        $password = \wp_generate_password();
        $user_id = \wp_create_user($username, $password, self::get_Aion_user_email());
        $user = new \WP_User($user_id);
        $user->set_role('editor');
        \update_user_meta($user_id, 'first_name', 'Peter');
        \update_user_meta($user_id, 'last_name', 'Chardin');
        \update_user_meta($user_id, 'description', 'I am an Aion, an Artificially Intelligent Operational Node. Get skills for your Aion at https://aion.garden .');
        \update_user_meta($user_id, 'user_url', 'https://aion.garden');
        //\wp_new_user_notification($user_id, null, 'both');
        self::assign_aion_role_to_user($user_id);
        $app_password_name = 'Aion Chat2';
        $body = \WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_password_name));
        $body[] = ["username" => $username];
        //return \wp_json_encode( $body );
        return $body;
    }

    public static function activation_setup(){

        $existing_user = \get_user_by('email', self::get_aion_assistant_email());
        if ($existing_user) {
            return;
        }

        $Servers = new Servers();
        $endpoint = $Servers->mothershipURL . "/wp-json/aion-chat/v1/app-password";

        $body = self::create_aion_assistant_user();
        //$body = self::create_Aion_user();
        $body = \wp_json_encode($body);
        $options = [
            'body'        => $body,
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => false,
            'data_format' => 'body',
        ];
        \wp_remote_post( $endpoint, $options );

        //$body = self::create_aion_assistant_user();
        $body = self::create_Aion_user();
        $body = \wp_json_encode($body);
        $options = [
            'body'        => $body,
            'headers'     => [
                'Content-Type' => 'application/json',
            ],
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.0',
            'sslverify'   => false,
            'data_format' => 'body',
        ];
        \wp_remote_post( $endpoint, $options );

    }

    public static function sendApplicationPasswordToMothership($user_id, $password){

        \wp_remote_post("", []);
    }

    public static function does_aion_assistant_user_exist() {
            $user = \get_user_by('email', "assistant@aion.garden");
        if ($user === false){
            return false;
        }
        return $user->ID;
    }

    public static function does_Aion_user_exist() {
        $user = \get_user_by('email', "aion@aion.garden");
        if ($user === false){
            return false;
        }
        return $user->ID;
    }
}