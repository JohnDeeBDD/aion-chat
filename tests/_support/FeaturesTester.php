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
class FeaturesTester extends \Codeception\Actor
{
    use _generated\FeaturesTesterActions;

    /**
     * @Given there are two servers
     */
     public function thereAreTwoServers(){
         $I = $this;
         $servers = json_decode(file_get_contents("/var/www/html/wp-content/plugins/aion-chat/servers.json"));
         $I->amOnUrl("http://" . $servers[0]);
         $I->see("Mothership");

         $I->amOnUrl("http://" . $servers[1]);
         $I->see("RemoteNode");
     }

    /**
     * @Then I see the servers are setup correctly
     */
     public function I_ConfigureTheChatRooms()
     {
         return true;
         throw new \PHPUnit\Framework\IncompleteTestError("Step `I see the servers are setup correctly` is not defined");
     }

    /**
     * @When /^I go to the other servers$/
     */
    public function iGoToTheOtherServers()
    {
        return true;
        throw new \PHPUnit\Framework\IncompleteTestError("Step 3 is not defined");

    }

}
