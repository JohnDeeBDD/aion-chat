<?php

namespace AionChat\Tests;

use AionChatMothership\User;

class UserTest extends \Codeception\TestCase\WPTestCase {

    public function testGetAionAssistantEmail() {
        $expected_email = "assistant@aion.garden";
        $actual_email = \AionChat\User::get_aion_assistant_email();
        $this->assertEquals($expected_email, $actual_email, "The emails should match.");
    }

    public function test_User_force_return_user_id() {
        /* The purpose of this function is that when given an email address, it either returns the user ID or creates a new user and returns that id.
        */
        $email = 'new_user@example.com';
        $user_id = User::force_return_user_id($email);
        $this->assertIsInt($user_id, "The returned user ID should be an integer.");
    }

    public function testDoesAionAssistantUserExistMethodExistence()
    {
        $this->assertTrue(
            method_exists(\AionChat\User::class, 'does_aion_assistant_user_exist'),
            'The method does_aion_assistant_user_exist does not exist in the AionChat\User class.'
        );
    }


    public function testAionAssistantUserExistenceBeforeAndAfterCreation() {
        $email = "assistant@aion.garden";

        // Assert that the user does not exist initially
        $userExistsBefore = \AionChat\User::get_aion_assistant_user_id($email);
        $this->assertFalse($userExistsBefore, "Initially, the user with email {$email} should not exist.");

        // Create a user with the specified email
        $this->factory->user->create(['user_email' => $email]);


        // Assert that the user exists after creation
        $userExistsAfter = \AionChat\User::get_aion_assistant_user_id($email);
        $this->assertTrue(is_int($userExistsAfter), "After creation, the user with email {$email} should exist.");
    }

}