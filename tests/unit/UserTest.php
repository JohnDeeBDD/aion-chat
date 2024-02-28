<?php

namespace AionChat\Tests;

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
        $user_id = \AionChat\User::force_return_user_id($email);
        $this->assertIsInt($user_id, "The returned user ID should be an integer.");
    }

    public function test_mothership_user_receive_store_application_password_request(){

    }


}