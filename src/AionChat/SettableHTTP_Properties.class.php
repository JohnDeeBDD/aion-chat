<?php

namespace AionChat;

class SettableHTTP_Properties{

    public static string $meta_key = "_aion_chat_";

    const parameters = [
        "account_user_id",
        "interlocutor_user_id",
        "completion_tokens",
        "functions",
        "instructions",
        "max_tokens",
        "model",
        "open_ai_api_key",
        "prompt_tokens",
        "reply_strategy",
        "status",
        "system_instructions",
        "total_tokens",
    ];

    public static function set_comment_meta_from_http_parameters($comment_id){
        $parameters = SettableHTTP_Properties::parameters;
        foreach ($parameters as $parameter) {
            if (isset($_REQUEST[$parameter])) {
                $meta_key = self::$meta_key . $parameter;
                \update_comment_meta($comment_id, $meta_key, $_REQUEST[$parameter]);
            }
        }
    }

}