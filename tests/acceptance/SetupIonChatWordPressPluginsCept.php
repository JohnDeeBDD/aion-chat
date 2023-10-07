<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Install Ion Chat');
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();

$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);
run($I);
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[1])]);
run($I);

function run($I)
{
    try {
        $I->loginAsAdmin();
    } catch (Exception $e) {
        return true;
    }
    $I->see("WordPress");
    sleep(2);
    try {
        $I->click(".woocommerce-message-close");
    } catch (Exception $e) {
        return true;
    }
    sleep(2);
    $I->click("Activate");
    $I->click('#cb-select-all-1');
    $I->click("#bp-admin-component-submit");
    $I->click('Opt in to make "Better Messages" better!');
    $I->click("Skip");
    $I->click('Opt in to make "WP Data Access" better!');
    $I->click("Skip");
}

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