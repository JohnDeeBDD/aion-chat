<?php

namespace AionChat;

use AionDialectic\Dialectic;

class Comment
{

    public static function enable_interaction()
    {
        //The main action hook that initiates an intelligent response:
        \add_action('comment_post', '\AionChat\Comment::comment_controller', 10, 1);
    }



    public static function comment_controller($comment_ID)
    {
        global $AionChatProtocal;
        if ("remote_node" === $AionChatProtocal) {
           // die("wtf2");
            $comment = \get_comment($comment_ID);

            //The plugin only responds to comments made on an Aion conversation CPT:
            if(!\get_post_type($comment->comment_post_ID) === "aion-conversation"){
                return;
            }

            //This tests is the author of the post where the comment has been made, is an Aion or not:
            if (!User::is_user_an_Aion(\get_post_field( 'post_author', $comment->comment_post_ID))) {
                return;
            }

            $Prompt = new Prompt();
            $Prompt->init_this_prompt($comment_ID, "created on remote");

            //Here we send the comment up
            $Prompt->response = $Prompt->send_up();

            //This will have to be refactored. We are sending the comment up AND sending to the post as a reply. This only works in sync mode
            //$Prompt->send_response_comment_to_post(self::chopEnds($Prompt->response['body']), \get_post_field( 'post_author', $comment->comment_post_ID));
            self::send_response_comment_to_post(self::chopEnds($Prompt->response['body']), \get_post_field( 'post_author', $comment->comment_post_ID), $Prompt->post_id);

            if (class_exists('AionDialectec/Dialectic')) {
               // $Dialectic = \AionDialectic\Dialectic::chain_response($comment->comment_post_ID, self::chopEnds($Prompt->response['body']));
            }
        }
    }

    public static function send_response_comment_to_post($comment_content, $comment_user_id, $post_id)
    {

        $comment_content = str_replace('```', '###TRIPLE_BACKTICK###', $comment_content);
        //  die("line 76 Prompt");
        $comment_data = array(
            'comment_post_ID' => $post_id,
            'comment_content' => $comment_content,
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $comment_user_id,
            'comment_date' => current_time('mysql'),
            'comment_approved' => 1,
        );

        // Insert the comment and get the comment ID
        $comment_id = \wp_insert_comment($comment_data);

        if ($comment_id) {
            return true;
        } else {
            throw new Exception("An error occurred while posting the comment.");
        }
    }

    private static function chopEnds(string $string): string
    {
        if (strlen($string) <= 3) {
            return $string;
        }
        return substr($string, 1, -1);
    }

    /*
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

    */
}