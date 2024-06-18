<?php

namespace AionChat;

class ApiKey{


    public static function get_openai_api_key(){

        return \get_option("openai-api-key", false);
    }
    public static function is_invalid_key_response($response){}
    public static function output_no_key_message(){}
    public static function output_bad_key_message(){}

}