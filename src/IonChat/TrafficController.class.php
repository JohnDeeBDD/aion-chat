<?php

namespace IonChat;

class TrafficController
{

    public static function routePrompt(Prompt $Prompt){
        $Prompt->status;
     //   \wp_schedule_single_event( time(), "ion_prompt_incoming", [$args["prompt"]]);
    }

public static function activation_setup_db(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'ion_chat_connections';

    try {
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $wpdb->query("DROP TABLE $table_name");
        }
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Unknown table') === false) {
            throw $e;
        }
    }

    // SQL to create the table
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    connection varchar(255) NOT NULL,
    PRIMARY KEY  (id)
) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


    /**
     * Retrieve the ID of a connection based on the connection string.
     *
     * @param string $connection The connection string.
     * @return int|null The ID of the connection or null if not found.
     */
    public static function get_connection_id(string $connection) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_connections';

        $sql = $wpdb->prepare("SELECT id FROM $table_name WHERE connection = %s", $connection);
        $id = $wpdb->get_var($sql);

        return $id ? intval($id) : null;
    }

    /**
     * Retrieve the connection string based on the ID.
     *
     * @param int $id The ID of the connection.
     * @return string|null The connection string or null if not found.
     */
    public static function get_connection(int $id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_connections';

        $sql = $wpdb->prepare("SELECT connection FROM $table_name WHERE id = %d", $id);
        $connection = $wpdb->get_var($sql);

        return $connection ? $connection : null;
    }
    
    /**
     * Insert a new connection string into the database.
     *
     * @param string $connection The connection string.
     * @return int The ID of the newly created connection.
     */
    public static function create_connection(string $connection) : int {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ion_chat_connections';

        // Insert the connection into the database
        $wpdb->insert($table_name, ['connection' => $connection]);

        // Return the ID of the newly created connection
        return intval($wpdb->insert_id);
    }

}