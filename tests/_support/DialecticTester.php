<?php

use _generated\AcceptanceTesterActions;
use Codeception\Actor;

class DialecticTester extends Actor
{
    use AcceptanceTesterActions;

    public string $remoteNodeIP;
    public string $mothershipIP;
    public int $Assistant_user_id;
    public int $Aion_user_id;
    public int $ConversationA_post_id;
    public int $ConversationB_post_id;
    public $result; // the result of the last function

    public function __construct(\Codeception\Scenario $scenario)
    {
        parent::__construct($scenario);
        $I = new AcceptanceTester($scenario);
        $I->amOnUrl("https://generalchicken.guru");
        $I->amOnPage("hire-john");
        //$I = $this;
        $siteUrls = $I->getSiteUrls();
        $this->mothershipIP = $siteUrls[0];
        $this->remoteNodeIP = $siteUrls[1];

        // Cleanup from last test:
        $I->executeRemoteCommandAsUbuntu($this->remoteNodeIP, "php /var/www/html/wp-content/plugins/aion-chat/doDeleteTestEtmConnections.php");
    }

    /**
     * @Given /^there are two Aion users: "([^"]*)" and "([^"]*)"$/
     */
    public function verifyAionUsersExistOnRemoteServer($user1, $user2)
    {
        // Check if the users exist on the remote server using WP-CLI
        $this->Assistant_user_id = $this->assertUserExistsOnRemoteServer($user1);
        $this->Aion_user_id = $this->assertUserExistsOnRemoteServer($user2);
    }

    /**
     * Helper function to check if a user exists on the remote server
     *
     * @param string $username The username to check
     * @throws \Exception If the user does not exist on the remote server
     */
    private function assertUserExistsOnRemoteServer($username)
    {
        // Build the WP-CLI command to check the user by username
        $checkUserCommand = "wp user get $username --field=ID --path=/var/www/html";

        // Execute the command remotely
        $output = $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $checkUserCommand);

        // Throw an exception if the user does not exist
        if (empty($output)) {
            throw new \Exception("The user $username does not exist on the remote server at " . $this->remoteNodeIP . ".");
        }
        return $output;
    }

    /**
     * @Given /^there are two Aion Conversation posts: "([^"]*)" authored by "([^"]*)" and "([^"]*)" authored by "([^"]*)"$/
     */
    public function thereAreTwoAionConversationPostsAuthoredByAndAuthoredBy($arg1, $arg2, $arg3, $arg4)
    {
        $I = $this;

        // Get the user IDs for Assistant and Aion
        $assistantUserId = $this->Assistant_user_id;
        $aionUserId = $this->Aion_user_id;

        // Create first Aion Conversation post and store its post ID
        $createFirstPostCommand = "wp post create --post_title='$arg1' --post_type=aion-conversation --post_status=publish --post_author=$assistantUserId --path=/var/www/html --porcelain";
        $this->ConversationA_post_id = $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $createFirstPostCommand);

        if (empty($this->ConversationA_post_id)) {
            throw new \Exception("Failed to create the post '$arg1' authored by Assistant on the remote server.");
        }

        // Create second Aion Conversation post and store its post ID
        $createSecondPostCommand = "wp post create --post_title='$arg3' --post_type=aion-conversation --post_status=publish --post_author=$aionUserId --path=/var/www/html --porcelain";
        $this->ConversationB_post_id = $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $createSecondPostCommand);

        if (empty($this->ConversationB_post_id)) {
            throw new \Exception("Failed to create the post '$arg3' authored by Aion on the remote server.");
        }

        $I->comment("Successfully created two Aion Conversation posts: '$arg1' authored by Assistant and '$arg3' authored by Aion.");
    }

    /**
     * @Given /^"([^"]*)" is the interlocutor for "([^"]*)", while "([^"]*)" is the interlocutor for "([^"]*)"$/
     */
    public function isTheInterlocutorForWhileIsTheInterlocutorFor($interlocutorA, $postA, $interlocutorB, $postB)
    {
        $I = $this;

        // Get the user IDs for interlocutors
        $assistantUserId = $this->Assistant_user_id;
        $aionUserId = $this->Aion_user_id;

        // Set meta data for ConversationA - Interlocutor is Aion
        $setMetaCommandA = "wp post meta set {$this->ConversationA_post_id} _aion_chat_interlocutor_user_id $aionUserId --path=/var/www/html";
        $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $setMetaCommandA);

        $setMetaCommandA = "wp post meta set {$this->ConversationA_post_id} _aion_chat_interlocutor_post_id " . $this->ConversationB_post_id . " --path=/var/www/html";
        $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $setMetaCommandA);

        // Set meta data for ConversationB - Interlocutor is Assistant
        $setMetaCommandB = "wp post meta set {$this->ConversationB_post_id} _aion_chat_interlocutor_user_id $assistantUserId --path=/var/www/html";
        $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $setMetaCommandB);

        $setMetaCommandA = "wp post meta set {$this->ConversationB_post_id} _aion_chat_interlocutor_post_id " . $this->ConversationA_post_id . " --path=/var/www/html";
        $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $setMetaCommandA);

        $setMetaCommandA = "wp post meta set {$this->ConversationA_post_id} _aion_chat_system_instructions \"'You are a playful friend named James. You are playing the game 20 Questions. In 20 Questions, player one selects an answer to the puzzle, and tells player two either Animal, vegetable, or mineral, depending on which category is most appropriate. Remember, no proper names.'\" --path=/var/www/html";
        $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $setMetaCommandA);

        $setMetaCommandA = "wp post meta set {$this->ConversationB_post_id} _aion_chat_system_instructions \"'You are a playful friend named David. You are playing the game 20 Questions. In 20 Questions, player one selects an answer to the puzzle, and tells player two either Animal, vegetable, or mineral, depending on which category is most appropriate. Remember, no proper names'\" --path=/var/www/html";
        $this->executeRemoteCommandAsUbuntu($this->remoteNodeIP, $setMetaCommandA);

        $I->comment("Interlocutors have been successfully set for $postA and $postB.");
    }

    /**
     * @Given /^I am viewing Conversation A authored by Assistant$/
     */
    public function iAmViewingConversationAAuthoredByAssistant()
    {
        $I = $this;
        // Get IPs for Mothership and Remote Node
        $siteUrls = $this->getSiteUrls();
        $mothershipIP = $siteUrls[0];
        $remoteNodeIP = $siteUrls[1];
        $I->amOnUrl("http://" . $remoteNodeIP . "/aion-conversation/conversationa/");
        $I->see("ConversationA");
    }

    /**
     * @When /^I execute the JavaScript function aion_chat_count_comments\(\)$/
     */
    public function iExecuteTheJavaScriptFunctionAion_chat_count_comments()
    {
        $I = $this;
        // Execute JavaScript and store the result
        $this->result = $I->executeJS('return aion_chat_count_comments();');
    }

    /**
     * @Then the function should return :num1
     */
    public function theFunctionShouldReturn($num1)
    {
        $I = $this;
        $I->assertEquals($num1, $this->result, "The JavaScript function did not return the expected value.");
    }

    /**
     * @Given /^There are zero comments$/
     */
    public function thereAreZeroComments()
    {
        $I = $this;
        $I->assertEquals(0, $I->executeJS('return aion_chat_count_comments();'));
    }

    /**
     * @When /^I make a comment$/
     */
    public function iPostAComment()
    {
        $I = $this;
        //$I->amOnPage("/aion-conversation/" . $this->createdPostTitle);


        $I->see("Leave a Reply");
        $I->fillField("comment", "What is the capital city of France?");
        $I->click("Post Comment");

    }

    /**
     * @When I am on page :arg1
     */
    public function iAmOnPage($arg1)
    {
        $I = $this;
        // Get IPs for Mothership and Remote Node
        $siteUrls = $this->getSiteUrls();
        $mothershipIP = $siteUrls[0];
        $remoteNodeIP = $siteUrls[1];
        $I->amOnUrl("http://" . $remoteNodeIP . "/aion-conversation/conversationa/");
        $I->see("ConversationA");
    }

    /**
     * @Then I see a response
     */
    public function iSeeAResponse()
    {
        //throw new \PHPUnit\Framework\IncompleteTestError("Step `I see a response` is not defined");
    }
    /**
     * @When I make a comment on :arg1
     */
    public function iMakeACommentOn($arg1)
    {
        $I = $this;
        // Get IPs for Mothership and Remote Node
        $siteUrls = $this->getSiteUrls();
        $mothershipIP = $siteUrls[0];
        $remoteNodeIP = $siteUrls[1];
        $I->amOnUrl("http://" . $remoteNodeIP . "/aion-conversation/conversationa/");
        $I->see("ConversationA");
        $I->see("Leave a Reply");
        $I->fillField("comment", "OK. I'll give you the clue, and you and the questions. The clue is mineral.");
        $I->click("Post Comment");
        sleep(5);
    }

}
