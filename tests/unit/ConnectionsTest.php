<?php

require_once('/var/www/html/wp-content/plugins/email-tunnel/src/EmailTunnel/autoloader.php');
class ConnectionsTest extends \Codeception\TestCase\WPTestCase
{

    /**
     * @test
     * this test defines the "email_tunnel_site_status" site option
     */
    public function itShouldSetAndGetSiteStatus(){
        \EmailTunnel\Connections::setSiteStatus("exit");
        $this->assertEquals("exit", \EmailTunnel\Connections::getSiteStatus());

        \EmailTunnel\Connections::setSiteStatus("entrance");
        $this->assertEquals("entrance", \EmailTunnel\Connections::getSiteStatus());

        \EmailTunnel\Connections::setSiteStatus("not connected");
        $this->assertEquals("not connected", \EmailTunnel\Connections::getSiteStatus());
    }

    /**
     * @test
     * edge case: nothing in the database
     * expectation: it should return "not connected"
     */
    public function itShouldReturnNotConnectedIfDBIsEmpty(){
        $this->assertEquals("not connected", \EmailTunnel\Connections::getSiteStatus());
    }



}