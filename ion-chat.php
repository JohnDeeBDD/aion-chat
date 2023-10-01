<?php
/*
Plugin Name: Ion Chat
Plugin URI: https://generalchicken.guru
Description: The Singularity is near.
Version: 1.0
Author: johndee
Author URI: https://generalchicken.guru
License: Copyright(C) 2023, generalchicken.guru . All rights reserved. THIS IS NOT FREE SOFTWARE.
*/

//die("ion");

//update_option('ion-chat', "");
function isIonUser($user_id){
    return true;
}

add_action( 'better_messages_message_sent', 'on_message_sent', 10, 1 );

function on_message_sent( $message ){
    // Sender ID
    $user_id = (int) $message->sender_id;

    //log_data_to_option("inside on_message_sent 24" );

    // Conversation ID
    $thread_id = $message->thread_id;

    // Message ID
    $message_id = $message->id;

    // Message Content
    $content = $message->message;
    if($user_id === 1){
        getIonReply(1);
    }
}

function log_data_to_option($data){
    $db = get_option('ion-chat');
    update_option('ion-chat', $db . $data . "<br />" . rand() .  "<br />");
}

function getIonReply($thread_id){

    $api_key = get_api_key(123/* to do! */);
    $message_thread_ids_array = returnArrayOfMessagesThread(1/* to do! $thread_id*/);
    $compiled_messages = compile_messages_for_transport_to_ChatGPT($message_thread_ids_array);
    $response = sendToChatGPT($compiled_messages, $api_key);
    doPutMessageInDB(3, 1, $response["choices"][0]["message"]["content"]);

}

function compile_messages_for_transport_to_ChatGPT($messageIDs, $conversationInitiation = []) {
    $conversationInitiation = [
//        ['role' => 'system', 'content' => 'You are a chatbot on a website.'],
        ['role' => 'system', 'content' => 'You are a helpful assistant.']
    ];
    global $wpdb; // This is the WordPress database object

    // Check if $messageIDs is an array and not empty
    if (!is_array($messageIDs) || empty($messageIDs)) {
        return false;
    }

    // Convert the message IDs to a comma-separated string
    $ids = implode(',', array_map('intval', $messageIDs)); // Ensure the IDs are integers for security

    // Query the database to get the messages
    $query = "SELECT * FROM {$wpdb->prefix}bm_message_messages WHERE id IN ($ids) ORDER BY date_sent ASC";
    $messages = $wpdb->get_results($query);

    // Initialize the result array with the conversation initiation messages
    $result = $conversationInitiation;

    // Loop through the queried messages and format them
    foreach ($messages as $message) {
        $result[] = [
            'role' => 'user', // Assuming all messages in the database are from users. Adjust as needed.
            'content' => $message->message
        ];
    }

    // Convert the result array to an object
    $objectToSend = (object) $result;

    return $objectToSend;
}

function doPutMessageInDB($sender_id, $thread_id, $content){
    $message_id = Better_Messages()->functions->new_message([
        'sender_id'    => $sender_id,
        'thread_id'    => $thread_id,
        'content'      => $content,
        'return'       => 'message_id',
        'error_type'   => 'wp_error'
    ]);
    if ( is_wp_error( $message_id ) ) {$error = $message_id->get_error_message();}
}

function generateInstructions(){
    return ['system', 'You are a helpful a.i. assistant named "Ion".'];
}

function sendToChatGPT($messages, $api_key, $functions = null) {
    // OpenAI API endpoint for ChatGPT
    $url = "https://api.openai.com/v1/chat/completions";

    // Ensure messages is an array
    $messages = is_object($messages) ? (array) $messages : $messages;

    // Prepare the data for the request
    $data = [
        "model" => "gpt-3.5-turbo-0613",
        'messages' => array_values($messages), // Convert to indexed array
        'max_tokens' => 150 // You can adjust this as needed
    ];
    $functions = [
        [
            "name" => "send_user_php",
            "description" => "Send user PHP related data or tasks",
            "parameters" => [
                "type" => "object",
                "properties" => [
                    "php_code" => [
                        "type" => "string",
                        "description" => "The PHP code or script to be sent",
                    ],
                    "action" => [
                        "type" => "string",
                        "enum" => ["execute", "analyze", "store"],
                        "description" => "The action to be performed on the provided PHP code",
                    ],
                ],
                "required" => ["php_code", "action"],
            ],
        ]
    ];


    // If functions are provided, add them to the data
    if ($functions !== null) {
        $data['functions'] = $functions;
    }

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        'Content-Type: application/json'
    ]);

    // Execute cURL session and get the response
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Decode the response
    return json_decode($response, true);
}

global $functions;
$functions = [
    createFunctionMetadata(
        "get_current_weather",
        "Get the current weather in a given location",
        [
            "type" => "object",
            "properties" => [
                "location" => [
                    "type" => "string",
                    "description" => "The city and state, e.g. San Francisco, CA",
                ],
                "unit" => [
                    "type" => "string",
                    "enum" => ["celsius", "fahrenheit"]
                ],
            ],
            "required" => ["location"],
        ]
    )
];

function createFunctionMetadata($name, $description, $parameters) {
    return [
        "name" => $name,
        "description" => $description,
        "parameters" => $parameters
    ];
}


function transformNicename($niceName){
    //return $niceName;
    if($niceName === "ion"){
        $niceName = "assistant";
    }else{
        $niceName = "user";
    }
    return $niceName;
}

/**
 * Retrieves an array of message IDs from a given thread.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param int $thread_id The ID of the thread for which to retrieve messages. Default is 1.
 * @param int $last The maximum number of message IDs to retrieve. Default is 1000.
 *
 * @return int[] An array of message IDs.
 */

function returnArrayOfMessagesThread($thread_id = 1, $last = 1000): array {
    global $wpdb;

    // Define the table name
    $table_name = 'wp_bm_message_messages';

    // Prepare the SQL query
    $sql = $wpdb->prepare(
        "SELECT id FROM $table_name WHERE thread_id = %d ORDER BY date_sent DESC LIMIT %d",
        $thread_id,
        $last
    );

    // Execute the query and retrieve the results
    $results = $wpdb->get_col($sql);
    return array_map('intval', $results);
}

function get_api_key($user_id){
    return "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";
    //return (\get_option("ion-api-kay"));
}

// Hook to run the function upon plugin activation
register_activation_hook(__FILE__, 'create_general_chicken_connections_table');

function create_general_chicken_connections_table() {
    global $wpdb;

    // Table name with WP prefix
    $table_name = $wpdb->prefix . 'general_chicken_connections';

    // Check if the table already exists
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

        // SQL to create the table
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            connection VARCHAR(255) NOT NULL,
            remote_id INT NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function get_local_id($connection_url, $remote_id) {
    global $wpdb;

    // Table name with WP prefix
    $table_name = $wpdb->prefix . 'general_chicken_connections';

    // Query the database
    $local_id = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table_name WHERE connection = %s AND remote_id = %d",
        $connection_url,
        $remote_id
    ));

    return $local_id ? $local_id : null; // Return the local id if found, otherwise return null
}

function get_remote_id($connection_url, $local_id) {
    global $wpdb;

    // Table name with WP prefix
    $table_name = $wpdb->prefix . 'general_chicken_connections';

    // Query the database
    $remote_id = $wpdb->get_var($wpdb->prepare(
        "SELECT remote_id FROM $table_name WHERE connection = %s AND id = %d",
        $connection_url,
        $local_id
    ));

    return $remote_id ? $remote_id : null; // Return the remote id if found, otherwise return null
}

function createConnection($connectionUrl, $remote_id) {
    global $wpdb;

    // Table name with WP prefix
    $table_name = $wpdb->prefix . 'general_chicken_connections';

    // Insert the data into the table
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'connection' => $connectionUrl,
            'remote_id'  => $remote_id
        ),
        array(
            '%s', // Format for connection (string)
            '%d'  // Format for remote_id (integer)
        )
    );

    // If insertion was successful, return the ID of the newly created record
    if ($inserted) {
        return $wpdb->insert_id;
    }

    // If there was an error, return null or handle the error as needed
    return null;
}

if(isset($_GET['c'])){
    add_action('init', function(){
        $api_key = get_api_key(123/* to do! */);
        $message_thread_ids_array = returnArrayOfMessagesThread(1/* to do! $thread_id*/);
        $compiled_messages = compile_messages_for_transport_to_ChatGPT($message_thread_ids_array);
        //var_dump($compiled_messages);die("compiled messages");
        $response = sendToChatGPT($compiled_messages, $api_key);

        doPutMessageInDB(3, 1, $response["choices"][0]["message"]["content"]);
    });
}

if(isset($_GET['b'])){
    var_dump(\get_option("ion-chat"));die();
}