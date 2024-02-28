<?php
class BootstrapTest extends \Codeception\TestCase\WPTestCase{

    public function test_users_created() {
        // Check if the user "Codeception" exists
        $user1 = get_user_by('email', 'codeception@email.com');
        $this->assertNotNull($user1);
        $this->assertEquals('Codeception', $user1->user_login);

        // Check if the user "Ion" exists
        $user2 = get_user_by('email', 'jiminac@aol.com');
        $this->assertNotNull($user2);
        $this->assertEquals('Ion', $user2->user_login);
    }

    public function test_post_created() {
        // Check if the post "First Chat" exists
        $posts = get_posts([
            'title' => 'First Chat',
            'post_type' => 'post',
        ]);
        $this->assertNotEmpty($posts);

        $post = array_shift($posts);
        $this->assertEquals('First Chat', $post->post_title);
        $this->assertEquals('You are a helpful assistant.', $post->post_content);
    }
}
