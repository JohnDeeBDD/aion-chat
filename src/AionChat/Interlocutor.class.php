<?php
namespace AionChat;

use AionChatMothership\HandleJSON;

/**
 * Class Interlocutor
 *
 * This class handles the interlocutor logic for comments in a WordPress environment.
 * It sets the appropriate interlocutor based on the post author and user roles,
 * and controls who is allowed to comment on posts.
 */
class Interlocutor {

    public static function get_post_author_id_from_comment($Comment) {
        // Ensure the input is a WP_Comment object
        if (!is_a($Comment, 'WP_Comment')) {
            // Throw a WP_Error if the comment object is invalid
            return new \WP_Error('invalid_comment_object', 'Invalid comment object.');
        }

        // Get the post ID associated with the comment
        $post_id = $Comment->comment_post_ID;

        // Get the post object from the post ID
        $post = \get_post($post_id);

        // Check if the post object exists
        if (!$post) {
            // Throw a WP_Error if the post object is invalid
            return new \WP_Error('invalid_post_id', 'Invalid post ID.');
        }

        // Return the post author's ID
        return $post->post_author;
    }

    /**
     * Determines the next speaker based on the last comment.
     *
     * @param int $post_id The ID of the post where the speaker should be determined.
     * @param int $interlocutor_user_id The user ID of the interlocutor.
     *
     * @return mixed The user ID of the next speaker, 'CLOSED', or 'OPEN'.
     */
    public static function determine_next_speaker($post_id, $interlocutor_user_id) {
        // Check if comments are open for this post
        if (!comments_open($post_id)) {
            return 'CLOSED';
        }

        // Fetch all comments for the given post ID
        $comments = \get_comments(array(
            'post_id' => $post_id,
            'status' => 'approve',
            'orderby' => 'comment_date',
            'order' => 'DESC',
            'number' => 1 // Only get the last comment
        ));

        // If there are no comments, return 'OPEN'
        if (empty($comments)) {
            return 'OPEN';
        }

        // Get the last comment
        $last_comment = $comments[0];
        $last_comment_user_id = intval($last_comment->user_id);

        // Get the post author's user ID
        $post_author_id = intval(get_post_field('post_author', $post_id));

        // Determine the next speaker based on who made the last comment
        if ($last_comment_user_id === $post_author_id) {
            // If the post author made the last comment, the interlocutor is the next speaker
            return $interlocutor_user_id;
        } else {
            // Otherwise, the post author is the next speaker
            return $post_author_id;
        }
    }


    /**
     * WordPress action handler before a comment is posted.
     *
     * Checks if there is post meta data for the interlocutor, and ensures only
     * the post author or the interlocutor can comment. If not set, it assigns an interlocutor.
     *
     * @param array $comment_data The comment data array containing user_id and comment_post_ID.
     *
     * @return array Modified comment data.
     */
    public static function set_interlocutor($comment_data) {
        // Check if there is post meta data for this post "_aion_chat_interlocutor_user_id"
        $interlocutor = \get_post_meta($comment_data['comment_post_ID'], '_aion_chat_interlocutor_user_id', true);
        $post_author_id = \intval(\get_post_field('post_author', $comment_data['comment_post_ID']));

        if (!$interlocutor){
            if ($comment_data['user_id'] === $post_author_id) {
                \update_post_meta($comment_data['comment_post_ID'], '_aion_chat_interlocutor_user_id', User::get_aion_assistant_user_id());
            } else {
                $post_author = \get_userdata($post_author_id);
                if (in_array('aion', $post_author->roles)) {
                    \update_post_meta($comment_data['comment_post_ID'], '_aion_chat_interlocutor_user_id', $comment_data['user_id']);
                } else {
                    if($post_author_id === User::get_aion_assistant_user_id()){
                        \update_post_meta($comment_data['comment_post_ID'], '_aion_chat_interlocutor_user_id', User::get_Aion_user_id());
                    }else{
                        \update_post_meta($comment_data['comment_post_ID'], '_aion_chat_interlocutor_user_id', User::get_aion_assistant_user_id());
                    }
                }
            }
        }

        return $comment_data;
    }

}