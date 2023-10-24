<?php

use _generated\AcceptanceTesterActions;

class test_stubs_are_deployed_context extends \Codeception\Actor
{
    use AcceptanceTesterActions;

    /**
     * @When /^test stubs are deployed$/
     */
    public function testStubsAreDeployed()
    {
        $I = $this;
        $file = file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json");
        $IPs = json_decode($file);
        global $dev1IP;
        global $mothershipUrl;
        global $dev2IP;
        //$IonChat_mothership_url = "https://ioncity.ai";
        $IonChat_mothership_url = "http://" . $IPs[1];
        $I->amOnUrl($IonChat_mothership_url);
        $I->see("RemoteNode");
        $I->loginAsAdmin();
        $I->amOnPage("/wp-admin/post-new.php");
        $I->fillField("post_title", "TEST POST");
        $I->click("Publish");
        $url = $I->grabFromCurrentUrl();
        $url = "/wp-admin/post.php?post=49&action=edit";
        $parts = parse_url($url);
        parse_str($parts['query'], $query);
        $post_value = intval($query['post']);
        $I->amOnPage("/test-post");
        $I->fillField("comment", "Greetings Ion. When new programmers create their first program, they are often taught to output a particular phrase of greeting. What is that phrase?");
        $I->click("Post Comment");

        try {
            $I->see("World");
        } catch (exception $e) {
            $I->see("world");
        }

    }

    /**
     * @Given /^the plugin is setup$/
     */
    public function thePluginIsSetup()
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
        $I->amOnPage("/?force_delete_all_posts=1");
        //$IonChat_mothership_url = "https://ioncity.ai";
        $IonChat_mothership_url = "http://" . $IPs[1];
        $I->amOnUrl($IonChat_mothership_url);
        $I->loginAsAdmin();
        $I->see("RemoteNode");
        $I->amOnPage("/?force_delete_all_posts=1");
        $I->amOnPage("/wp-admin/edit.php");
        $I->dontSee("TEST POST");
    }
}