<?php

namespace IonChat;

use \AllowDynamicProperties;

#[AllowDynamicProperties]
class Prompt {
    
    public array $Choices = []; //array of Choice objects
    public array $Functions = []; //Array of Functions objects
    public array $Messages = []; //Array of Message object
    public int $account_user_id;
    public int $completion_tokens;
    public int $max_tokens;
    public int $model_created;
    public int $prompt_tokens;
    public int $remote_comment_id; // the bm_message id that kicked off the request
    public int $remote_post_id;
    public int $remote_user_id;
    public string $status; //send down //smash into db
    public int $post_id; // the local thread id
    public int $comment_id;
    public string $comment_content;
    public int $total_tokens;
    public int $user_id;
    public string $user_email;
    public string $model;
    public string $model_id;
    public string $model_object_name;
    public string $OpenAI_api_key;
    public string $remote_connection_domain_url;
    public string $remote_user_email;
    public string $WP_api_key;
    public $response;

    public function set_messages() {
        // Initialize an empty array to hold the messages
        $this->Messages = [];

        // Get the comments for the post with ID stored in $this->post_id
        $args = array(
            'post_id' => $this->post_id,
            'status' => 'approve'
        );
        $comments = get_comments($args);
        $content = \get_post_field('post_content', $this->post_id);
        array_push($this->Messages, ["role" => "system", "content" => $content]);

        // Loop through each comment and add it to the messages array
        foreach ($comments as $comment) {
            $role = is_ion_user($comment->user_id) ? "assistant" : "user";
            $Message = [
                "role" => $role,
                "content" => $comment->comment_content
            ];
            array_push($this->Messages, $Message);
        }
        $this->Messages = array_values($this->Messages);
    }

    public function send_to_ChatGPT()
    {
        $api_key = $this->OpenAI_api_key;
        // OpenAI API endpoint for ChatGPT
        $url = "https://api.openai.com/v1/chat/completions";

        // Prepare the data for the request
        $data = [
            "model" => "gpt-3.5-turbo-0613",
            //"model" => "gpt-4",
            'messages' => ($this->Messages),
            'max_tokens' => 1500 // You can adjust this as needed
        ];
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

        // Set cURL options
        $options = [
            //CURLOPT_URL => 'https://example.com/api/resource',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_VERBOSE => true,
            CURLOPT_STDERR => $verbose = fopen('php://temp', 'w+'),
        ];
        //curl_setopt_array($ch, $options);


        // Execute cURL session and get the response
        $response = curl_exec($ch);

        $debug_info = [];

            $debug_info['error'] = 'cURL Error: ' . curl_error($ch);

            $info = curl_getinfo($ch);
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);

            $debug_info['verbose'] = $verboseLog;
            $debug_info['response'] = $response;
        curl_close($ch);
        \update_option('wp_curl_debug_info', $debug_info);
        \update_option('wp_curl_debug_info_data', $data);
        // Decode the response
        return \json_decode($response, true);
    }

    public function init_this_prompt($comment_id, $status){
        $this->OpenAI_api_key = \get_option("openai-api-key", true);

        // Set the comment_id from the method argument
        $this->comment_id = $comment_id;

        // Fetch the comment content based on the comment_id
        $comment = \get_comment($comment_id);
        $this->comment_content = $comment->comment_content;

        // Fetch the post ID associated with the comment
        $this->post_id = $comment->comment_post_ID;

        // Fetch the user with username "Codeception"
        $user = \get_user_by('login', 'Codeception');
        $this->user_id = $user->ID;
        $this->user_email = $user->user_email;
        $this->set_messages();
        // Set the status
        $this->status = $status;
    }

    public function send_down(){


        \update_option("down_bus", $this);
        global $dev1IP;global $dev2IP;
        $response = \wp_remote_post( "http://" . $dev2IP . "/wp-json/ion-chat/v1/ion-prompt", array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'prompt'  => \serialize($this),
                )
            )
        );
        \update_option("down_bus", \var_export(\unserialize($response), true));

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            echo 'Response:<pre>';
            print_r( $response );
            echo '</pre>';
        }
        return \json_decode($response, true);


    }

    public function send_up(){
        //this action is happening on the remote.
        \update_option("ion-chat-up-bus", $this);
        global $dev1IP;global $dev2IP;
        $response = \wp_remote_post( "http://" . $dev1IP . "/wp-json/ion-chat/v1/ion-prompt", array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'prompt'  => \serialize($this),
                )
            )
        );
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            echo 'Response:<pre>';
            print_r( $response );
            echo '</pre>';
        }
    }

    private function returnArrayOfMessagesThread($thread_id = 1, $last = 1000): array
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

    public function compile_ion_messages_from_bm_thread($thread_id, $conversationInitiation = []){
        $messageIDs = $this->returnArrayOfMessagesThread($thread_id);

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
        $result = [];
        $result[] = $this->returnInstruction();

        // Loop through the queried messages and format them
        foreach ($messages as $message) {

            if(is_ion_user($message->sender_id)){
                $role = "assistant";
            }else{
                $role = "user";
            }
            $result[] = new Message( $role, $message->message );
        }
        $this->Messages = $result;
    }

    public function returnInstruction(){
        return new Message("system", "You are a helpful assistant.");
    }

    public static function create(Prompt $prompt) {
        global $wpdb;
        $table_name_prompt = $wpdb->prefix . 'prompt';

        $data = [];
        foreach (self::propertiesMap() as $property => $defaultValue) {
            if (property_exists($prompt, $property)) {
                $data[$property] = $prompt->$property;
            }
        }

        $wpdb->insert($table_name_prompt, $data);

        return $wpdb->insert_id;
    }

    public static function get(int $prompt_id) {
        global $wpdb;
        $table_name_prompt = $wpdb->prefix . 'prompt';

        $result = $wpdb->get_row("SELECT * FROM $table_name_prompt WHERE id = $prompt_id");

        if ($result) {
            $prompt = new Prompt();
            foreach (self::propertiesMap() as $property => $defaultValue) {
                if (isset($result->$property)) {
                    $prompt->$property = $result->$property;
                }
            }

            return $prompt;
        }

        return null;
    }

    public static function update(Prompt $prompt, int $prompt_id) {
        global $wpdb;
        $table_name_prompt = $wpdb->prefix . 'prompt';

        $data = [];
        foreach (self::propertiesMap() as $property => $defaultValue) {
            if (property_exists($prompt, $property)) {
                $data[$property] = $prompt->$property;
            }
        }

        $wpdb->update($table_name_prompt, $data, array('id' => $prompt_id));
    }

    public static function create_tables() {
    global $wpdb;

    // Table for Prompt
    $table_name_prompt = $wpdb->prefix . 'prompt';
    $charset_collate = $wpdb->get_charset_collate();

    // Dynamically generate columns for the Prompt table based on properties
    $columns = [];
    $tempInstance = new Prompt();
    $propertiesMap = $tempInstance->getPropertiesMap();
    foreach ($propertiesMap as $property => $value) {
        switch (gettype($value)) {
            case 'integer':
                $columns[] = "{$property} int";
                break;
            case 'string':
                $columns[] = "{$property} text";
                break;
            case 'array':
                $columns[] = "{$property} text"; // Assuming arrays will be stored as JSON strings
                break;
            // Add more cases as needed
        }
    }
    $columns_sql = implode(",\n", $columns);

    $sql_prompt = "CREATE TABLE $table_name_prompt (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        $columns_sql,
        PRIMARY KEY  (id)
    ) $charset_collate;";

        

        // Table for Choice
        $table_name_choice = $wpdb->prefix . 'choice';
        $sql_choice = "CREATE TABLE $table_name_choice (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            finish_reason text NOT NULL,
            response_index int,
            message_role text,
            message_content text,
            prompt_id mediumint(9) NOT NULL,
            FOREIGN KEY (prompt_id) REFERENCES $table_name_prompt(id),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Table for FunctionData
        $table_name_function_data = $wpdb->prefix . 'function_data';
        $sql_function_data = "CREATE TABLE $table_name_function_data (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name text NOT NULL,
            description text,
            parameters text,
            prompt_id mediumint(9) NOT NULL,
            FOREIGN KEY (prompt_id) REFERENCES $table_name_prompt(id),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        \dbDelta($sql_prompt);
        \dbDelta($sql_choice);
        \dbDelta($sql_function_data);
    }

    public static function delete(int $prompt_id) {
        global $wpdb;

        // Delete associated FunctionData records
        $table_name_function_data = $wpdb->prefix . 'function_data';
        $wpdb->delete($table_name_function_data, array('prompt_id' => $prompt_id));

        // Delete associated Choice records
        $table_name_choice = $wpdb->prefix . 'choice';
        $wpdb->delete($table_name_choice, array('prompt_id' => $prompt_id));

        // Delete the Prompt record itself
        $table_name_prompt = $wpdb->prefix . 'prompt';
        $wpdb->delete($table_name_prompt, array('id' => $prompt_id));
    }
    /**
 * Determines if a given string is a serialized instance of the "Prompt" class.
 *
 * This function performs the following steps:
 * 1. Checks if the string contains the class name "Prompt".
 * 2. Uses a custom error handler to catch any errors during unserialization.
 * 3. Attempts to unserialize the string and checks if the resulting object is an instance of "Prompt".
 *
 * @param string $str The string to check.
 * @return bool True if the string is a serialized instance of "Prompt", false otherwise.
 * @throws Exception If there's an error during unserialization.
 */
    public static function isSerializedPrompt($str) {
    // Step 1: Check if the string contains the class name
    if (strpos($str, 'Prompt') === false) {
        return false;
    }

    // Step 2: Use a custom error handler
    set_error_handler(function($errno, $errstr) {
        throw new \Exception($errstr);
    });

    try {
        $object = unserialize($str);

        // Step 3: Check the type of the unserialized object
        if ($object instanceof Prompt) {
            restore_error_handler();
            return true;
        }
    } catch (\Exception $e) {
        // Unserialization failed
    }

    //restore_error_handler();
    return false;
}
    
    public static function instantiate_dummy()
{
    // Dummy data
    $dummyData = [
        'model' => 'ModelXYZ',
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
        'account_user_id' => 2002,
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

    /**
     * Returns an associative array of property names derived from the class definition.
     */
    private static function getPropertiesMap() {
        $reflectionClass = new \ReflectionClass(__CLASS__);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);


        $propertiesMap = [];
        foreach ($properties as $property) {
            // Exclude static properties and properties that start with an underscore (if any)
            if (!$property->isStatic() && $property->getName()[0] !== '_') {
                $propertiesMap[$property->getName()] = null; // default value is null, adjust if needed
            }
        }

        return $propertiesMap;
    }

}

class Choice {
    public string $finish_reason;
    public int $response_index;
    public Message $Message;

    public function __construct( string $finish_reason, int $response_index, Message $Message){
        $this->finish_reason = $finish_reason;
        $this->response_index = $response_index;
        $this->Message = $Message;
    }
}

class FunctionData {
    public string $name;
    public string $description;
    public array $parameters;

    public function __construct(string $name, string $description = "", array $parameters = []) {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = $parameters;
    }
}

