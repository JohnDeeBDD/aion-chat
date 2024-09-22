<?php

use AionChat\CommentMeta;
use Codeception\TestCase\WPTestCase;

class CommentMetaTest extends WPTestCase
{
    use CommentMeta;

    public function setUp(): void
    {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
    }

    /**
     * Test for setting comment meta from an array of key-value pairs
     *
     * @test
     */
    public function testSetCommentMetaFromKeyValuePairs()
    {
        $userId = $this->factory->user->create();
        wp_set_current_user($userId);


        $postId = wp_insert_post([
            'post_title'   => "Test Post",
            'post_content' => 'Lorem ipsum.',
            'post_status'  => 'publish',
            'post_author'  => $userId,
        ]);

        $commentId = wp_insert_comment([
            'comment_post_ID' => $postId,
            'comment_author'  => 'Test Author',
            'comment_content' => 'This is a test comment',
            'user_id'         => $userId,
        ]);

        $data = [
            ["max_tokens"                  => 123],
            ["model"                       => "SomeModel"],
            ["open_ai_api_key"             => "Abc123Abc123"],
            ["non_settable_key"            => "value_to_ignore"],
            ["reply_strategy"              => "fast"]
        ];

        //This step simulates the HTTP request. It can contain extra data that is not used.
        $this->setCommentMetaFromKeyValuePairs($this->settable_meta_keys, $data, $commentId);

        $this->assertEquals(123, get_comment_meta($commentId, '_aion_chat_max_tokens', true));
        $this->assertEquals("SomeModel", get_comment_meta($commentId, '_aion_chat_model', true));
        $this->assertEquals("Abc123Abc123", get_comment_meta($commentId, '_aion_chat_open_ai_api_key', true));
        $this->assertEmpty(get_comment_meta($commentId, '_aion_chat_non_settable_key', true));
        $this->assertEquals("fast", get_comment_meta($commentId, '_aion_chat_reply_strategy', true));

    }
}