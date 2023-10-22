<?php

namespace IonChat;

use \AllowDynamicProperties;

#[AllowDynamicProperties]
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

    public int $user_id;
    public int $remote_user_id;
    public string $user_email;
    public string $remote_user_email;

    public $open_ai_api_key;
    public $remote_open_ai_api_key;

    public string $remote_connection_domain_url;
    public string $wordpress_api_key;

    public $response;

    public function post_comment_to_post($comment_content)
    {

        //$comment_content = str_replace('```', '###TRIPLE_BACKTICK###', $comment_content);
        $comment_data = array(
            'comment_post_ID' => $this->post_id,
            'comment_author' => "Ion",
            'comment_content' => $comment_content,
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => User::get_ion_user_id(),
            'comment_date' => current_time('mysql'),
            'comment_approved' => 1,
        );

        // Insert the comment and get the comment ID
        $comment_id = wp_insert_comment($comment_data);

        if ($comment_id) {
            return true;
        } else {
            throw new Exception("An error occurred while posting the comment.");
        }
    }

    public function set_messages()
    {
        // Initialize an empty array to hold the messages
        $this->Messages = [];

        // Get the comments for the post with ID stored in $this->post_id
        $args = array(
            'post_id' => $this->post_id,
            'status' => 'approve'
        );
        $comments = get_comments($args);


        // Loop through each comment and add it to the messages array
        foreach ($comments as $comment) {
            $role = User::is_ion_user($comment->user_id) ? "assistant" : "user";
            $Message = [
                "role" => $role,
                "content" => $comment->comment_content
            ];
            array_push($this->Messages, $Message);
        }

        if (\metadata_exists('post', $this->post_id, 'ion-chat-instructions')){
            $instructions = \get_post_meta( $this->post_id, 'ion-chat-instructions', true);
        }else{
            $instructions = "You are a helpful assistant.";
        }
        array_push($this->Messages, ["role" => "system", "content" => $instructions]);
        $this->Messages = array_reverse($this->Messages);
        $this->Messages = array_values($this->Messages);

    }

    public function xxsend_self_to_ChatGPT()
    {
        $api_key = $this->open_ai_api_key;
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

        $this->remote_connection_domain_url = \get_site_url();

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

    public function xxsend_down()
    {


        \update_option("down_bus", $this);
        global $dev1IP;
        global $dev2IP;
        $response = \wp_remote_post("http://" . $dev2IP . "/wp-json/ion-chat/v1/ion-prompt", array(
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
        \update_option("down_bus", \var_export(\unserialize($response), true));

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo "Something went wrong: $error_message";
        } else {
            echo 'Response:<pre>';
            print_r($response);
            echo '</pre>';
        }
        return \json_decode($response, true);


    }

    public function send_up()
    {
        //this action is happening on the remote.
        \update_option("ion-chat-up-bus", $this);
        global $dev1IP;
        global $dev2IP;

        $response = \wp_remote_post("http://" . $dev1IP . "/wp-json/ion-chat/v1/ion-prompt", array(
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

    public function returnInstruction()
    {
        return new Message("system", "You are a helpful assistant.");
    }

}
