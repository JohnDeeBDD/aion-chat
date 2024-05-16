<?php

namespace AionChat;

class Prompt
{

    public array $Choices = []; //array of Choice objects
    public array $Functions = []; //Array of Functions objects
    public array $Messages = []; //Array of Message object
    public int $account_user_id;
    public string $status;
    public int $completion_tokens;
    public int $max_tokens;
    public int $total_tokens;
    public int $model_created;
    public int $prompt_tokens;
    public string $model;
    public string $model_id;
    public string $model_object_name;

    public int $comment_id;
    public int $remote_comment_id;
    public string $comment_content;

    public int $post_id;
    public int $remote_post_id;
    public string $post_title;
    public string $post_content;
    public array $tags = [];

    public int $user_id;
    public int $remote_user_id;
    public string $user_email;
    public string $remote_user_email;

    public int $author_user_id;
    public int $author_remote_user_id;
    public string $author_user_email;
    public string $author_remote_user_email;

    public $open_ai_api_key;
    public $remote_open_ai_api_key;

    public string $origin_domain_url;
    public string $wordpress_api_key;

    public string $system_instructions;

    public $response;

    public $functions;

    public $replyStrategy;
    // sync http response
    // async application password
    // async username/password
    // async email
    // 2 factor


    public function set_messages()
    {

        // Initialize an empty array to hold the messages
        $this->Messages = [];

        // Get the comments for the post with ID stored in $this->post_id
        $args = array(
            'post_id' => $this->post_id,
            'status' => 'approve'
        );
        $comments = \get_comments($args);

        $post_author = \get_post_field ('post_author', $this->post_id);
        // Loop through each comment and add it to the messages array
        foreach ($comments as $comment) {
            if($comment->user_id === $post_author){
                $role = "assistant";
            }else{
                $role = "user";
            }
            //$role = User::is_user_an_Aion($comment->user_id) ? "assistant" : "user";
            $Message = [
                "role" => $role,
                "content" => $comment->comment_content
            ];
            array_push($this->Messages, $Message);
        }

        if(!isset($this->system_instructions)){
            if (\metadata_exists('post', $this->post_id, 'aion-chat-instructions')){
                $this->system_instructions = \get_post_meta( $this->post_id, 'aion-chat-instructions', true);
            }else{
                $this->system_instructions = Instructions::getHelpfulAssistantInstructions();
            }
        }


        array_push($this->Messages, ["role" => "system", "content" => $this->system_instructions]);
        $this->Messages = array_reverse($this->Messages);
        $this->Messages = array_values($this->Messages);

    }

    protected function set_open_ai_api_key()
    {
        if ('not-exists' === get_option('openai-api-key', 'not-exists')) {
            $this->open_ai_api_key = null;
        } else {
            $this->open_ai_api_key = \get_option("openai-api-key", true);
        }
    }

    public function init_this_prompt($comment_id, $status)
    {
        $this->set_open_ai_api_key();

        $this->origin_domain_url = \get_site_url();


        // Set the comment_id from the method argument
        $this->comment_id = $comment_id;

        // Fetch the comment content based on the comment_id
        $comment = \get_comment($comment_id);
        $this->comment_content = $comment->comment_content;

        // Fetch the post ID associated with the comment
        $this->post_id = $comment->comment_post_ID;
        $this->user_id = $comment->user_id;
        $this->user_email = $comment->comment_author_email;
        $this->set_messages();
        // Set the status
        $this->status = $status;
    }

    public function send_up()
    {   //die("153");
        //this action is happening on the remote.
        // \update_option("aion-chat-up-bus", $this);
        global $AionChat_mothership_url;
        $response = \wp_remote_post($AionChat_mothership_url . "/wp-json/aion-chat/v1/aion-prompt", array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'prompt' => \serialize($this),
                )
            )
        );
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
            die();
        }
        return $response;

    }

    public function directQuestion($question, $instructions = "You are a helpful assistant.") {
        /*

                'post_author'           => $user_id,
                        'post_content'          => '',
                        'post_content_filtered' => '',
                        'post_title'            => '',
                        'post_excerpt'          => '',
                        'post_status'           => 'draft',
                        'post_type'             => 'post',
                        'comment_status'        => '',
                        'ping_status'           => '',
                        'post_password'         => '',
                        'to_ping'               => '',
                        'pinged'                => '',
                        'post_parent'           => 0,
                        'menu_order'            => 0,
                        'guid'                  => '',
                        'import_id'             => 0,
                        'context'               => '',
                        'post_date'             => '',
                        'post_date_gmt'         => '',

        */
        \wp_insert_post();
    }

}
/*
function createFunctionMetadata($name, $description, $parameters) {
    return [
        "name" => $name,
        "description" => $description,
        "parameters" => $parameters
    ];
}
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
*/