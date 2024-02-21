<?php

namespace AionChatMothership;

use AionChat\Exception;
use AionChat\User;

class Comment
{

    public static function enable_interaction()
    {
        \add_action('comment_post', function($comment_id){
            global $AionChatProtocal;
            if ("mothership" === $AionChatProtocal) {
                $comment = \get_comment($comment_id);
                if (!\get_post_type($comment->comment_post_ID) === "aion-conversation") {
                    return;
                }
                if (self::is_comment_author_same_as_post_author($comment_id)) {
                    return;
                }
                self::on_comment_posted($comment_id);
            }
        }, 10, 1);
    }

    private static function is_comment_author_same_as_post_author($comment_id) {
        // Get the comment by its ID
        $comment = \get_comment($comment_id);
        if (!$comment) {
            return false; // Comment does not exist
        }
        //$comment_user_id = $comment->user_id;
        // Get the email of the comment author
        //$commentAuthorEmail = $comment->comment_author_email;

        // If the comment does not have an email, return false
       // if (empty($commentAuthorEmail)) {
         //   return false;
       // }

        // Fetch the user by email
        //$user = \get_user_by('email', $commentAuthorEmail);
        //if (!$user) {
          //  return false; // User does not exist
       // }

        // Get the post associated with the comment
        $post = \get_post($comment->comment_post_ID);
        if (!$post) {
            return false; // Post does not exist
        }

        // Compare the user ID of the comment author with the post author's user ID
        return $comment->user_id === $post->post_author;
    }

    public static function send_response_to_post_comment($comment_content, $post_id)
    {

        $comment_content = str_replace('```', '###TRIPLE_BACKTICK###', $comment_content);
        $comment_data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => "Ion",
            'comment_content' => $comment_content,
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => User::get_ion_user_id(),
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

    public static function on_comment_posted($comment_id)
    {
            $comment = \get_comment($comment_id);
            $Prompt = new Prompt();
            $Prompt->init_this_prompt($comment_id, "created on mothership");
            $Prompt->send_self_to_ChatGPT();
            self::send_response_to_post_comment($Prompt->response, $comment->comment_post_ID);
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