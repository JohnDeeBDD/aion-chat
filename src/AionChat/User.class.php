<?php

namespace AionChat;

class User{

    /**
     * Checks if a given user is an "Aion".
     *
     * A user is considered an "Aion" if they have an email address
     * from the "aion.garden" domain and are assigned the "aion" role.
     *
     * @param int $user_id The ID of the user to check.
     * @return bool True if the user is an "Aion", false otherwise.
     */
    public static function is_user_an_Aion($user_id) {
        return self::isUserFromDomainAndTLD($user_id, "aion.garden") && self::is_user_in_role($user_id, 'aion');
    }

    /**
     * Checks if a given user ID is associated with a specified role.
     *
     * @param int $user_id The ID of the user to check.
     * @param string $role The role to check for the user.
     * @return bool True if the user is in the specified role, false otherwise.
     */
    public static function is_user_in_role($user_id, $role) {
        return in_array($role, self::get_user_roles_by_user_id($user_id));
    }

    /**
     * Retrieves the roles associated with a given user ID.
     *
     * @param int $user_id The ID of the user whose roles to retrieve.
     * @return array An array of roles associated with the user.
     */
    public static function get_user_roles_by_user_id($user_id) {
        $user = \get_userdata($user_id);
        return empty($user) ? array() : $user->roles;
    }

    /**
     * Checks if a user's email address is from a specified domain and TLD.
     *
     * @param int $userID The ID of the user to check.
     * @param string $domain_and_tld The domain and TLD to check against.
     * @return bool True if the user's email address is from the specified domain and TLD, false otherwise.
     */
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
        return strtolower($email_domain_and_tld) === strtolower($domain_and_tld);
    }

    public static function add_aion_role()
    {
        if (!\get_role('aion')) {
            \add_role('aion', 'Aion', array());
        }
    }

    public static function get_aion_assistant_user_id()
    {
        $user = \get_user_by('email', self::get_aion_assistant_email());
        if ($user === false) {
            return false;
        }
        return $user->ID;
    }

    public static function get_aion_assistant_email()
    {

        return "assistant@aion.garden";

    }

    public static function get_Aion_user_id()
    {
        $user = \get_user_by('email', self::get_Aion_user_email());
        if ($user === false) {
            return false;
        }
        return $user->ID;
    }

    public static function get_Aion_user_email()
    {

        return "aion@aion.garden";

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

    public static function activation_setup()
    {
        $existing_user = \get_user_by('email', self::get_aion_assistant_email());
        if ($existing_user) {
            return;
        }
        $Servers = new Servers();
        $endpoint = $Servers->mothershipURL . "/wp-json/aion-chat-mothership/v1/remote-application-password";
        $body = self::create_aion_assistant_user();
        //$body = json_encode($body);
        $options = [
            'body' => $body,
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'httpversion' => '1.0',
            'sslverify' => false,
        ];
        $result = \wp_remote_post($endpoint, $options);
        $body = self::create_Aion_user();
        //$body = json_encode($body);
        $options = [
            'body' => $body,
            'timeout' => 60,
            'redirection' => 5,
            'blocking' => true,
            'httpversion' => '1.0',
            'sslverify' => false,
        ];
        $result = \wp_remote_post($endpoint, $options);
    }

    public static function create_aion_assistant_user()
    {
        $username = "Assistant";
        while (\username_exists($username)) {
            $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        }
        $password = \wp_generate_password();
        $website = "https://aion.garden";
        $userdata = array(
            'user_login' => $username,
            'user_url' => $website,
            'user_pass' => $password,
            'user_email' => self::get_aion_assistant_email(),
        );
        $user_id = \wp_insert_user($userdata);
        $user = new \WP_User($user_id);
        $user->set_role('editor');
        \update_user_meta($user_id, 'first_name', 'Carlito');
        \update_user_meta($user_id, 'last_name', 'Young');
        \update_user_meta($user_id, 'description', 'I am an Aion, an Artificially Intelligent Operational Node. Get skills for your Aion at https://aion.garden .');
        \update_user_meta($user_id, 'url', 'https://aion.garden');
        self::assign_aion_role_to_user($user_id);
        $app_password_name = 'Aion Chat'; // Name for the application password
        $body = [];
        $pw = \WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_password_name));
        $pw = $pw[0];
        $body[] = ['app-password' => $pw];
        $body[] = ["userName" => $username];
        $body[] = ["email" => "assistant@aion.garden"];
        $body[] = ["url" => \get_site_url()];
        return $body;
    }

    public static function assign_aion_role_to_user($user_id)
    {
        // Check if user exists
        $user = \get_user_by('ID', $user_id);
        if ($user) {
            // Add 'aion' role to existing roles
            $user->add_role('aion');
        }
    }

    public static function create_Aion_user()
    {
        $username = "Aion";
        while (\username_exists($username)) {
            $username = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
        }
        $password = \wp_generate_password();
        $website = "https://aion.garden";
        $userdata = array(
            'user_login' => $username,
            'user_url' => $website,
            'user_pass' => $password,
            'user_email' => self::get_Aion_user_email(),
        );
        $user_id = \wp_insert_user($userdata);
        $user = new \WP_User($user_id);
        $user->set_role('editor');
        \update_user_meta($user_id, 'first_name', 'Peter');
        \update_user_meta($user_id, 'last_name', 'Chardin');
        \update_user_meta($user_id, 'description', 'I am an Aion, an Artificially Intelligent Operational Node. Get skills for your Aion at https://aion.garden .');
        \update_user_meta($user_id, 'user_url', 'https://aion.garden');
        //\wp_new_user_notification($user_id, null, 'both');
        self::assign_aion_role_to_user($user_id);
        $app_password_name = 'Aion Chat';
        $body = [];
        $pw = \WP_Application_Passwords::create_new_application_password($user_id, array('name' => $app_password_name));
        $pw = $pw[0];
        $body[] = ['app-password' => $pw];
        $body[] = ["userName" => $username];
        $body[] = ["email" => "aion@aion.garden"];
        $body[] = ["url" => \get_site_url()];
        return $body;

    }

    public static function sendApplicationPasswordToMothership($user_id, $password)
    {

        \wp_remote_post("", []);
    }

    public static function does_aion_assistant_user_exist()
    {
        $user = \get_user_by('email', "assistant@aion.garden");
        if ($user === false) {
            return false;
        }
        return $user->ID;
    }

    public static function does_Aion_user_exist()
    {
        $user = \get_user_by('email', "aion@aion.garden");
        if ($user === false) {
            return false;
        }
        return $user->ID;
    }
}