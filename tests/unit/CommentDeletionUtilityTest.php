<?php

class CommentDeletionUtilityTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
        require_once('/var/www/html/wp-content/plugins/aion-mother/src/AionChatMothership/autoloader.php');
    }

    /**
     * @test
     * it should be exist
     */
    public function testClassAndMethodExistence()
    {
        $className = '\AionChat\CommentDeletionUtility';
        $methodName = 'do_delete_all_comments';

        // Check if the class exists
        $this->assertTrue(class_exists($className), "Class {$className} does not exist");

        // If the class exists, check if the method exists in the class
        if (class_exists($className)) {
            $this->assertTrue(method_exists($className, $methodName), "Method {$methodName} does not exist in class {$className}");
        }
    }

    /**
     * @test
     * nothing should happen if there are no comments on the post
     */
    public function nothingShouldHappenTest()
    {
        $post_id = $this->factory()->post->create();

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        // No comments exist, so result should be false
        $this->assertFalse($result, "The method should return false when no comments are found.");
    }

    /**
     * @test
     * it should delete a single comment
     */
    public function itShouldDeleteASingleCommentTest()
    {
        $post_id = $this->factory()->post->create();
        $comment_id = $this->factory()->comment->create(['comment_post_ID' => $post_id]);

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        // Check that the comment was deleted
        $this->assertTrue($result, "The method should return true when a comment is deleted.");
        $this->assertNull(get_comment($comment_id), "The comment should be deleted.");
    }

    /**
     * @test
     * it should delete all comments from a post with 3 comments on it
     */
    public function itShouldDelete3Comments()
    {
        $post_id = $this->factory()->post->create();
        $comment_ids = $this->factory()->comment->create_many(3, ['comment_post_ID' => $post_id]);

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        // Check that all comments were deleted
        $this->assertTrue($result, "The method should return true when comments are deleted.");
        foreach ($comment_ids as $comment_id) {
            $this->assertNull(get_comment($comment_id), "Each comment should be deleted.");
        }
    }

    /**
     * @test
     * it should return false for invalid post ID
     */
    public function itShouldReturnFalseForInvalidPostID()
    {
        $invalid_post_id = -1; // Invalid post ID
        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($invalid_post_id);

        $this->assertFalse($result, "The method should return false for an invalid post ID.");
    }

    /**
     * @test
     * it should delete comments with different statuses
     */
    public function itShouldDeleteCommentsWithDifferentStatuses()
    {
        $post_id = $this->factory()->post->create();
        $approved_comment_id = $this->factory()->comment->create(['comment_post_ID' => $post_id, 'comment_approved' => 1]);
        $pending_comment_id = $this->factory()->comment->create(['comment_post_ID' => $post_id, 'comment_approved' => 0]);
        //$spam_comment_id = $this->factory()->comment->create(['comment_post_ID' => $post_id, 'comment_approved' => 'spam']);

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        $this->assertTrue($result, "The method should return true when mixed-status comments are deleted.");

        // Check if comments are deleted or marked as spam
        $this->assertNull(get_comment($approved_comment_id), "The approved comment should be deleted.");
        $this->assertNull(get_comment($pending_comment_id), "The pending comment should be deleted.");
       // $this->assertFalse(get_comment($spam_comment_id), "The spam comment should be deleted.");
    }


    /**
     * @test
     * it should return false if no post ID is provided
     */
    public function itShouldReturnFalseForNoPostID()
    {
        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments(null);

        $this->assertFalse($result, "The method should return false if no post ID is provided.");
    }



    /**
     * @test
     * it should return false if the post has no comments
     */
    public function itShouldReturnFalseForPostWithNoComments()
    {
        $post_id = $this->factory()->post->create(); // Post with no comments

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        $this->assertFalse($result, "The method should return false when the post has no comments.");
    }

    /**
     * @test
     * it should return false if the post is deleted
     */
    public function itShouldReturnFalseIfPostIsDeleted()
    {
        $post_id = $this->factory()->post->create();
        $this->factory()->comment->create_many(3, ['comment_post_ID' => $post_id]);

        // Now delete the post
        wp_delete_post($post_id, true);

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        $this->assertFalse($result, "The method should return false if the post has been deleted.");
    }


    /**
     * notest
     * it should delete all comments from a post with 300 comments on it
     */
    public function itShouldDelete300Comments()
    {
        $post_id = $this->factory()->post->create();
        $comment_ids = $this->factory()->comment->create_many(300, ['comment_post_ID' => $post_id]);

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        // Check that all comments were deleted
        $this->assertTrue($result, "The method should return true when a large number of comments are deleted.");
        foreach ($comment_ids as $comment_id) {
            $this->assertNull(get_comment($comment_id), "Each comment should be deleted.");
        }
    }

    /**
     * no test
     * it should handle deletion of a large number of comments efficiently
     */
    public function itShouldDeleteLargeNumberOfCommentsEfficiently()
    {
        $post_id = $this->factory()->post->create();
        $comment_ids = $this->factory()->comment->create_many(1000, ['comment_post_ID' => $post_id]); // 1000 comments

        $result = \AionChat\CommentDeletionUtility::do_delete_all_comments($post_id);

        $this->assertTrue($result, "The method should return true when a large number of comments are deleted.");
        foreach ($comment_ids as $comment_id) {
            $this->assertNull(get_comment($comment_id), "Each comment should be deleted.");
        }
    }

}
