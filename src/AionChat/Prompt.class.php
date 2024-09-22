<?php

namespace AionChat;

class Prompt
{

    use CommentMeta;

    public array $choices = []; //array of Choice objects
    public array $functions_available = []; //Array of Functions objects
    public array $functions_called = [];
    public array $messages = []; //Array of Message object
    public array $Comments = []; //Array of comments

    public int    $account_user_id;
    public int    $remote_account_user_id;
    public string $account_user_email;

    public int    $author_user_id;
    public string $author_user_email;
    public int    $author_remote_user_id;

    public int    $interlocutor_user_id;
    public string $interlocutor_user_email;
    public int    $interlocutor_remote_user_id;

    public int $user_id; // the opposite speaker to the Aion
    public int $remote_user_id; // the opposite speaker to the Aion
    public string $user_email; // the opposite speaker to the Aion

    public string $status;

    public int $completion_tokens;
    public int $max_tokens;
    public int $total_tokens;
    public int $prompt_tokens;

    public string $model;

    public int    $comment_id;  // ? needed
    public int    $remote_comment_id;  // ? needed
    public string $comment_content;  // ? needed

    public int    $post_id;
    public int    $remote_post_id;
    public string $post_title;
    public string $post_content;
    public array  $tags = [];

    public $open_ai_api_key;
    public $remote_open_ai_api_key;

    public string $origin_domain_url;
    public int $interlocutor_post_id;
    public string $wordpress_api_key;

    public string $system_instructions;

    public $response;
    public array $response_meta;
    public $response_comment_id;
    public $remote_response;

    public $functions;

    public $reply_strategy;
    // sync http response
    // async application password
    // async username/password
    // async email
    // 2 factor
    /*
    public $settable_Prompt_meta_data_keys = [
        "functions",
        "account_user_id",
        "status",
        "completion_tokens",
        "max_tokens",
        "total_tokens",
        "prompt_tokens",
        "model",
        "open_ai_api_key",
        "remote_open_ai_api_key",
        "system_instructions",
        "reply_strategy"
    ];
*/
    public static function createFunctionMetadata($name, $description, $parameters)
    {
        return [
            "name" => $name,
            "description" => $description,
            "parameters" => $parameters
        ];
    }

    public static function get_meta_property($property_name, $comment_id)
    {
        // Construct the meta key
        $meta_key = "_aion_chat_" . $property_name;

        // Fetch the comment meta
        $comment_meta_value = get_comment_meta($comment_id, $meta_key, true);

        if (!empty($comment_meta_value)) {
            // Return the comment meta value if it exists
            return $comment_meta_value;
        }

        // If the comment meta doesn't exist, fetch the parent post ID
        $comment = get_comment($comment_id);
        if ($comment) {
            $post_id = $comment->comment_post_ID;

            // Check if the post is of the custom post type 'aion-conversation'
            $post = get_post($post_id);
            if ($post && $post->post_type === 'aion-conversation') {
                // Fetch the post meta
                $post_meta_value = get_post_meta($post_id, $meta_key, true);

                if (!empty($post_meta_value)) {
                    // Return the post meta value if it exists
                    return $post_meta_value;
                }
            }
        }

        // If neither exists, return null or a default value
        return null;
    }

    public function validate_and_set_Prompt_author_id_from_email()
    {

        $User = get_user_by("email", $this->author_user_email);
        if (!$User) {
            throw new Exception("Error: Author email not found");
        }
        if (!User::is_user_an_Aion($User->ID)) {
            throw new Exception("Error: Author is not an Aion");
        }
        $this->author_user_id = $User->ID;
    }

    public function send_up()
    {
        // Determine the correct API route
        $api_route = "/wp-json/aion-chat/v1/aion-prompt";
        if (isset($this->model) && $this->model === "dall-e-3") {
            $api_route = "/wp-json/aion-chat/v1/dall-e-3";
        }

        // Get the mothership URL
        global $Servers;
        $AionChat_mothership_url = $Servers->mothershipURL;

        // Send POST request
        $response = \wp_remote_post($AionChat_mothership_url . $api_route, array(
            'method' => 'POST',
            'timeout' => 60,
            'redirection' => 1,
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'prompt' => serialize($this), // Consider serializing only necessary data
            )
        ));

        // Handle potential errors
        if (\is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("Error in send_up(): $error_message at $AionChat_mothership_url");
            return false; // Or return a custom error response
        }

        // Optionally update a WordPress option or handle the response as needed
        // update_option('response', $response);

        return $response;
    }



    //These properties are settable via http and are stored as meta data on the comment

    public function set_comment_meta_from_http_parameters($comment_id)
    {
        foreach ($this->settable_meta_keys as $parameter) {
            if (isset($_REQUEST[$parameter])) {
                $metaKey = "_aion_chat_" . $parameter;
                update_comment_meta($comment_id, $metaKey, $_REQUEST[$parameter]);
            }
        }
    }

    /*
     * This function pulls data from the database at a point denoted by a specific comment on an Aion Conversation
     * It initializes the Prompt data structure from that comment
     */
    public function init_this_prompt($comment_id, $status)
    {
        $this->origin_domain_url = get_site_url();
        $this->comment_id = $comment_id;
        $Comment = get_comment($comment_id);
        $this->comment_content = $Comment->comment_content;
        $this->post_id = $Comment->comment_post_ID;
        $this->user_id = $Comment->user_id;
        $this->user_email = $Comment->comment_author_email;
        $this->author_user_id = get_post_field('post_author', $Comment->comment_post_ID);
        $this->author_user_email = get_the_author_meta('user_email', $this->author_user_id);
        $this->setPromptPropertiesFromPostMeta($Comment->comment_post_ID);
        $this->setPromptPropertiesFromCommentMeta($comment_id, $this->post_id);
        $this->set_empty_properties_to_defaults();
        $this->set_comments();
        $this->status = $status;
    }

    //If there are properties that are empty, that cannot remain so, there are filled in here.
    public function set_empty_properties_to_defaults(){

        //api key
        if(!isset($this->open_ai_api_key)){
            $this->open_ai_api_key = ApiKey::get_openai_api_key();
        }
        //throttle
        if(!isset($this->max_tokens)){
            $this->max_tokens = 1500;
        }

        //system_instructions
        if(!isset($this->system_instructions)){
            $this->system_instructions = Instructions::getHelpfulAssistantInstructions();
        }
    }

    //This function is overwritten on the Mothership

    public function set_comments()
    {

        $this->Comments = [];

        // Get the comments for the post with ID stored in $this->post_id
        $args = array(
            'post_id' => $this->post_id,
            'status' => 'approve'
        );
        $this->Comments = get_comments($args);
    }

    public function set_messages()
    {

        // Initialize an empty array to hold the messages
        $this->messages = [];

        // Get the comments for the post with ID stored in $this->post_id
        $args = array(
            'post_id' => $this->post_id,
            'status' => 'approve'
        );
        $comments = get_comments($args);

        $post_author = get_post_field('post_author', $this->post_id);
        // Loop through each comment and add it to the messages array
        foreach ($comments as $comment) {
            if ($comment->user_id === $post_author) {
                $role = "assistant";
            } else {
                $role = "user";
            }
            $Message = [
                "role" => $role,
                "content" => $comment->comment_content
            ];
            array_push($this->messages, $Message);
        }
        array_push($this->messages, ["role" => "system", "content" => $this->system_instructions]);
        $this->messages = array_reverse($this->messages);
        $this->messages = array_values($this->messages);

    }


}