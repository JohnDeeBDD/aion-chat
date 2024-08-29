<?php

/**
 * This test performs a basic call and response under different modes.
 */

$I = new AcceptanceTester($scenario);

// Get IPs for Mothership and Remote Node
$siteUrls = $I->getSiteUrls();
$mothershipIP = $siteUrls[0];
$remoteNodeIP = $siteUrls[1];

$I->reconfigureThisVariable(["url" => ('http://' . $remoteNodeIP)]);
$I->loginAsAdmin();
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-aion-chat");
