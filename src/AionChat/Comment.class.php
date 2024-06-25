<?php

namespace AionChat;

//use AionDialectic\Dialectic;

class Comment
{

    public static function action_comment_post($comment_ID)
    {
        global $AionChatProtocal;
        if ("remote_node" === $AionChatProtocal) {
            $comment = \get_comment($comment_ID);
            //The plugin only responds to comments made on an Aion conversation CPT:
            if (!\get_post_type($comment->comment_post_ID) === "aion-conversation") {
                return;
            }
            //This tests is the author of the post where the comment has been made, is an Aion or not. We ignore Aion comments:
            if (!User::is_user_an_Aion(\get_post_field('post_author', $comment->comment_post_ID))) {
                return;
            }
            $Prompt = new Prompt();
            $Prompt->init_this_prompt($comment_ID, "created on remote");
            $Prompt = self::set_prompt_params_from_optional_http_request($Prompt);
            $Prompt->response = $Prompt->send_up();
            \update_option("response_Comment_line27", \var_export($Prompt->response, true));
            $comment_content = json_decode($Prompt->response['body']);
            \update_option("response_Comment_line29", \var_export($comment_content, true));
            if (isset($Prompt->model)){
                if($Prompt->model === "dall-e-3"){
                    $comment_content = Dall_E_3::doHandleResponse($Prompt);
                }
            }
            self::put_comment_reply_on_post(\get_post_field('post_author', $comment->comment_post_ID), $Prompt->post_id, $comment_content);
            return self::chopEnds($Prompt->response['body']);

        }
    }

    public static function set_prompt_params_from_optional_http_request(Prompt $Prompt)
    {
        $setable_parameters = ["Functions", "account_user_id", "status", "completion_tokens", "max_tokens", "total_tokens", "prompt_tokens",
            "model", "comment_id", "remote_comment_id", "comment_content", "post_id", "remote_post_id", "open_ai_api_key", "remote_open_ai_api_key", "origin_domain_url",
            "aion-chat-instructions", "replyStrategy"];

        foreach ($setable_parameters as $parameter) {

            if (isset($_REQUEST[$parameter])) {
                $metaKey = "aion-chat-" . $parameter;
                \update_post_meta($Prompt->post_id, $metaKey, $_REQUEST[$parameter]);
                $Prompt->$parameter = $_REQUEST[$parameter];
            }
        }
        return $Prompt;
    }

    //public static function put_comment_reply_on_post($comment_content, $comment_user_id, $post_id)
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
            throw new Exception("An error occurred while posting the comment.");
        }
        return $comment_id;
    }

    private static function chopEnds(string $string): string
    {
        if (strlen($string) <= 3) {
            return $string;
        }
        return substr($string, 1, -1);
    }
}