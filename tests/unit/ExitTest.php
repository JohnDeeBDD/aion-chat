<?php

require_once('/var/www/html/wp-content/plugins/email-tunnel/src/EmailTunnel/autoloader.php');
class ExitTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @test
     * it should set the creds
     */
    public function isShouldSetTheCreds()
    {

    }



    /**
     * @refactor
     * removeExitCred() test
     */
    public function removeExitCredTest(){
        //Given there are no exit creds

        //When removeExitCred is called on a particular [non-existent] cred
        $Exit = new \EmailTunnel\TunnelExit();
        $result = $Exit->removeExitCred("http://notinthedb.com", 1);

        //Then false should be returned
        $this->assertFalse($result);

        //Given there are three items in the DB:
        $this->createMockData();
        $expectedResult =
            [
                ['name' => "https://site1.com", 'createdOn' => "04/04/2022", 'emails' => 555, 'status' => 'hello'],
                ['name' => 'https://site3.guru', 'createdOn' => "04/04/2022", 'emails' => 555, 'status' => 'hello'],
            ];

        //When a site is removed
        $Exit->removeExitCred("http://site2.net", 1);

        //Then only two should be remaining in the DB
        $givenResult = $Exit->getExitDataForFrontend(1);
        $this->assertEquals($expectedResult, $givenResult);
    }

    /**
     * @test
     * getApplicationPasswordUUIDByNameTest
     */
    public function getApplicationPasswordUUIDByNameTest(){
        $exampleSiteName = 'email-tunnel:https://somesite.com';
        $pw = WP_Application_Passwords::create_new_application_password(1, ['name' => $exampleSiteName]);
        $uuid = $pw[1]['uuid'];

        $Exit = new \EmailTunnel\TunnelExit();
        $resultUUID = $Exit->getApplicationPasswordUUIDByName($exampleSiteName, 1);
        $this->assertEquals($uuid, $resultUUID);
    }

    private function createMockData(){
        //Given the user has some application passwords:
        $app_pass = WP_Application_Passwords::create_new_application_password( 1, array( 'name' => 'email-tunnel:https://site1.com' ) );
        $app_pass = WP_Application_Passwords::create_new_application_password( 1, array( 'name' => 'email-tunnel:http://site2.net' ) );
        $app_pass = WP_Application_Passwords::create_new_application_password( 1, array( 'name' => 'email-tunnel:https://site3.guru' ) );
    }
}