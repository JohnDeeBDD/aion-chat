<?php

namespace IonChat;

interface ITrafficController
{
    public static function link_local_remote_threads(int $local_thread_id, int $remote_thread_id, int $connection_id): int;

    public static function get_remote_thread_id(int $local_thread_id, int $connection_id);

    public static function get_local_thread_id(int $remote_thread_id, int $connection_id);

    public static function activation_setup_db();

    public static function get_connection_id(string $connection);

    public static function get_connection(int $id);

    public static function create_connection(string $connection): int;
}

class TrafficController
{
    public static function prompt_incoming(Prompt $Prompt)
    {
        $Prompt->status = "received on mothership";
        $Prompt->remote_comment_id = $Prompt->comment_id;
        unset($Prompt->comment_id);
        $Prompt->remote_post_id = $Prompt->post_id;
        unset($Prompt->post_id);
        $Prompt->remote_user_id = $Prompt->user_id;
        unset($Prompt->user_id);
        $connection_id = TrafficController::get_connection_id($Prompt->remote_connection_domain_url) ?? TrafficController::create_connection($Prompt->remote_connection_domain_url);
        $sender_id = force_return_user_id($Prompt->user_email);
        $Prompt->user_id = $sender_id;
        if(TrafficController::get_local_thread_id($Prompt->remote_post_id, $connection_id) === null){
            $ion_user_id = get_ion_user_id();
            $user_ids = [
                $ion_user_id,
                $Prompt->user_id
            ];
            $subject = 'Conversation Subject';
            $Prompt->post_id = Better_Messages()->functions->create_new_conversation( $user_ids, $subject );
            TrafficController::link_local_remote_threads($Prompt->post_id, $Prompt->remote_post_id, $connection_id);
        }else{
            $Prompt->post_id = TrafficController::get_local_thread_id($Prompt->remote_post_id, $connection_id);
        }
        $thread_id = $Prompt->post_id;
        $content = $Prompt->comment_content;

        \update_option('ion-chat-up-bus', ($Prompt));

        //Set api key:
        if(!(isset($Prompt->OpenAI_api_key))){
            $Prompt->OpenAI_api_key = \get_option("openai-api-key");
        }

        $response = $Prompt->send_to_ChatGPT();
        $Prompt->response = $response;
        $Prompt->status = "GPT_replied";
        if(isset($response["choices"][0]["message"]["content"])){
            $response = $response["choices"][0]["message"]["content"];
        }else{
            $response = \var_export($response, true);
        }

        $ion_response_message_id = \Better_Messages()->functions->new_message([
            'sender_id' => get_ion_user_id(),
            'thread_id' => $thread_id,
            'content' => $response,
            //'return' => 'message_id',
            'error_type' => 'wp_error'
        ]);

        $message_id = \Better_Messages()->functions->new_message([
            'sender_id' => $sender_id,
            'thread_id' => $thread_id,
            'content' => $content,
           // 'return' => 'message_id',
            'error_type' => 'wp_error'
        ]);

        $Prompt->send_down();
        \update_option('down_bus', ($Prompt));
        if (\is_wp_error($message_id)) {
            $error = $message_id->get_error_message();
            \update_option('down_bus', ($Prompt));

        }

    }

    /**
     * Retrieve the ID of a connection based on the connection string.
     *
     * @param string $connection The connection string.
     * @return int|null The ID of the connection or null if not found.
     */
    public static function get_connection_id(string $connection)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_connections';

        $sql = $wpdb->prepare("SELECT id FROM $table_name WHERE connection = %s", $connection);
        $id = $wpdb->get_var($sql);

        return $id ? intval($id) : null;
    }

    /**
     * Insert a new connection string into the database.
     *
     * @param string $connection The connection string.
     * @return int The ID of the newly created connection.
     */
    public static function create_connection(string $connection): int
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_connections';

        // Insert the connection into the database
        $wpdb->insert($table_name, ['connection' => $connection]);

        // Return the ID of the newly created connection
        return intval($wpdb->insert_id);
    }

    /**
     * Link a local thread ID with a remote thread ID for a given connection.
     *
     * @param int $local_thread_id The ID of the local thread.
     * @param int $remote_thread_id The ID of the remote thread.
     * @param int $connection_id The ID of the connection.
     * @return int The ID of the newly created thread link.
     */
    public static function link_local_remote_threads(int $local_thread_id, int $remote_thread_id, int $connection_id): int
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ion_chat_thread_links';

        // Insert the thread link into the database
        $wpdb->insert($table_name, [
            'connection_id' => $connection_id,
            'local_thread_id' => $local_thread_id,
            'remote_thread_id' => $remote_thread_id
        ]);

        // Return the ID of the newly created thread link
        return intval($wpdb->insert_id);
    }

    /**
     * Retrieve the remote thread ID based on a local thread ID and connection.
     *
     * @param int $local_thread_id The ID of the local thread.
     * @param int $connection_id The ID of the connection.
     * @return int|null The ID of the remote thread or null if not found.
     */
    public static function get_remote_thread_id(int $local_thread_id, int $connection_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_thread_links';

        $sql = $wpdb->prepare("SELECT remote_thread_id FROM $table_name WHERE local_thread_id = %d AND connection_id = %d", $local_thread_id, $connection_id);
        $remote_thread_id = $wpdb->get_var($sql);

        return $remote_thread_id ? intval($remote_thread_id) : null;
    }

    /**
     * Retrieve the local thread ID based on a remote thread ID and connection.
     *
     * @param int $remote_thread_id The ID of the remote thread.
     * @param int $connection_id The ID of the connection.
     * @return int|null The ID of the local thread or null if not found.
     */
    public static function get_local_thread_id(int $remote_thread_id, int $connection_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_thread_links';

        $sql = $wpdb->prepare("SELECT local_thread_id FROM $table_name WHERE remote_thread_id = %d AND connection_id = %d", $remote_thread_id, $connection_id);
        $local_thread_id = $wpdb->get_var($sql);

        return $local_thread_id ? intval($local_thread_id) : null;
    }

    public static function activation_setup_db()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Check and drop existing tables
        $table_name_connections = $wpdb->prefix . 'ion_chat_connections';
        $table_name_thread_links = $wpdb->prefix . 'ion_chat_thread_links';

        $wpdb->query("DROP TABLE IF EXISTS $table_name_connections");
        $wpdb->query("DROP TABLE IF EXISTS $table_name_thread_links");

        // Create the 'ion_chat_connections' table
        $sql_connections = "CREATE TABLE $table_name_connections (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    connection varchar(255) NOT NULL,
    PRIMARY KEY  (id)
) $charset_collate;";

        // Create the 'ion_chat_thread_links' table
        $sql_thread_links = "CREATE TABLE $table_name_thread_links (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    connection_id mediumint(9) NOT NULL,
    local_thread_id mediumint(9) NOT NULL,
    remote_thread_id mediumint(9) NOT NULL,
    PRIMARY KEY  (id)
) $charset_collate;";

        require_once(\ABSPATH . 'wp-admin/includes/upgrade.php');
        \dbDelta($sql_connections);
        \dbDelta($sql_thread_links);
    }

    /**
     * Retrieve the connection string based on the ID.
     *
     * @param int $id The ID of the connection.
     * @return string|null The connection string or null if not found.
     */
    public static function get_connection(int $id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_connections';

        $sql = $wpdb->prepare("SELECT connection FROM $table_name WHERE id = %d", $id);
        $connection = $wpdb->get_var($sql);

        return $connection ? $connection : null;
    }

    public static function on_message_sent($bm_message){
        global $IonChatProtocal;
        if (
            TrafficController::do_user_ids_constitute_ion_conversation(Better_Messages()->functions->get_recipients_ids($bm_message->thread_id))
            and !(is_ion_user($bm_message->sender_id)
            and $IonChatProtocal === "remote_node")
        ){
                $Prompt = new Prompt();
                $Prompt->account_user_id = (int)$bm_message->sender_id;
                $Prompt->status = "send_up";
                $Prompt->post_id = (int)$bm_message->thread_id;
                $Prompt->user_id = (int)$bm_message->sender_id;
                $Prompt->comment_id = (int)$bm_message->id;
                $Prompt->comment_content = (string)$bm_message->message;
                $Prompt->user_email = (\get_userdata((int)$bm_message->sender_id))->user_email;
                $Prompt->remote_connection_domain_url = \site_url();
                $Prompt->compile_ion_messages_from_bm_thread($bm_message->thread_id);
                $Prompt->send_up();
        }


        if (
            TrafficController::do_user_ids_constitute_ion_conversation(Better_Messages()->functions->get_recipients_ids($bm_message->thread_id))
            and !(is_ion_user($bm_message->sender_id)
                and $IonChatProtocal === "mothership")
        ){
            $Prompt = new Prompt();

            $Prompt->OpenAI_api_key = \get_option('openai-api-key');
            $Prompt->account_user_id = (int)$bm_message->sender_id;
            $Prompt->status = "chat_on_ms";
            $Prompt->post_id = (int)$bm_message->thread_id;
            $Prompt->user_id = (int)$bm_message->sender_id;
            $Prompt->comment_id = (int)$bm_message->id;
            $Prompt->comment_content = (string)$bm_message->message;
            $Prompt->user_email = (\get_userdata((int)$bm_message->sender_id))->user_email;
            //$Prompt->remote_connection_domain_url = \site_url();
            $Prompt->compile_ion_messages_from_bm_thread($bm_message->thread_id);
            $response = $Prompt->send_to_ChatGPT();
            $Prompt->response = $response;
            $Prompt->status = "GPT_replied";
            if(isset($response["choices"][0]["message"]["content"])){
                $response = $response["choices"][0]["message"]["content"];
            }else{
                $response = \var_export($response, true);
            }

            $ion_response_message_id = \Better_Messages()->functions->new_message([
                'sender_id' => get_ion_user_id(),
                'thread_id' => $Prompt->post_id,
                'content' => $response,
                //'return' => 'message_id',
                'error_type' => 'wp_error'
            ]);

        }
    }

    public static function do_user_ids_constitute_ion_conversation($user_ids)
    {
        if (is_array($user_ids) && count($user_ids) === 2) {
            $isIonUserFound = false;
            foreach ($user_ids as $id) {
                if (!is_int($id)) {
                    return false;
                }
                if (TrafficController::isIonUser($id)) {
                    $isIonUserFound = true;
                }
            }
            return $isIonUserFound;
        }
        return false;
    }

    public static function isIonUser($user_id)
    {
        $user_info = get_userdata($user_id);
        $user_email = $user_info->user_email;

        if ($user_email === "jiminac@aol.com") {
            return true;
        } else {
            return false;
        }
    }
}