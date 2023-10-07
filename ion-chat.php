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

namespace IonChat;

//die("IonChat!");

require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php");
require_once("/var/www/html/wp-content/plugins/ion-chat/src/action-scheduler/action-scheduler.php");

\add_action('ion_prompt_incoming', 'IonChat\prompt_incoming ', 10, 1);
\add_action('rest_api_init', function () {
    \register_rest_route(
        "ion-chat/v1",
        "ion-prompt",
        array(
            'methods' => ['POST', 'GET'],
            'callback' => function($args){
                \wp_schedule_single_event( time(), "ion_prompt_incoming", [$args["prompt"]]);
                return true;
            },
            'permission_callback' => function () {
                return true;
            },
        )
    );
});

function prompt_incoming($Prompt){
    TrafficController::routePrompt($Prompt);
}

if (isset($_GET['z'])) {
    echo '<pre>';
   // \update_option('z', 'wtf');
    \var_dump(\get_option("z"));
    echo '</pre>';
    die();
}

\add_action('ion_prompt_incoming', 'IonChat\prompt_incoming', 10, 1);

\add_action('better_messages_message_sent', 'IonChat\on_message_sent', 10, 1);

// Hook to run the function upon plugin activation
\register_activation_hook(__FILE__, 'IonChat\activate_ion_chat');

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

function compile_messages_for_transport($messageIDs, $conversationInitiation = [])
{
    log_data_to_option($messageIDs, "compile_messages_for_transport_to_ChatGPT");
    $conversationInitiation = [
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
    $objectToSend = (object)$result;

    return $objectToSend;
}

function createFunctionMetadata($name, $description, $parameters)
{
    return [
        "name" => $name,
        "description" => $description,
        "parameters" => $parameters
    ];
}

function doPutMessageInDB($sender_id, $thread_id, $content)
{
    $message_id = Better_Messages()->functions->new_message([
        'sender_id' => $sender_id,
        'thread_id' => $thread_id,
        'content' => $content,
        'return' => 'message_id',
        'error_type' => 'wp_error'
    ]);
    if (is_wp_error($message_id)) {
        $error = $message_id->get_error_message();
    }
}

function generateInstructions()
{
    return ['system', 'You are a helpful a.i. assistant named "Ion".'];
}

function get_api_key($user_id)
{
    return \file_get_contents("/var/www/html/wp-content/plugins/ion-chat/api_key.txt");
}

function getIonReply($thread_id)
{

    $userInThread = Better_Messages()->functions->get_recipients_ids($thread_id);

    //Only support for two chatters so far!
    if (!(\count($userInThread)) === 2) {
        return;
    }

    $noIons = true;
    foreach ($userInThread as $user_id) {
        if (isIonUser($user_id)) {
            $noIons = false;
        }
    }

    if ($noIons) {
        return;
    }

    $api_key = get_api_key(123/* to do! */);
    log_data_to_option($api_key, "api key");
    $message_thread_ids_array = returnArrayOfMessagesThread($thread_id);
    log_data_to_option($message_thread_ids_array, "message thread ids array");
    $compiled_messages = compile_messages_for_transport($message_thread_ids_array);
    $response = sendToChatGPT($compiled_messages, $api_key);
    log_data_to_option($response, "response from GPT");

    if (isset($response["choices"][0]["message"]["content"])) {
        $response = $response["choices"][0]["message"]["content"];
    } else {
        $response = \var_export($response, true);
    }
    doPutMessageInDB(3, $thread_id, $response);

}

function isIonUser($user_id)
{
    $user_info = get_userdata($user_id);
    $user_email = $user_info->user_email;

    if ($user_email === "jiminac@aol.com") {
        return true;
    } else {
        return false;
    }
}

function log_data_to_option($data, $tag = "tag")
{
    $db = get_option('ion-chat');
    \update_option('ion-chat', $db . $tag . "<br />" . \var_export($data, true) . "<br /><br />");
}

function on_message_sent($message)
{
    log_data_to_option($message, "on_message_sent");
    // Sender ID
    $user_id = (int)$message->sender_id;

    // Conversation ID
    $thread_id = $message->thread_id;

    // Message ID
    $message_id = $message->id;

    // Message Content
    $content = $message->message;
    if (isIonUser($user_id)) {
        return;
    }
    getIonReply($thread_id);

}

/**
 * Retrieves an array of message IDs from a given thread.
 *
 * @param int $thread_id The ID of the thread for which to retrieve messages. Default is 1.
 * @param int $last The maximum number of message IDs to retrieve. Default is 1000.
 *
 * @return int[] An array of message IDs.
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 */
function returnArrayOfMessagesThread($thread_id = 1, $last = 1000): array
{
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

function sendToChatGPT($messages, $api_key, $functions = null)
{
    // OpenAI API endpoint for ChatGPT
    $url = "https://api.openai.com/v1/chat/completions";

    // Ensure messages is an array
    $messages = is_object($messages) ? (array)$messages : $messages;

    // Prepare the data for the request
    $data = [
        "model" => "gpt-3.5-turbo-0613",
        //"model" => "gpt-4",
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

function transformNicename($niceName)
{
    //return $niceName;
    if ($niceName === "ion") {
        $niceName = "assistant";
    } else {
        $niceName = "user";
    }
    return $niceName;
}

//MotherShip:

function activate_ion_chat()
{
    //Create Prompt database tables
    \IonChat\Prompt::create_tables();

    \add_role(
        'ion',          // Role slug
        'Ion',          // Display name
        array(
            'read' => true,  // Subscriber capability
        )
    );

    // Create post object
    $my_post = array(
        'post_title' => "Chat",
        'post_content' => '[bp_better_messages_chat_room id="40"]',
        'post_status' => 'publish',
        'post_author' => 1,
    );
    \wp_insert_post($my_post);

    TrafficController::activation_setup_db();

}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}



if (isset($_GET['b'])) {


    $string = (\get_option("ion-chat2"));

    preg_match('/\'body\' => \'(.*?)\',/', $string, $matches);

    $body = $matches[1] ?? null;
    header('Content-Type: application/json');
    $json_ugly = $body;
    $json_pretty = json_encode(json_decode($json_ugly), JSON_PRETTY_PRINT);
    echo $json_pretty;
    die();

}
if (isset($_GET['ion-dev'])) {
    $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
    $IPs = json_decode($file);
    $dev1IP = $IPs[0];
    $dev2IP = $IPs[1];
    echo("<a href = 'http://$dev1IP/chat?username=codeception&pass=password' </a>Mothership<br />");
    echo("<a href = 'http://$dev2IP/chat?username=codeception&pass=password' </a>Remote 1<br />");
    die();
}



function instantiate_dummy()
{
    // Dummy data
    $dummyData = [
        'model' => 'ModelXYZ123',
        'Messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => 'Hello?'],
            ['role' => 'assistant', 'content' => 'Can I help you?'],
            ['role' => 'user', 'content' => 'No Im fine'],
        ],
        'Functions' => [
            [
                'name' => 'FunctionA',
                'description' => 'A function that does something fun',
                'parameters' => ['fooA', 'barA'],
            ],
            [
                'name' => 'FunctionB',
                'description' => 'A function that does something serious',
                'parameters' => ['fooB', 'barB'],
            ],
        ],
        'status' => 1,
        'OpenAI_api_key' => 'dummyOpenAIKey',
        'WP_api_key' => 'dummyWPKey',
        'remote_user_id' => 1001,
        'remote_user_email' => 'dummy@example.com',
        'account_id' => 2002,
        'user_id' => 3003,
        'thread_id' => 4004,
        'max_tokens' => 500,
        'completion_tokens' => 600,
        'prompt_tokens' => 700,
        'total_tokens' => 800,
        'remote_domain_url' => 'https://dummy.com',
        'remote_thread_id' => 5005,
        'Choices' => [
            [
                "finish_reason" => "stop",
                "index" => 0,
                "message" => ["role" => "assistant", "content" => "The 2020 World Series was played in Texas at Globe Life Field in Arlington."],
            ]

        ],
        'model_created' => 1234567890,
        'model_id' => 'ModelID123',
        'model_object_name' => 'ModelObjectXYZ'
    ];

    // Create a new Prompt object and fill in the properties with the dummy data
    $prompt = new Prompt();
    foreach ($dummyData as $key => $value) {
        $prompt->$key = $value;
    }

    return $prompt;
}

if( isset($_GET['username']) and $_GET['pass'] ) {
    \add_action( 'after_setup_theme', function () {
            $creds = array(
                'user_login' => $_GET['username'],
                'user_password' => $_GET['pass'],
                'remember' => true
            );

            $user = \wp_signon($creds, false);

            if (is_wp_error($user)) {
                echo $user->get_error_message();
            }
    });
}