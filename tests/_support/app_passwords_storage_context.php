<?php

use _generated\AcceptanceTesterActions;
use Codeception\Actor;

class app_passwords_storage_context extends Actor{
    use AcceptanceTesterActions;

    /**
     * @Given /^Ion is a user on the mothership$/
     */
    public function ionIsAUserOnTheMothership()
    {
        $I = $this;
        $file = file_get_contents("/var/www/html/wp-content/plugins/aion-chat/servers.json");
        $IPs = json_decode($file);
        global $AionChat_mothership_url;
        $AionChat_mothership_url = "http://" . $IPs[0];
        $I->reconfigureThisVariable(["url" => $AionChat_mothership_url]);
        $I->loginAsAdmin();
        $I->amOnPage("/wp-admin/users.php");
        $I->fillField("#user-search-input", "ion@ioncity.ai");
        $I->click("#search-submit");
        $I->see("Carlton Young");
    }

    /**
     * @When /^I am on the user\-edit\.php page$/
     */
    public function iAmOnTheUserEditPhpPage()
    {
        $I = $this;
        $I->moveMouseOver( '.username' );
        $I->click("Edit");
        $I->see("Edit User Ion");
    }

    /**
     * @Then /^I should see the add new app password div$/
     */
    public function iShouldSeeTheAddNewAppPasswordDiv()
    {
        $I = $this;
        $I->scrollTo("#application-passwords-storage");
        $I->see("No remote application passwords.");
    }

    /**
     * @Given /^I am logged in as an admin$/
     */
    public function iAmLoggedInAsAnAdmin()
    {
        $I = $this;
        $file = file_get_contents("/var/www/html/wp-content/plugins/aion-chat/servers.json");
        $IPs = json_decode($file);
        global $AionChat_mothership_url;
        $AionChat_mothership_url = "http://" . $IPs[0];
        $I->reconfigureThisVariable(["url" => $AionChat_mothership_url]);
        $I->loginAsAdmin();
    }

    /**
     * @When /^I navigate to the user\-edit\.php page$/
     */
    public function iNavigateToTheUserEditPhpPage()
    {
        $I = $this;
        $I->amOnPage("/wp-admin/users.php");
        $I->fillField("#user-search-input", "ion@ioncity.ai");
        $I->click("#search-submit");
        $I->see("Carlton Young");
        $I->moveMouseOver( '.username' );
        $I->click("Edit");
        $I->see("Edit User Ion");
    }

    /**
     * @Then /^I should see the \'([^\']*)\' div$/
     */
    public function iShouldSeeTheDiv($arg1)
    {
        $I = $this;
        $I->scrollTo("#application-passwords-storage");
        $I->see("Ion Chat Application Passwords");
    }

    /**
     * @Given /^I am on the user\-edit\.php page with the \'([^\']*)\' div visible$/
     */
    public function iAmOnTheUserEditPhpPageWithTheDivVisible($divId)
    {
        $I = $this;
        $I->amOnPage("/wp-admin/users.php");
        $I->fillField("#user-search-input", "ion@ioncity.ai");
        $I->click("#search-submit");
        $I->see("Carlton Young");
        $I->moveMouseOver( '.username' );
        $I->click("Edit");
        $I->see("Edit User Ion");
        $I->scrollTo("#application-passwords-storage");
        $I->see("Ion Chat Application Passwords");
    }


    /**
     * @When /^I fill in the application password data fields and submit$/
     */
    public function iFillInTheApplicationPasswordDataFieldsAndSubmit()
    {
        $I = $this;
        $remoteSiteUrl = 'https://example.com'; // Replace with a suitable test URL
        $remoteUserName = 'testuser'; // Replace with a suitable test username
        $applicationPassword = 'testpassword'; // Replace with a suitable test password

        $I->fillField("#remote-site-url", $remoteSiteUrl);
        $I->fillField("#remote-user-name", $remoteUserName);
        $I->fillField("#remote-application-password", $applicationPassword);
        $I->executeJS("submitRemoteAppPasswordForm();"); // Triggering the JavaScript function directly
    }


    /**
     * @Then /^the new application password should be stored$/
     */
    public function theNewApplicationPasswordShouldBeStored()
    {
        throw new IncompleteTestError();
    }

    /**
     * @Given /^I should see a confirmation message$/
     */
    public function iShouldSeeAConfirmationMessage()
    {
        throw new IncompleteTestError();
    }

    /**
     * @Given /^I have stored application passwords$/
     */
    public function iHaveStoredApplicationPasswords()
    {
        throw new IncompleteTestError();
    }

    /**
     * @When /^I navigate to the application passwords section$/
     */
    public function iNavigateToTheApplicationPasswordsSection()
    {
        throw new IncompleteTestError();
    }

    /**
     * @Then /^I should see a list of my stored application passwords$/
     */
    public function iShouldSeeAListOfMyStoredApplicationPasswords()
    {
        throw new IncompleteTestError();
    }

    /**
     * @When /^I choose to delete an existing application password$/
     */
    public function iChooseToDeleteAnExistingApplicationPassword()
    {
        throw new IncompleteTestError();
    }

    /**
     * @Then /^the application password should be removed$/
     */
    public function theApplicationPasswordShouldBeRemoved()
    {
        throw new IncompleteTestError();
    }
}