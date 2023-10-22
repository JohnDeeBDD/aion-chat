<?php
namespace IonChat\Tests;

class UserTest extends \Codeception\TestCase\WPTestCase {

    public function testGetIonEmail() {
        $expected_email = "jiminac@aol.com";
        $actual_email = \IonChat\User::get_ion_email();
        $this->assertEquals($expected_email, $actual_email, "The emails should match.");
    }

    public function testGetIonUserId() {

        $expected_user_id = 3;
        $actual_user_id = \IonChat\User::get_ion_user_id();
        $this->assertEquals($expected_user_id, $actual_user_id, "The user IDs should match.");
    }

    public function testIsIonUser() {
        $is_ion_user = \IonChat\User::is_ion_user(3);
        $this->assertTrue($is_ion_user, "The user should be identified as an ion user.");
    }

    public function testForceReturnUserId() {
        $email = 'new_user@example.com';
        $user_id = \IonChat\User::force_return_user_id($email);
        $this->assertIsInt($user_id, "The returned user ID should be an integer.");
    }
}
