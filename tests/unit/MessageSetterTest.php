<?php
class MessageSetterTest extends \Codeception\TestCase\WPTestCase
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

        $className = '\AionChatMothership\MessageSetter';
        $methodName = 'create_messages_array';

        // Check if the class exists
        $this->assertTrue(class_exists($className), "Class {$className} does not exist");

        // If the class exists, check if the method exists in the class
        if (class_exists($className)) {
            $this->assertTrue(method_exists($className, $methodName), "Method {$methodName} does not exist in class {$className}");
        }

    }

    /**
     * @test
     * it should return the correct message array
     */
    public function itShouldReturnTheCorrectMessageArrayTest(){
        // Assuming this method sets up the conversation comments and returns a post ID
        $data = $this->setupConversationComments();
        $post_id = $data[0];

        // The actual returned message array from the method under test
        $returnedMessageArray = \AionChatMothership\MessageSetter::create_messages_array($post_id, "You are a helpful assistant named Roy.");

        // The expected array that the returned array should match
        $expectedMessageArray = [
            [
                "role" => "system",
                "content" => "You are a helpful assistant named Roy."
            ],
            [
                "role" => "user",
                "content" => "What is the tallest structure in that city?"

            ],
            [
                "role" => "assistant",
                "content" => "The capital of France is Paris."
            ],
            [
                "role" => "user",
                "content" => "What is the capital of France?"
            ],
        ];
        $this->assertEquals ($expectedMessageArray, $returnedMessageArray);
    }



    /**
     * setup conversation comments
     */
    private function setupConversationComments(){
        \AionChat\User::add_aion_role();
        \AionChat\User::create_Aion_user();

        // Retrieve the existing Aion user by email
        $user = get_user_by('email', 'aion@aion.garden');
        $user_id = $user->ID;

        // Create a new user with the role of 'contributor'
        $new_user_id = wp_insert_user([
            'user_login' => 'TestUser',
            'user_pass'  => wp_generate_password(),
            'user_email' => 'testuser@email.com',
            'role'       => 'contributor',
            'display_name' => 'TestUser',
        ]);

        // Check if the user was created successfully
        if (is_wp_error($new_user_id)) {
            $this->fail('Failed to create TestUser: ' . $new_user_id->get_error_message());
        }

        // Create a new post
        $post_id = wp_insert_post([
            'post_title'   => 'Test Post',
            'post_content' => 'This is a test post for unit testing.',
            'post_status'  => 'publish',
            'post_author'  => $user_id,
        ]);

        // Check if the post was created successfully
        if (is_wp_error($post_id)) {
            $this->fail('Failed to create post: ' . $post_id->get_error_message());
        }

        //update_post_meta($post_id, "_aion_chat_system_instructions", "You are a helpful assistant doing unit tests.");
        // Add comments to the post to simulate the conversation

        // Contributor's first comment
        wp_insert_comment([
            'comment_post_ID' => $post_id,
            'comment_author' => 'TestUser',
            'user_id' => $new_user_id,
            'comment_content' => 'What is the capital of France?',
            'comment_approved' => 1,
        ]);

        // Aion's first reply
        wp_insert_comment([
            'comment_post_ID' => $post_id,
            'comment_author' => 'Aion',
            'user_id' => $user_id,
            'comment_content' => 'The capital of France is Paris.',
            'comment_approved' => 1,
        ]);

        // Contributor's second comment
        $comment_id = wp_insert_comment([
            'comment_post_ID' => $post_id,
            'comment_author' => 'TestUser',
            'user_id' => $new_user_id,
            'comment_content' => 'What is the tallest structure in that city?',
            'comment_approved' => 1,
        ]);

        return [$post_id, $comment_id];
    }
}
