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

    public function testAddAionRole() {
        // Ensure the role does not already exist before running the test
        remove_role('aion');

        // Call the method to add the 'aion' role
        \AionChat\User::add_aion_role();

        // Retrieve the role object
        $aion_role = \get_role('aion');

        // Assert that the role has been created
        $this->assertNotNull($aion_role, "The 'aion' role should have been added.");

        // Assert that the role has the expected capabilities (if any were defined in the add_aion_role method)
        // Example assertion: (Uncomment and adjust based on actual role capabilities defined in add_aion_role)
        // $this->assertEquals($expected_capabilities, $aion_role->capabilities, "The 'aion' role should have the expected capabilities.");

        // Cleanup: Remove the 'aion' role after testing to maintain test isolation
        remove_role('aion');
    }

}