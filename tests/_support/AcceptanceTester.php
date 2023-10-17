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
    {
        $I = $this;
        $I->reconfigureThisVariable(["url" => ($this->mothership_url)]);
        $I->amOnUrl($this->mothership_url);
        $I->amOnPage("/wp-content/debug.log");
        $I->see("IonChat\Prompt Object");
        $this->page_source = $I->grabPageSource("/wp-content/debug.log");
        $I->comment(\var_export($this->page_source, true));
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

}
