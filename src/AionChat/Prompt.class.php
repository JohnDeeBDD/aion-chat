<?php

namespace AionChat;

class Prompt
{

    public array $Choices = []; //array of Choice objects
    public array $Functions_available = []; //Array of Functions objects
    public array $Functions_called = [];
    public array $Messages = []; //Array of Message object
    public array $Comments = []; //Array of comments
    public int $account_user_id;
    public string $status;

    public int $completion_tokens;
    public int $max_tokens;
    public int $total_tokens;
    public int $prompt_tokens;

    public string $model;

    public int $comment_id;  // ? needed
    public int $remote_comment_id;  // ? needed
    public string $comment_content;  // ? needed

    public int $post_id;
    public int $remote_post_id;
    public string $post_title;
    public string $post_content;
    public array $tags = [];

    public int $user_id; // the opposite speaker to the Aion
    public int $remote_user_id; // the opposite speaker to the Aion
    public string $user_email; // the opposite speaker to the Aion
    public string $remote_user_email; // the opposite speaker to the Aion

    public int $author_user_id;
    public string $author_user_email;
    public int $author_remote_user_id; // ? needed
    public string $author_remote_user_email;

    public $open_ai_api_key;
    public $remote_open_ai_api_key;

    public string $origin_domain_url;
    public string $wordpress_api_key;

    public string $system_instructions;

    public $response;
    public array $response_meta;
    public $response_comment_id;
    public $remote_response;

    public $functions;

    public $replyStrategy;
    // sync http response
    // async application password
    // async username/password
    // async email
    // 2 factor
    public static function createFunctionMetadata($name, $description, $parameters)
    {
        return [
            "name" => $name,
            "description" => $description,
            "parameters" => $parameters
        ];
    }

    public function set_comments(){

        $this->Comments = [];

        // Get the comments for the post with ID stored in $this->post_id
        $args = array(
            'post_id' => $this->post_id,
            'status' => 'approve'
        );
        $this->Comments = \get_comments($args);
    }

    public function  set_messages()
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


            if (\metadata_exists('post', $this->post_id, 'aion-chat-instructions')){
                $this->system_instructions = \get_post_meta( $this->post_id, 'aion-chat-instructions', true);
            }else{
                $this->system_instructions = Instructions::getHelpfulAssistantInstructions();
            }



        array_push($this->Messages, ["role" => "system", "content" => $this->system_instructions]);
        $this->Messages = array_reverse($this->Messages);
        $this->Messages = array_values($this->Messages);

    }

    public function send_up(){
        //die("line 119");
        global $Servers;
        $AionChat_mothership_url = $Servers->mothershipURL;
        $response = \wp_remote_post($AionChat_mothership_url . "/wp-json/aion-chat/v1/aion-prompt", array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 1,
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(),
                'body' => array(
                    'prompt' => \serialize($this),
                )
            )
        );
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: Prompt 139 $error_message $AionChat_mothership_url";
            die();
        }
        //update_option('response', $response);
        return ($response);
    }

    //This function is overwritten on the Mothership
    public function init_this_prompt($comment_id, $status)
    {
        $this->origin_domain_url = \get_site_url();
        $this->comment_id = $comment_id;
        $comment = \get_comment($comment_id);
        $this->comment_content = $comment->comment_content;
        $this->post_id = $comment->comment_post_ID;
        $this->user_id = $comment->user_id;
        $this->user_email = $comment->comment_author_email;
        $this->author_user_id = \get_post_field('post_author', $comment->comment_post_ID);
        $this->author_user_email = \get_the_author_meta('user_email', $this->author_user_id);
        $this->set_comments();
        $this->set_messages();
        $this->status = $status;
        $this->open_ai_api_key = ApiKey::get_openai_api_key();

    }

    public static function get_meta_property($property_name, $comment_id) {
        // Construct the meta key
        $meta_key = "_aion_chat_{$property_name}";

        // Fetch the comment meta
        $comment_meta_value = \get_comment_meta($comment_id, $meta_key, true);

        if (!empty($comment_meta_value)) {
            // Return the comment meta value if it exists
            return $comment_meta_value;
        }

        // If the comment meta doesn't exist, fetch the parent post ID
        $comment = \get_comment($comment_id);
        if ($comment) {
            $post_id = $comment->comment_post_ID;

            // Check if the post is of the custom post type 'aion-conversation'
            $post = \get_post($post_id);
            if ($post && $post->post_type === 'aion-conversation') {
                // Fetch the post meta
                $post_meta_value = \get_post_meta($post_id, $meta_key, true);

                if (!empty($post_meta_value)) {
                    // Return the post meta value if it exists
                    return $post_meta_value;
                }
            }
        }

        // If neither exists, return null or a default value
        return null;
    }

}