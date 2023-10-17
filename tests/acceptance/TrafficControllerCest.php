<?php
class TrafficControllerCest
{
    private $mothership_url;
    private $remote_url;

    public function _before(AcceptanceTester $I)
    {
        // Fetch the URLs from the JSON file
        $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
        $IPs = json_decode($file);
        $this->mothership_url = "http://" . $IPs[0];
        $this->remote_url = "http://" . $IPs[1];
    }

    public function testRemoteAndMothershipExist(AcceptanceTester $I)
    {

    }

}
