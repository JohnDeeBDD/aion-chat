<?php
$I = new AcceptanceTester($scenario);
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);
$I->loginAsAdmin();
$I->see("WordPress");
try {
    $I->click("Dismiss");
} catch (Exception $e) {
    return true;
}
try {
    $I->click(".woocommerce-message-close");
} catch (Exception $e) {
    return true;
}
$I->clickWithLeftButton(".fs-close");
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-ion-chat-mothership");
$I->see("Plugin activated.");
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[1])]);
$I->loginAsAdmin();
$I->see("WordPress");
try {
    $I->click("Dismiss");
} catch (Exception $e) {
    return true;
}
$I->clickWithLeftButton(".fs-close");
$I->click(".woocommerce-message-close");
$I->amOnPage("/wp-admin/plugins.php");

