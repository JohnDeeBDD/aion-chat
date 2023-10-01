<?php

require_once('/var/www/html/wp-content/plugins/email-tunnel/src/EmailTunnel/autoloader.php');
class TunnelEntranceTest extends \Codeception\TestCase\WPTestCase{

	/**
	 * @test
	 * setEntranceCredentials() test
	 */
	public function setEntranceCredentialsTest(){
                $Entrance = new \EmailTunnel\TunnelEntrance();
                $url = "https://somesite.com";
                $code = "123456";
                $remoteUserName = "Bob";
                $Entrance->setEntranceCredentials($url, $code, $remoteUserName);
                $option = get_option('email_tunnel_entrance_creds');
                $expectedReturn = 
                        [
                                ['url' => 'https://somesite.com', 'code' => '123456', 'remote_user_name' => 'Bob', 'status' => 'not connected' ]
                        ];                
                $this->assertEquals($expectedReturn, $option);

                $url = "https://some-other-site.com";
                $code = "0987654";
                $Entrance->setEntranceCredentials($url, $code, $remoteUserName);
                $option = get_option('email_tunnel_entrance_creds');
                $expectedReturn =
                        [ 
                                ['url' => 'https://somesite.com', 'code' => '123456', 'remote_user_name' => 'Bob', 'status' => 'not connected' ],
                                ['url' => 'https://some-other-site.com', 'code' => '0987654', 'remote_user_name' => 'Bob', 'status' => 'not connected' ]
                        ];
                $this->assertEquals($expectedReturn, $option);
	}

    /**
     * @test
     * getSelectedEntrance() test
     */
    public function getSelectedEntranceTest(){
        //Given there is an entrance cred
        //And that the entrance cred is selected
        $data = $this->getMockDummyEntranceData(); //includes three creds, one of which is "connected"
        update_option('email_tunnel_entrance_creds', $data);

        //When getSelectedEntrance() is called
        $Entrance = new EmailTunnel\TunnelEntrance();
        $selectedEntrance = $Entrance->getSelectedEntrance();

        //Then the selected site's url, code, and remote user-name should be returned
        $mockDataSelectedUrl = "https://some-other-site.com";
        //var_dump($entranceUrl);die();
        $this->assertEquals($mockDataSelectedUrl, $selectedEntrance['url']);
    }


	/**
	 * @test
     * setSelectedEntrance() test
	 * it should select a certain entrance by setting it's status from "not connected" to "connected"
	 */
        public function setSelectedEntranceTest(){
            //Given there is an entrance cred
            //And that the entrance cred is NOT selected
            $data = $this->getMockDummyEntranceData(); //includes three creds, one of which is "connected"
            update_option('email_tunnel_entrance_creds', $data);
            $mockDataNotSelectedUrl = "https://some-third-site.com";

            //When getSelectedEntrance() is called
            $Entrance = new \EmailTunnel\TunnelEntrance();
            $selectedEntrance = $Entrance->getSelectedEntrance();

            //Then the result should not be the not selected url
            $this->assertNotEquals($mockDataNotSelectedUrl, $selectedEntrance['url']);

            //When setSelectedEntrance() is called with the unselected entrance
            $Entrance->setSelectedEntrance($mockDataNotSelectedUrl);
            //And getSelectedEntrance() is called
            //Then the new URL should be selected
            $selectedEntrance = $Entrance->getSelectedEntrance();
            $this->assertEquals($mockDataNotSelectedUrl, $selectedEntrance['url']);
	 }
	 

	  
	 /**
	  * @test edge case "no connected site"
      * getSelectedEntrance() should return false
	  */
	 public function getSelectedEntrance_case_noConnectedSite(){
	     //Given there are some exit creds
         //And none of them are "selected"
	     $Entrance = new \EmailTunnel\TunnelEntrance();

	     $url = "https://somesite.com";$code = "123456";$remoteUserName = "Bob";
         $Entrance->setEntranceCredentials($url, $code, $remoteUserName);

         $url = "https://some-other-site.com"; $code = "0987654";
         $Entrance->setEntranceCredentials($url, $code, $remoteUserName);

         //When getSelectedEntrance() is called
         $entranceUrl = $Entrance->getSelectedEntrance();

         //Then "false" should be returned
         $this->assertEquals(FALSE, $entranceUrl);
	  }
	  
        /**
         * @test getFilteredEntranceCredsForFrontEndUITableData()
         * it should provide entrance cred data for the frontend
         */
        public function getFilteredEntranceCredsForFrontEndUITableDataTest(){
                //filter the data for viewing. The "code" shouldn't emit.
                $data = $this->getMockDummyEntranceData();
                update_option('email_tunnel_entrance_creds', $data);
                
                //should be the same minus the "code" part
                $expectedReturn = 
                      [ 
                        ['url' => 'https://somesite.com', 'status' => 'not connected', 'created' => '04/05/2022', 'emails' => 666, 'revoke' => 'https://somesite.com' ],
                        ['url' => 'https://some-other-site.com', 'status' => 'connected', 'created' => '04/05/2022', 'emails' => 666, 'revoke' => 'https://some-other-site.com' ],
                        ['url' => 'https://some-third-site.com', 'status' => 'not connected', 'created' => '04/05/2022', 'emails' => 666, 'revoke' => 'https://some-third-site.com' ],
                      ];
                      
                $Entrance = new \EmailTunnel\TunnelEntrance();
                $actual = $Entrance->getFilteredEntranceCredsForFrontEndUITableData();
                
                $this->assertEquals($expectedReturn, $actual);
                
        }

    /**
     * @test
     * removeEntranceCred() test
     */
        public function removeEntranceCredTest(){
            //Given there are some entrances in the database:
            $data = $this->getMockDummyEntranceData();
            update_option('email_tunnel_entrance_creds', $data);

            //When a particular site is removed:
            $Entrance = new \EmailTunnel\TunnelEntrance();
            $siteToRemove = "https://some-other-site.com";
            $functionResult = $Entrance->removeEntranceCred($siteToRemove);

            //Then the site should no longer be in the result
            $expectedResult =
                [
                    ['url' => 'https://some-third-site.com', 'code' => '333', 'remote_user_name' => 'Codeception', 'status' => 'not connected' ],
                    ['url' => 'https://somesite.com', 'code' => '111', 'remote_user_name' => 'Codeception', 'status' => 'not connected' ],

                ];
            $this->assertEqualsCanonicalizing($expectedResult, $functionResult);

            //And the entry for the site should no longer be in the database:
            $optionFromDB = get_option('email_tunnel_entrance_creds');
            $this->assertEqualsCanonicalizing($expectedResult, $optionFromDB);
        }
        
        private function getMockDummyEntranceData(){
                $mockData =
                        [
                            ['url' => 'https://somesite.com', 'code' => '111', 'remote_user_name' => 'Codeception', 'status' => 'not connected' ],
                            ['url' => 'https://some-other-site.com', 'code' => '222', 'remote_user_name' => 'Codeception', 'status' => 'connected' ],
                            ['url' => 'https://some-third-site.com', 'code' => '333', 'remote_user_name' => 'Codeception', 'status' => 'not connected' ],
                        ];
                return $mockData;
        }
}