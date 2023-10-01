<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Install Ion Chat');
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);

try {
    $I->loginAsAdmin();
} catch (Exception $e) {
}


try {
    $I->see("WxxxordPress");
} catch (Exception $e) {
}

try {
    $I->click("Dismiss");
} catch (Exception $e) {
}


try {
    $I->click(".woocommerce-message-close");
} catch (Exception $e) {
}



$I->click('Opt in to make "Better Messages" better!');
$I->click("Skip");
$I->click("Activate");
$I->click('#cb-select-all-1');
$I->click("#bp-admin-component-submit");
$I->amOnPage("/wp-admin/edit.php?post_type=bpbm-chat");
$I->click(".page-title-action");




try {

} catch (Exception $e) {
}


try {

} catch (Exception $e) {
}


try {

} catch (Exception $e) {
}


try {

} catch (Exception $e) {
}


