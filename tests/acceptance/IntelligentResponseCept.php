<?php

/* This test performs a basic call and response under different modes. */

$I = new AcceptanceTester($scenario);

//delete old posts
$remoteNodeIP = $I->getSiteUrls();
$mothershipIP = $I->getSiteUrls();
$mothershipIP = $mothershipIP[0];
$remoteNodeIP = $remoteNodeIP[1];

$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $mothershipIP . " php /var/www/html/wp-content/plugins/aion-chat/doDeleteTestEtmConnections.php";
echo(shell_exec($command));
$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $remoteNodeIP . " php /var/www/html/wp-content/plugins/aion-chat/doDeleteTestEtmConnections.php";
echo(shell_exec($command));
$command = "php /var/www/html/wp-content/plugins/aion-chat/doDeleteTestEtmConnections.php";
echo(shell_exec($command));



//localhost_mode_test($I);
//mothership_mode_test($I);
remote_mode_test($I);

function localhost_mode_test($I){
    //LOCALHOST MODE TEST
//This setup function creates a "test post" "aion-converstaion" CPT on localhost:
    $localhostPostID = $I->setupTestPostOnLocalhost();

//The first call and response:
    $I->makeAComment("What is the capital city of France?");
    $I->shouldSeeAnIntelligentResponse("Paris");

//The second call and response references the first one:
    $I->makeAComment("What is the tallest structure in that city?");
    $I->shouldSeeAnIntelligentResponse("Eiffel Tower");

//cleanup localhost test:
    echo(shell_exec("wp post delete $localhostPostID --force"));
}
function mothership_mode_test($I){
    //MOTHERSHIP MODE TEST
//This test does a call and response directly on the mothership

//This setup function creates a "test post" "aion-converstaion" CPT on the mothership:
    $mothershipPostID = $I->setupTestPostOnMothership();
    $mothershipPostID = $I->extractPostNumeral($mothershipPostID);
//The first call and response:
    $I->makeAComment("What is the capital city of the United States of America?");
    $I->shouldSeeAnIntelligentResponse("Washington");

//The second call and response references the first one:
    $I->makeAComment("What is the first name of the person that city is named after?");
    $I->shouldSeeAnIntelligentResponse("George");

//Cleanup mothership mode test:
    $mothershipIP = $I->getSiteUrls();
    $mothershipIP = $mothershipIP[0];
    $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $mothershipIP . " wp post delete $mothershipPostID --force --path=/var/www/html/";
    echo(shell_exec($command));
}
function remote_mode_test($I){


    //REMOTE NODE MODE
    $remoteNodeIP = $I->getSiteUrls();
    $mothershipIP = $I->getSiteUrls();
    $mothershipIP = $mothershipIP[0];
    $remoteNodeIP = $remoteNodeIP[1];
    $remoteNodePostID = $I->setupTestPostOnRemoteNode();
    $remoteNodePostID = $I->extractPostNumeral($remoteNodePostID);
//The first call and response:
    $I->makeAComment("Who was the President of the United States in 2003?");
    $I->shouldSeeAnIntelligentResponse("Bush");

//The second call and response references the first one:
    $I->makeAComment("Who was the next President after that one?");
    $I->shouldSeeAnIntelligentResponse("Obama");


//Cleanup
    $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $remoteNodeIP . " wp post delete $remoteNodePostID --force --path=/var/www/html/";
    echo(shell_exec($command));

    $command =  "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $mothershipIP . " wp post list --post_type='aion-conversation' --format=ids --path=/var/www/html/";
    $conversationID = shell_exec($command);
    echo("convo ID is $conversationID");

    $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $mothershipIP . " wp post delete $conversationID --force --path=/var/www/html/";
    echo(shell_exec($command));


}
