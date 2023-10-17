<?php
use Behat\Behat\Context\Context;

class FeatureContext implements Context
{
    private $mothership_url;
    private $remote_node_url;

    /**
     * @Given /^the server IPs are readable in the servers\.json file$/
     */
    public function theServerIPsAreReadableInTheServersJsonFile()
    {
        $I = $this;

        // Describe what this test is going to do
        $I->wantToTest('Read server IPs from servers.json file');

        // Define the path to servers.json
        $serversJsonPath = '/var/www/html/wp-content/plugins/ion-chat/servers.json';

        // Check if the file is accessible and readable
        $I->amGoingTo("Check if the file at {$serversJsonPath} is accessible and readable");
        if (file_exists($serversJsonPath) && is_readable($serversJsonPath)) {

            // Read the JSON content from the file
            $jsonContent = file_get_contents($serversJsonPath);
            $decodedServerIps = json_decode($jsonContent, true);

            $this->mothership_url = "http://" . $decodedServerIps[0];
            $this->remote_node_url = "http://" . $decodedServerIps[1];


            // Validate the JSON content
            $I->expectTo('Have valid JSON content');
            if (json_last_error() === JSON_ERROR_NONE) {
                // Validate IPs here
                $I->comment('IPs are valid');
            } else {
                throw new Exception("Invalid JSON format");
            }
        } else {
            throw new Exception("servers.json file is not accessible or readable");
        }
    }
}
