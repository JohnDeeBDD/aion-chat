<?php

use _generated\AcceptanceTesterActions;

class test_stubs_are_deployed_context extends \Codeception\Actor
{
    use AcceptanceTesterActions;

    /**
     * @Given /^the plugin is setup on the remote$/
     */
    public function thePluginIsSetupOnTheRemote()
    {
        $I = $this;
        $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
        $IPs = json_decode($file);
        global $dev1IP;
        global $mothershipUrl;
        global $dev2IP;
        $IonChat_mothership_url = "http://" . $IPs[0];
        $I->amOnUrl($IonChat_mothership_url);
        $I->see("Mothership");
        $I->amOnPage("/?create_stub_posts=1");
        //$IonChat_mothership_url = "https://ioncity.ai";
        $IonChat_mothership_url = "http://" . $IPs[1];
        $I->amOnUrl($IonChat_mothership_url);
        $I->loginAsAdmin();
        $I->see("RemoteNode");
        $I->amOnPage("/?create_stub_posts=1");
    }

    /**
     * @Given /^appropriate test stubs have been deployed on the remote$/
     */
    public function appropriateTestStubsHaveBeenDeployedOnTheRemote()
    {
        $I = $this;
        $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
        $IPs = json_decode($file);
        global $dev1IP;
        global $mothershipUrl;
        global $dev2IP;
        //$IonChat_mothership_url = "https://ioncity.ai";
        $I->amOnUrl("http://" . $IPs[1]);
        $I->amOnPAge("/aion-conversation/ion-home/");
        $I->see("Hello! I am an Aion named Ion.");
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


}