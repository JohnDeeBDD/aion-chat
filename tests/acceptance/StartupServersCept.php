<?php
$I = new AcceptanceTester($scenario);
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);
$I->loginAsAdmin();
$I->see("WordPress");
//$I->click(".fs-close");
$I->click(".welcome-panel-close");
$I->click(".woocommerce-message-close");
$I->clickWithLeftButton(".fs-close");
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-ion-chat");
//$I->see("Plugin activated.");
$I->click("#activate-ion-chat-mothership");
//$I->see("Plugin activated.");

$I->amOnPage("/wp-admin/admin.php?page=ion-admin-page");
$I->fillField("openai-api-key", "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");
$I->click("Save Changes");


$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[1])]);
$I->loginAsAdmin();
$I->see("WordPress");
try {
    $I->click(".woocommerce-message-close");
} catch (Exception $e) {
    return true;
}
$I->clickWithLeftButton(".fs-close");
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-ion-chat");
