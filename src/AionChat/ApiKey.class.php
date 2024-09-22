<?php

namespace AionChat;

class ApiKey{


    public static function get_openai_api_key(){

        return \get_option("openai-api-key", false);
    }
    public static function is_invalid_key_response($response){}
    public static function output_no_key_message(){}
    public static function output_bad_key_message(){}

    /**
     * Set the OpenAI API key in the comment meta if it does not already exist. When this function is run,
     * SettableHTTP_Properties::set_comment_meta_from_http_parameters will have already been run, therefore if a key
     * was posted in the HTTP request it should already be in the comment meta.
     *
     * @param int $comment_ID The ID of the comment.
     * @return string the api key
     */
    public static function set_open_ai_api_key_in_Prompt($comment_ID)
    {
        $meta_key = "_aion_chat_open_ai_api_key";
        $api_key = \get_comment_meta($comment_ID, $meta_key, true);

        if (empty($api_key)) {
            $api_key = ApiKey::get_openai_api_key();
            if ($api_key) {
                \update_comment_meta($comment_ID, $meta_key, $api_key);
            }
        }
        return $api_key;
    }

}