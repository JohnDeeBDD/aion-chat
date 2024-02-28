<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{

    private $mothership_url;
    private $remote_node_url;
    private $page_source;

    use _generated\AcceptanceTesterActions;

   /**
    * Define custom actions here
    */
   public function moveToScreenWhereActionIsAvailable($action){
       $roles = $action->roles;
   }

    /**
     * @Given /^the server I_P_s are readable in the servers\.json file$/
     */
    public function theServerI_P_sAreReadableInTheServersJsonFile()
    {
        $I = $this;

        // Describe what this test is going to do
        $I->wantToTest('Where does this go?');
        $I->wantToTest('Traffic Controller Feature');
        //$I->execute("123");


        // Define the path to servers.json
        $serversJsonPath = '/var/www/html/wp-content/plugins/aion-chat/servers.json';

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
                $I->comment('IPs are valid');
            } else {
                throw new Exception("Invalid JSON format");
            }
        } else {
            throw new Exception("servers.json file is not accessible or readable");
        }
    }

    /**
     * @Given /^there is a remote node$/
     */
    public function thereIsARemoteNode()
    {
        $I = $this;
        $I->amOnUrl($this->remote_node_url);
        $I->see("Remote");

    }

    /**
     * @Given /^there is a mothership server$/
     */
    public function thereIsAMothershipServer()
    {
        $I = $this;
        $I->amOnUrl($this->mothership_url);
        $I->see("Mothership");
    }

    /**
     * @Given /^there is a stub post called Chat on the mothership$/
     */
    public function thereIsAStubPostCalledChatOnTheMothership()
    {
        $I = $this;
        $I->amOnUrl($this->mothership_url);
        $I->amOnPage("/?create_stub_post=1");
        $I->amOnPage("/stub/");
        $I->see("You are a helpful assistant.");
        $I->see("Leave a Reply");
    }

    /**
     * @When /^a comment is published on the mothership server$/
     */
    public function aCommentIsPublishedOnTheMothershipServer()
    {
        $I = $this;
        $I->reconfigureThisVariable(["url" => ($this->mothership_url)]);
        $I->amOnUrl($this->mothership_url);
        $I->loginAsAdmin();
        $I->amOnPage("/stub");
        $I->fillField("comment", "In computer programming, a simple phrase is often outputted to the screen, as the programmer's first attempt at creating a program on that system. The phrase is a simple greeting. What is the phrase?");
        $I->clickWithLeftButton("#submit");
    }

    /**
     * @Given /^the comment is of a tracked post$/
     */
    public function theCommentIsOfATrackedPost()
    {
        //todo all posts are tracked at this point
    }

    /**
     * @Then /^a Prompt is created on the mothership server$/
     */
    public function aPromptIsCreatedOnTheMothershipServer()
    {
        $I = $this;
        $I->comment("this happens in the backend");
    }

    /**
     * @Given /^the Prompt is visable in the debug$/
     */
    public function thePromptIsVisableInTheDebug()
    {   //odo
    }

    /**
     * @Given /^the Prompt is sent to ChatGPT$/
     */
    public function thePromptIsSentToChatGPT()
    {
        $I = $this;
        $I->comment(\var_export($this->page_source, true));
    }

    /**
     * @Given /^the mothership server gets the reply from ChatGPT$/
     */
    public function theMothershipServerGetsTheReplyFromChatGPT()
    {
        $I = $this;
        $I->expect("Chat GPT to have already answered a prompt. The correct answer is 'Hello world.' Or some variation." );
        $I->amOnUrl($this->mothership_url);
        $I->amOnPage("/stub");
        try {
            $I->see("world");
        }
        catch(\Exception $e) {
            $I->see("World");
        }
    }

    public function setupPluginOnLocalhost()
    {
        //This is the localhost context

        $this->amOnUrl("http://localhost/");
        $this->loginAsAdmin();
        $this->amOnPage("/wp-admin/");
        $this->see("Ion");
        $command = 'wp post create --post_type=aion-conversation --post_title="TestPost"';
        $postID = ( $this->extractPostNumeral(shell_exec($command)));

        $command = "wp post meta update " . $postID . " aion-chat-instructions 'You are a helpful assistant.'";
        echo(shell_exec($command));

        $command = 'wp user get Assistant --field=ID';
        $IonUserID = (shell_exec($command));
        $command = "wp post update " . $postID . " --post_author=" . $IonUserID;
        echo(shell_exec($command));
        $command = "wp post update " . $postID . " --post_status='publish'";
        echo(shell_exec($command));
        return $postID;
    }

    public function setupPluginOnMothership()
    {
        $remoteNodeIP = $this->getSiteUrls();
        $remoteNodeIP = $remoteNodeIP[0];
        $this->amOnUrl("http://" . $remoteNodeIP);
        $this->loginAsAdmin();
        $this->amOnPage("/wp-admin/");
        $this->see("Mothership");

        $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $remoteNodeIP . " /var/www/html/wp-content/plugins/aion-chat/tests/acceptance/intelligent_response_setup.sh";
        return (shell_exec($command));
    }

    public function setupPluginOnRemoteNode()
    {
        $remoteNodeIP = $this->getSiteUrls();
        $remoteNodeIP = $remoteNodeIP[1];
        $this->amOnUrl("http://" . $remoteNodeIP);
        $this->loginAsAdmin();
        $this->amOnPage("/wp-admin/");
        $this->see("RemoteNode");

        $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $remoteNodeIP . " /var/www/html/wp-content/plugins/aion-chat/tests/acceptance/intelligent_response_setup.sh";
        return(shell_exec($command));
    }

    public function extractPostNumeral($string) {
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

    public function makeAComment($comment)

    {
        $I = $this;
        $I->amOnPage("/aion-conversation/testpost");
        $I->see("Leave a Reply");
        $I->fillField("comment", $comment);
        $I->click("Post Comment");
    }

    public function shouldSeeAnIntelligentResponse($response)
    {
        $I = $this;
        $I->amOnPage("/aion-conversation/testpost");
        $I->see("Leave a Reply");
        $I->see($response);
    }

    public function cleanupAfterRemotenodeIntelligentResponse(){}
}
