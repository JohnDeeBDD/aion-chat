<?php

namespace AionChat;

/**
 * Utility class for deleting all comments associated with a given post ID.
 *
 * This class provides a static method to delete all comments of a post,
 * including comments with all statuses (approved, pending, spam, trash).
 *
 * @package AionChat
 */
class CommentDeletionUtility{

    /**
     * Deletes all comments associated with a specific post ID.
     *
     * This method retrieves all comments linked to the specified post ID,
     * regardless of their status, and deletes them forcefully.
     *
     * @param int $post_id The ID of the post whose comments should be deleted.
     * @return bool True if comments were found and deleted, false otherwise (including invalid post ID).
     */
    public static function do_delete_all_comments($post_id) {
        // Validate and sanitize post ID
        $post_id = \absint($post_id);
        if(!$post_id) {
            return false; // Invalid post ID
        }

        // Get all comments associated with the post ID, including spam
        $comments = \get_comments(array(
            'post_id' => $post_id,
            'status' => 'all', // Include all comment statuses
            'number' => '', // Get all comments
        ));

        // Check if there are comments to delete
        if (empty($comments)) {
            return false; // No comments found
        }

        // Loop through each comment and force delete it
        foreach($comments as $comment) {
            // For spam comments, mark them as deleted explicitly
           // if ($comment->comment_approved === 'spam') {
           //     \wp_spam_comment($comment->comment_ID);
           // }
            \wp_delete_comment($comment->comment_ID, true); // Force delete
        }

        return true; // Successfully deleted all comments
    }

}
