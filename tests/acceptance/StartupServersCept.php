<?php
$I = new AcceptanceTester($scenario);
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);

try {
    $I->loginAsAdmin();
} catch (Exception $e) {
    return true;
}
$I->see("WordPress");
try {
    //$I->click("Dismiss");
} catch (Exception $e) {
    return true;
}
try {
    $I->click(".woocommerce-message-close");
} catch (Exception $e) {
    return true;
}
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-ion-chat-mothership");
$I->see("Plugin activated.");



