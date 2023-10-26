<?php
namespace IonChat\Tests;

class UserTest extends \Codeception\TestCase\WPTestCase {

    public function testGetIonEmail() {
        $expected_email = "ion@ioncity.ai";
        $actual_email = \IonChat\User::get_ion_email();
        $this->assertEquals($expected_email, $actual_email, "The emails should match.");
    }

    public function testForceReturnUserId() {
        $email = 'new_user@example.com';
        $user_id = \IonChat\User::force_return_user_id($email);
        $this->assertIsInt($user_id, "The returned user ID should be an integer.");
    }
}
