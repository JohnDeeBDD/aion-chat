<?php

namespace IonChatMothership;

use \AllowDynamicProperties;
use IonChat\Exception;
use IonChat\User;

#[AllowDynamicProperties]
class Prompt extends \IonChat\Prompt {

    public static function createFunctionMetadata($name, $description, $parameters) {
        return [
            "name" => $name,
            "description" => $description,
            "parameters" => $parameters
        ];
    }

    public function send_self_to_ChatGPT()    {

        $this->functions  = [
            self::createFunctionMetadata(
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

        $api_key = $this->open_ai_api_key;
        // OpenAI API endpoint for ChatGPT
        $url = "https://api.openai.com/v1/chat/completions";

        // Prepare the data for the request
        $data = [
            //"model" => "gpt-3.5-turbo-0613",
            "model" => "gpt-4",
            'messages' => ($this->Messages),
            'max_tokens' => 1500, // You can adjust this as needed
            'functions' => ($this->functions)
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
        $response = \json_decode($response, true);
        if (isset($response['choices'][0]['message']['content'])) {
            $this->response = $response['choices'][0]['message']['content'];
        } else {
            $this->response = \var_export($response, true);
        }
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

        //$this->user_id = \IonChat\User::get_ion_user_id();
        //$this->user_email = \IonChat\User::get_ion_email();
        $this->set_messages();
        // Set the status
        $this->status = $status;
    }

    public function send_down(){


        \update_option("down_bus", $this);

        $response = \wp_remote_post( $this->origin_domain_url . "/wp-json/ion-chat/v1/ion-prompt", array(
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
       // \update_option("down_bus", \var_export(\unserialize($response), true));

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



}
