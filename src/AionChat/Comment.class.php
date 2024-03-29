<?php

namespace AionChat;

class Comment
{

    public static function enable_interaction()
    {
        \add_action('comment_post', '\AionChat\Comment::comment_controller', 10, 1);
    }



    public static function comment_controller($comment_ID)
    {
        global $AionChatProtocal;
        if ("remote_node" === $AionChatProtocal) {
           // die("wtf2");
            $comment = \get_comment($comment_ID);
            if(!\get_post_type($comment->comment_post_ID) === "aion-conversation"){
                return;
            }
            if (!User::is_user_an_Aion(\get_post_field( 'post_author', $comment->comment_post_ID))) {
                return;
            }
            $Prompt = new Prompt();
            $Prompt->init_this_prompt($comment_ID, "created on remote");
            $Prompt->response = $Prompt->send_up();
            $Prompt->send_response_comment_to_post(self::chopEnds($Prompt->response['body']));
        }
    }

    private static function chopEnds(string $string): string
    {
        if (strlen($string) <= 3) {
            return $string;
        }
        return substr($string, 1, -1);
    }

    public static function is_ion_mentioned($post_id)
    {
        $args = array(
            'post_id' => $post_id,
        );
        $comments = \get_comments($args);
        foreach ($comments as $comment) {
            if (preg_match('/\b(Ion|ion)\b/', $comment->comment_content) === 1) {
                return true;
            }
        }
        return false;
    }

}