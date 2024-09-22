<?php

/**
 * This test looks at plugin installation
 */

$I = new AcceptanceTester($scenario);

// Get IPs for Mothership and Remote Node
$siteUrls = $I->getSiteUrls();
$mothershipIP = $siteUrls[0];
$remoteNodeIP = $siteUrls[1];

//Delete RAPs on mothership

function deleteRAPsonSite($IP_address, $userName){



}



function getUserIDfromMothership($mothershipIP, $userName) {
    // Update the command to fetch user information instead of shop orders
    $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $mothershipIP . " wp user list --format=json --path=/var/www/html";

    // Execute the command and decode the JSON response
    $response = shell_exec($command);
    $users = json_decode($response);

    // Find the user with the username "Aion"
    foreach ($users as $user) {
        if ($user->user_login === $userName) {
            echo(PHP_EOL . "The user ID for $userName is: " . $user->ID . PHP_EOL);
            return $user->ID;
        }
    }

    // If no user with username "Aion" is found, return null or an appropriate value
    echo(PHP_EOL . "User with username $userName not found." . PHP_EOL);
    return null;
}
