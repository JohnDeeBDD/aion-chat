<?php
class InterlocutorTest extends \Codeception\TestCase\WPTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        require_once('/var/www/html/wp-content/plugins/aion-chat/src/AionChat/autoloader.php');
        require_once('/var/www/html/wp-content/plugins/aion-mother/src/AionChatMothership/autoloader.php');
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

        // Contributor's first comment
        $comment_id =  wp_insert_comment([
            'comment_post_ID' => $post_id,
            'comment_author' => 'TestUser',
            'user_id' => $new_user_id,
            'comment_content' => 'What is the capital of France?',
            'comment_approved' => 1,
        ]);

        return [$post_id, $comment_id];
    }

    /**
     * @test
     * it should test for when a user already exists
     */
    public function test_force_set_interlocutor_id_user_exists() {
        // Create a user for the test
        $email = 'existinguser@example.com';
        $username = 'existinguser';
        $user_id = $this->factory->user->create([
            'user_login' => $username,
            'user_email' => $email,
        ]);

        // Call the method
        $result = \AionChatMothership\Interlocutor::force_set_user_id($email);

        // Assert that the returned user ID is the same as the existing user
        $this->assertEquals($user_id, $result);
    }

    /**
     * @test
     * it should create a new user when the email does not exist
     */
    public function test_force_set_interlocutor_id_user_does_not_exist() {
        // Define a non-existing user email
        $email = 'newuser@example.com';

        // Ensure no user exists with this email
        $this->assertFalse(email_exists($email));

        // Call the method
        $result = \AionChatMothership\Interlocutor::force_set_user_id($email);

        // Assert that the result is a valid user ID
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);

        // Assert that the user now exists in the database
        $user = get_user_by('id', $result);
        $this->assertNotNull($user);
        $this->assertEquals($email, $user->user_email);
        $this->assertStringStartsWith('interloc_', $user->user_login);
    }

    /**
     * @test
     * it should return a WP_Error when an invalid email is provided
     */
    public function test_force_set_interlocutor_id_invalid_email() {
        // Define an invalid email string
        $email = 'invalid-email-string';

        // Call the method
        $result = \AionChatMothership\Interlocutor::force_set_user_id($email);

        // Assert that the result is a WP_Error
        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertEquals('invalid_email', $result->get_error_code());
    }

    /**
     * @test
     * it should determine the next speaker
     */
    public function determine_next_speaker_test(){
        //if
    }
}
