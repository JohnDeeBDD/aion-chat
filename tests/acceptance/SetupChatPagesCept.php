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

    $I->clickWithLeftButton("//a[text()='Select All'][1]");

   // $I->executeJS('window.scrollTo(0,0);');
   // $I->scrollTo("#editor_2");
    $I->clickWithLeftButton("#editor_2");

    //$I->executeJS('window.scrollTo(0,0);');
    //$I->scrollTo("#author_2");
    $I->clickWithLeftButton("#author_2");

    //$I->executeJS('window.scrollTo(0,0);');
//    $I->scrollTo("#contributor_2");
    $I->clickWithLeftButton("#contributor_2");

  //  $I->executeJS('window.scrollTo(0,0);');
    //$I->scrollTo("#subscriber_2");
    $I->clickWithLeftButton("#subscriber_2");

    //$I->executeJS('window.scrollTo(0,0);');
    //$I->scrollTo("#customer_2");
    $I->clickWithLeftButton("#customer_2");

    //$I->executeJS('window.scrollTo(0,0);');
    //$I->scrollTo("#shop_manager_2");
    $I->clickWithLeftButton("#shop_manager_2");


    //$I->executeJS('window.scrollTo(0,0);');
    //$I->scrollTo("#shop_manager_2");
    $I->clickWithLeftButton("#ion_2");

    //$I->executeJS('window.scrollTo(0,0);');
    //$I->scrollTo("#ion_3");
    $I->clickWithLeftButton("#ion_3");

    //$I->executeJS('window.scrollTo(0,0);');
    $I->clickWithLeftButton("#publish");
    $I->see("Post published.");
}