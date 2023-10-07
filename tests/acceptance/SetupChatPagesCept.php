<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Install Ion Chat');
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();

$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);
setup_chat($I);
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[1])]);
setup_chat($I);

function setup_chat($I){
    $I->loginAsAdmin();
    $I->amOnPage("/wp-admin/post-new.php?post_type=bpbm-chat");
    $I->fillField("post_title", "Main Chat");

    $I->click("//a[text()='Select All'][1]");

    //$I->scrollTo("#editor_2");
    $I->click("#editor_2");

    //$I->scrollTo("#author_2");
    $I->click("#author_2");

    //$I->scrollTo("#contributor_2");
    $I->click("#contributor_2");

    //$I->scrollTo("#subscriber_2");
    $I->click("#subscriber_2");

    ///$I->scrollTo("#customer_2");
    $I->click("#customer_2");

    //$I->scrollTo("#shop_manager_2");
    $I->click("#shop_manager_2");

    //$I->scrollTo("#shop_manager_2");
    $I->click("#ion_2");

    $I->scrollTo("#ion_3");
    $I->click("#ion_3");

    $I->executeJS('window.scrollTo(0,0);');
    $I->click("#publish");
    sleep(2);
}