<?php

require_once('/var/www/html/wp-content/plugins/email-tunnel/src/ETM/autoloader.php');
class UserTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @test
     * ETM\User::getUserIDorCreateUser should return the user ID based on Email
     */
    public function itShouldreturnTheUserID(){
        $password = \wp_generate_password();
        $userID = \wp_create_user("usernameo", "Password123$", "emailo@email.com");
        $resultID = ETM\User::getUserIDorCreateUser("emailo@email.com");
        $this->assertEquals($userID, $resultID);
    }

    /**
     * @test
     * it should return the user ID based on Email
     */
    public function itShouldCreateAnewUser(){
        $someNewEmail = "somenewemail@email.com";

        //Give there is an email that is NOT in the database:
        $userID = \get_user_by('email', $someNewEmail);
        $this->assertFalse($userID);

        $User= new ETM\User;
        $resultUserID = $User::getUserIDorCreateUser($someNewEmail);
        $userID = \get_user_by('email', $someNewEmail);
        $userID = $userID->ID;
        $this->assertEquals($userID, $resultUserID);
    }



}