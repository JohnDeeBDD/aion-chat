<?php

use _generated\AcceptanceTesterActions;

class test_stubs_are_deployed_context extends \Codeception\Actor
{
    use AcceptanceTesterActions;

    /**
     * @Given /^appropriate test stubs have been deployed on the remote$/
     */
    public function appropriateTestStubsHaveBeenDeployedOnTheRemote()
    {
        $I = $this;
        global $IonChat_mothership_url;
        global $IonChat_remote_node_url;
        //$IonChat_mothership_url = "https://ioncity.ai";
        $I->amOnUrl($IonChat_remote_node_url);
        $I->amOnPAge("/aion-conversation/ion-home/");
        $I->see("I am an Aion named Ion");
    }

    /**
     * @When /^I make a comment on the remote$/
     */
    public function iMakeACommentOnTheRemote()
    {
        $I = $this;
        $I->amOnPAge("/aion-conversation/ion-home/");
        $I->fillField("comment", "Greetings Ion. When new programmers create their first program, they are often taught to output a particular phrase of greeting. What is that phrase?");
        $I->click("Post Comment");
    }

    /**
     * @Then /^I should see an intelligent response from Ion on the remote$/
     */
    public function iShouldSeeAnIntelligentResponseFromIonOnTheRemote()
    {
        $I = $this;
        try {
            $I->see("World");
        } catch (exception $e) {
            $I->see("world");
        }
    }

    /**
     * @Given /^the plugin is setup on the mothership$/
     */
    public function thePluginIsSetupOnTheMothership()
    {
        $I = $this;
        $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
        $IPs = json_decode($file);

        global $IonChat_mothership_url;
        global $IonChat_remote_node_url;
        $IonChat_mothership_url = "http://" . $IPs[0];
        $I->amOnUrl($IonChat_mothership_url);
        $I->see("Mothership");
        $I->amOnPage("/?create_stub_posts=1");
        //$IonChat_mothership_url = "https://ioncity.ai";
        $IonChat_remote_node_url = "http://" . $IPs[1];
        $I->amOnUrl($IonChat_remote_node_url);
        $I->loginAsAdmin();
        $I->see("RemoteNode");
        $I->amOnPage("/?create_stub_posts=1");
    }

    /**
     * @Given /^appropriate test stubs have been deployed on the mothership$/
     */
    public function appropriateTestStubsHaveBeenDeployedOnTheMothership()
    {
        $I = $this;
        global $IonChat_mothership_url;
        global $IonChat_remote_node_url;
        //$IonChat_mothership_url = "https://ioncity.ai";
        $I->amOnUrl($IonChat_mothership_url);
        $I->amOnPAge("/aion-conversation/ion-home/");
        $I->see("Hello! I am an Aion named Ion.");
    }

    /**
     * @When /^I make a comment on the mothership$/
     */
    public function iMakeACommentOnTheMothership()
    {
        $I = $this;
        global $IonChat_mothership_url;
        $I->amOnUrl($IonChat_mothership_url);
        $I->reconfigureThisVariable(["url" => $IonChat_mothership_url]);
        $I->loginAsAdmin();
        $I->amOnPAge("/aion-conversation/ion-home/");
        $I->fillField("comment", "Greetings Ion. When new programmers create their first program, they are often taught to output a particular phrase of greeting. What is that phrase?");
        $I->click("Post Comment");
    }

    /**
     * @Then /^I should see an intelligent response from Ion on the mothership$/
     */
    public function iShouldSeeAnIntelligentResponseFromIonOnTheMothership()
    {
        $I = $this;
        try {
            $I->see("World");
        } catch (exception $e) {
            $I->see("world");
        }
    }

    /**
     * @Given /^the plugin is setup on the servers$/
     */
    public function thePluginIsSetupOnTheServers()
    {
        $I = $this;

        //require_once("/var/www/html/wp-content/plugins/ion-chat/src/IonChat/Plugin.class.php");
        global $IonChat_mothership_url;
        global $IonChat_remote_node_url;
        //$IonChat_mothership_url = "https://ioncity.ai";
        $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
        $IPs = json_decode($file);
        $IonChat_mothership_url = "http://" . $IPs[0];
        $IonChat_remote_node_url = "http://" . $IPs[1];
        global $IonChatProtocal;
        if (!isset($IonChatProtocal)) {
            $IonChatProtocal = "remote_node";
        }


        //$IonChat_mothership_url = "https://ioncity.ai";

        $I->amOnUrl($IonChat_mothership_url);
        $I->see("Mothership");
        $I->amOnPage("/?create_stub_posts=1");
        $I->amOnUrl($IonChat_remote_node_url);
        $I->loginAsAdmin();
        $I->see("RemoteNode");
        $I->amOnPage("/?create_stub_posts=1");
    }
}