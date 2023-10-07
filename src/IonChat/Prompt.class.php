<?php

namespace IonChat;

class Prompt implements IPrompt {
    
    public array $Choices = []; //array of Choice objects
    public array $Functions = []; //Array of Functions objects
    public array $Messages = []; //Array of Message object
    public int $account_user_id;
    public int $completion_tokens;
    public int $max_tokens;
    public int $model_created;
    public int $prompt_tokens;
    public int $remote_thread_id;
    public int $remote_user_id;
    public int $status; //send down //smash into db
    public int $thread_id;
    public int $total_tokens;
    public int $user_id;
    public string $model;
    public string $model_id;
    public string $model_object_name;
    public string $OpenAI_api_key;
    public string $remote_domain_url;
    public string $remote_user_email;
    public string $WP_api_key;

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
        dbDelta($sql_prompt);
        dbDelta($sql_choice);
        dbDelta($sql_function_data);
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
        throw new Exception($errstr);
    });

    try {
        $object = unserialize($str);

        // Step 3: Check the type of the unserialized object
        if ($object instanceof Prompt) {
            restore_error_handler();
            return true;
        }
    } catch (Exception $e) {
        // Unserialization failed
    }

    restore_error_handler();
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

class Message {
    public string $role;
    public string $content;

    public function __construct(string $role, string $content) {
        $this->role = $role;
        $this->content = $content;
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

interface IPrompt {
    public static function create_tables();
    public static function create(Prompt $prompt);
    public static function get(int $prompt_id);
    public static function update(Prompt $prompt, int $prompt_id);
    public static function delete(int $prompt_id);
}