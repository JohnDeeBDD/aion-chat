<?php

/**
 * This test performs a basic call and response under different modes.
 */

$I = new AcceptanceTester($scenario);

// Get IPs for Mothership and Remote Node
$siteUrls = $I->getSiteUrls();
$mothershipIP = $siteUrls[0];
$remoteNodeIP = $siteUrls[1];

// Delete old posts on all servers
deleteOldPosts($mothershipIP, $remoteNodeIP);

// Run tests in different modes
//localhost_mode_test($I);
//mothership_mode_test($I, $mothershipIP);
remote_mode_test($I, $mothershipIP, $remoteNodeIP);

/**
 * Function to delete old posts from servers
 */
function deleteOldPosts($mothershipIP, $remoteNodeIP) {
    $privateKey = '/home/johndee/ozempic.pem';

    // Delete on mothership
    executeRemoteCommand($mothershipIP, "php /var/www/html/wp-content/plugins/aion-chat/doDeleteAllEtmConnections.php", $privateKey);

    // Delete on remote node
    executeRemoteCommand($remoteNodeIP, "php /var/www/html/wp-content/plugins/aion-chat/doDeleteTestEtmConnections.php", $privateKey);

    // Delete locally
    echo(shell_exec("php /var/www/html/wp-content/plugins/aion-chat/doDeleteTestEtmConnections.php"));
}

/**
 * Helper function to execute remote commands via SSH
 */
function executeRemoteCommand($serverIP, $command, $privateKey) {
    $sshCommand = "ssh -o StrictHostKeyChecking=no -i $privateKey ubuntu@$serverIP $command";
    echo(shell_exec($sshCommand));
}

/**
 * Test function for remote mode
 */
function remote_mode_test($I, $mothershipIP, $remoteNodeIP) {
    $remoteNodePostID = setupRemoteNodeTest($I);

    // The first call and response
    $I->makeAComment("Who was the President of the United States in 2003?");
    $I->shouldSeeAnIntelligentResponse("Bush");

    // The second call and response referencing the first one
    $I->makeAComment("Who was the next President after that one?");
    $I->shouldSeeAnIntelligentResponse("Obama");

    $I->makeAComment("What was that President's wife's first name?");
    $I->shouldSeeAnIntelligentResponse("Michelle");

    $I->makeAComment("In the first question I asked you, what year did I ask about?");
    $I->shouldSeeAnIntelligentResponse("2003");



    // Optionally cleanup after the test
    cleanupTest($remoteNodeIP, $mothershipIP, $remoteNodePostID);
}

/**
 * Setup test on the remote node
 */
function setupRemoteNodeTest($I) {
    $remoteNodePostID = $I->setupTestPostOnRemoteNode();
    return $I->extractPostNumeral($remoteNodePostID);
}

/**
 * Cleanup test posts
 */
function cleanupTest($remoteNodeIP, $mothershipIP, $postID) {
    $cleanup = false;

    if ($cleanup) {
        $privateKey = '/home/johndee/ozempic.pem';

        // Cleanup remote node post
        executeRemoteCommand($remoteNodeIP, "wp post delete $postID --force --path=/var/www/html/", $privateKey);

        // Get conversation ID and delete it
        $conversationID = shell_exec("ssh -o StrictHostKeyChecking=no -i $privateKey ubuntu@$mothershipIP wp post list --post_type='aion-conversation' --format=ids --path=/var/www/html/");
        executeRemoteCommand($mothershipIP, "wp post delete $conversationID --force --path=/var/www/html/", $privateKey);
    }
}

/**
 * Test function for localhost mode
 */
function localhost_mode_test($I) {
    // Setup a test post on localhost
    $localhostPostID = $I->setupTestPostOnLocalhost();

    // The first call and response
    $I->wantTo("Test an intelligence response on localhost");
    $I->makeAComment("What is the capital city of France?");
    $I->shouldSeeAnIntelligentResponse("Paris");

    // The second call and response referencing the first one
    $I->makeAComment("What is the tallest structure in that city?");
    $I->shouldSeeAnIntelligentResponse("Eiffel Tower");

    // Cleanup localhost test
    echo(shell_exec("wp post delete $localhostPostID --force"));
}

/**
 * Test function for mothership mode
 */
function mothership_mode_test($I, $mothershipIP) {
    // Setup a test post on the mothership
    $mothershipPostID = setupMothershipTest($I);

    // The first call and response
    $I->makeAComment("What is the capital city of the United States of America?");
    $I->shouldSeeAnIntelligentResponse("Washington");

    // The second call and response referencing the first one
    $I->makeAComment("What is the first name of the person that city is named after?");
    $I->shouldSeeAnIntelligentResponse("George");

    // Optionally cleanup after the test
    cleanupMothershipTest($mothershipIP, $mothershipPostID);
}

/**
 * Setup test on the mothership
 */
function setupMothershipTest($I) {
    $mothershipPostID = $I->setupTestPostOnMothership();
    return $I->extractPostNumeral($mothershipPostID);
}

/**
 * Cleanup mothership test posts
 */
function cleanupMothershipTest($mothershipIP, $postID) {
    $cleanup = false;

    if ($cleanup) {
        $privateKey = '/home/johndee/ozempic.pem';
        executeRemoteCommand($mothershipIP, "wp post delete $postID --force --path=/var/www/html/", $privateKey);
    }
}
