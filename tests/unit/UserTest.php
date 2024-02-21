<?php

namespace AionChat\Tests;

class UserTest extends \Codeception\TestCase\WPTestCase {

    public function testGetIonEmail() {
        $expected_email = "ion@ioncity.ai";
        $actual_email = \AionChat\User::get_ion_email();
        $this->assertEquals($expected_email, $actual_email, "The emails should match.");
    }

    public function testForceReturnUserId() {
        $email = 'new_user@example.com';
        $user_id = \AionChat\User::force_return_user_id($email);
        $this->assertIsInt($user_id, "The returned user ID should be an integer.");
    }

    /**
     * @test
     */
    public function IonShouldBeCreatedUponActivation(){
        /*
         *     Scenario: There is no Ion user
    Given there is no user with the email address "jiminac@aol.com"
    When the plugin is activated
    Then a user should be created with the email "jiminac@aol.com", username "Ion"
And an email should be sent to Ion with a password
And the remote fires a ping to the mothership

         */
    }


}