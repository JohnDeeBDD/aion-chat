<?php

use _generated\AcceptanceTesterActions;

class intelligent_response_from_ion_localhost_context extends \Codeception\Actor

{
    use AcceptanceTesterActions;

    private $createdPostTitle;

    public function __construct(\Codeception\Scenario $scenario)
    {
        parent::__construct($scenario);
        $this->createdPostTitle = "testPost";
    }

    public function __destruct()
    {

    }

    /**
     * @Given /^the plugin is setup on the servers$/
     */
    public function thePluginIsSetupOnTheServers()
    {
        //This is the localhost context
        $I = $this;
        $I->amOnUrl("http://localhost/");
        $I->loginAsAdmin();
        $I->amOnPage("/wp-admin/");
        $I->see("Ion");
        $command = 'wp post create --post_type=aion-conversation --post_title="' . $this->createdPostTitle . '"';
        global $postID;
        $postID = ( $this->extractPostNumeral(shell_exec($command)));

        $command = "wp post meta update " . $postID . " aion-chat-instructions 'You are a helpful assistant.'";
        echo(shell_exec($command));

        $command = 'wp user get Ion --field=ID';
        $IonUserID = (shell_exec($command));
        $command = "wp post update " . $postID . " --post_author=" . $IonUserID;
        echo(shell_exec($command));
        $command = "wp post update " . $postID . " --post_status='publish'";
        echo(shell_exec($command));




    }

    /**
     * @When /^I make a comment$/
     */
    public function iMakeAComment()
    {
        $I = $this;
        $I->amOnPage("/aion-conversation/" . $this->createdPostTitle);
        $I->see("Leave a Reply");
        $I->fillField("comment", "What is the capital city of France?");
        $I->click("Post Comment");

    }

    /**
     * @Then /^I should see an intelligent response from Ion$/
     */
    public function iShouldSeeAnIntelligentResponseFromIon()
    {        $I = $this;
        $I->amOnPage("/aion-conversation/" . $this->createdPostTitle);
        $I->see("Paris");
    }

    /**
     * @When /^the feature test is done the post is deleted$/
     */
    public function theFeatureTestIsDoneThePostIsDeleted()
    {
        global $postID;
        echo(shell_exec("wp post delete $postID --force"));
    }

    private function generateRandomString($length = 10) {
    // Define characters that can be used in the string
    $characters = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
    $charactersLength = count($characters);
    $randomString = '';

    // Generate random string
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
    }

    private function extractPostNumeral($string) {
        // Use preg_match to find a sequence of digits in the string
        if (preg_match('/\b(\d+)\b/', $string, $matches)) {
            // If a match is found, it's stored in $matches
            // $matches[0] would contain the whole matched string,
            // while $matches[1] contains the first captured parenthesized subpattern, which in this case is the numeral.
            return (int)$matches[1]; // Return the numeral as an integer
        } else {
            // Return some default or error value if no numeral is found
            return false;
        }
    }

    /**
     * @When /^I make a comment with text "([^"]*)"$/
     */
    public function iMakeACommentWithText($arg1)
    {
        $I = $this;
        $I->amOnPage("/aion-conversation/" . $this->createdPostTitle);
        $I->see("Leave a Reply");
        $I->fillField("comment", $arg1);
        $I->click("Post Comment");
    }

    /**
     * @Then /^I should see an intelligent response "([^"]*)"$/
     */
    public function iShouldSeeAnIntelligentResponse($arg1)
    {
        $I = $this;
        $I->amOnPage("/aion-conversation/" . $this->createdPostTitle);
        $I->see("Leave a Reply");
        $I->see($arg1);
    }
}