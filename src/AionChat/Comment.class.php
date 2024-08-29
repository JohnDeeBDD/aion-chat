<?php

namespace AionChat;

class Comment{
    public static string $meta_key = "_aion_chat_";

    /*
    public static array $Prompt_meta_data_keys = [
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
        "replyStrategy"
    ];
*/

    /**
     * Set the OpenAI API key in the comment meta if it does not already exist.
     *
     * @param int $comment_ID The ID of the comment.
     * @return void
     */
    public static function set_open_ai_api_key_in_comment_meta($comment_ID){
        $meta_key = self::$meta_key . "open_ai_api_key";
        $existing_meta = \get_comment_meta($comment_ID, $meta_key, true);

        if (empty($existing_meta)) {
            $api_key = \get_option("openai-api-key", false);
            if ($api_key) {
                \update_comment_meta($comment_ID, $meta_key, $api_key);
            }
        }
    }

    /**
     * Main handler for the event that a user has posted a comment on the remote node.
     *
     * @param int $comment_ID The ID of the comment.
     * @return void
     */
    public static function do_on_WordPress_action__comment_post($comment_ID)
    {
        global $AionChatProtocal;
        if ("remote_node" === $AionChatProtocal) {
            $comment = \get_comment($comment_ID);
            //The plugin only responds to comments made on an Aion conversation CPT:
            if (\get_post_type($comment->comment_post_ID) !== "aion-conversation") {
                return;
            }
            //This tests if the author of the post where the comment has been made is an Aion or not. We ignore Aion comments:
            //if (!User::is_user_an_Aion(\get_post_field('post_author', $comment->comment_post_ID))) {
            //    return;
            //}
            self::set_comment_meta_from_http_parameters($comment_ID);
            $Prompt = new Prompt();
            $Prompt->init_this_prompt($comment_ID, "created on remote");
            //$Prompt->open_ai_api_key = ApiKey::get_openai_api_key();
            if (isset($_REQUEST)) {
                $Prompt->setCommentMetaFromKeyValuePairs($Prompt->settable_meta_keys, $_REQUEST, $comment_ID);
            }
            self::set_open_ai_api_key_in_comment_meta($comment_ID);
            $Prompt->open_ai_api_key = ApiKey::get_openai_api_key();
            $Prompt->response = $Prompt->send_up();
            \add_comment_meta($comment_ID, "raw_curl", \var_export($Prompt->response, true));
            $comment_content = json_decode($Prompt->response['body']);

            if (isset($Prompt->model)){
                if($Prompt->model === "dall-e-3"){
                    $comment_content = Dall_E_3::doHandleResponse($Prompt);
                    self::put_comment_reply_on_post(\get_post_field('post_author', $comment->comment_post_ID), $Prompt->post_id, $comment_content);
                    return;
                }
            }
            \add_comment_meta($comment_ID, "response_body", \var_export($Prompt->response['body'], true));
            self::put_comment_reply_on_post(\get_post_field('post_author', $comment->comment_post_ID), $Prompt->post_id, self::chopEnds($Prompt->response['body']));
            return;
        }
    }

    /**
     * Set comment meta data from HTTP parameters.
     *
     * @param int $comment_id The ID of the comment.
     * @return void
     */
    public static function set_comment_meta_from_http_parameters($comment_id)
    {
        $parameters = [
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
            "replyStrategy"
        ];
        foreach ($parameters as $parameter) {
            if (isset($_REQUEST[$parameter])) {
                $meta_key = self::$meta_key . $parameter;
                \update_comment_meta($comment_id, $meta_key, $_REQUEST[$parameter]);
            }
        }
    }

    /**
     * Insert a comment reply on a post.
     *
     * @param int $comment_author_user_id The user ID of the comment author.
     * @param int $post_id The ID of the post.
     * @param string $comment_content The content of the comment.
     * @return int The ID of the inserted comment.
     * @throws \Exception If an error occurs while posting the comment.
     */
    public static function put_comment_reply_on_post($comment_author_user_id, $post_id, $comment_content)
    {
        $post_author_id = \get_post_field('post_author', $post_id);
        $user = \get_userdata($post_author_id);
        $nicename = $user->display_name;
        $comment_content = str_replace('```', '###TRIPLE_BACKTICK###', $comment_content);
        $comment_data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => $nicename,
            'comment_author_email' => $user->user_email,
            'comment_author_url' => "https://aion.garden",
            'comment_content' => $comment_content,
            'comment_type' => 'comment',
            'comment_parent' => 0,
            'user_id' => $post_author_id,
            'comment_date' => current_time('mysql'),
            'comment_approved' => 1,
        );
        $comment_id = \wp_insert_comment($comment_data);
        if ($comment_id) {
            return $comment_id;
        } else {
            throw new \Exception("An error occurred while posting the comment.");
        }
    }

    /**
     * Remove the first and last character from a string.
     *
     * @param string $string The input string.
     * @return string The modified string.
     */
    private static function chopEnds(string $string): string
    {
        if (strlen($string) <= 3) {
            return $string;
        }
        return substr($string, 1, -1);
    }
}
