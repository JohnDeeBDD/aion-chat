<?php

namespace IonChatMothership;

use \AllowDynamicProperties;

#[AllowDynamicProperties]
class Prompt extends \IonChat\Prompt {

    public function send_self_to_ChatGPT()    {

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

    public function init_this_prompt($comment_id, $status){
        $this->set_open_ai_api_key();

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

        $response = \wp_remote_post( $this->remote_connection_domain_url . "/wp-json/ion-chat/v1/ion-prompt", array(
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


    public function xxsend_up(){
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


    public function returnInstruction(){
        return new Message("system", "You are a helpful assistant.");
    }

}