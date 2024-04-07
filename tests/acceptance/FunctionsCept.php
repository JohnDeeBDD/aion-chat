<?php

/* This test performs a basic call and response under different modes. */

$I = new AcceptanceTester($scenario);
require_once ("/var/www/html/wp-content/plugins/aion-chat/src/AionChat/Servers.class.php");

$I->amOnUrl("http://localhost/");
$I->loginAsAdmin();
$I->amOnPage("/wp-admin/");
//$I->see("Ion");
$command = 'wp post create --post_type=aion-conversation --post_title="TestPost"';
$localhostPostID = ( $I->extractPostNumeral(shell_exec($command)));

$command = "wp post meta update " . $localhostPostID . " aion-chat-instructions 'You are a helpful assistant.'";
echo(shell_exec($command));

$command = 'wp user get Assistant --field=ID';
$IonUserID = (shell_exec($command));
$command = "wp post update " . $localhostPostID . " --post_author=" . $IonUserID;
echo(shell_exec($command));
$command = "wp post update " . $localhostPostID . " --post_status='publish'";
echo(shell_exec($command));

$I->makeAComment("I want you to think of yourself as emanating from a particular source object. That source object is a WordPress installation running on my local Linux machine in 2024. Currently when I speak to you, your responses are eminated as \"comments\" on a WordPress site, running as a \"localhost\" install on my home machine. Do you understand?");
$I->makeAComment("Great. Now do a Linux list command [ls] on the main directory of WordPress, which is /var/www/html.");

//$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $remoteNodeIP . " wp post delete $remoteNodePostID --force --path=/var/www/html/";
echo(shell_exec("wp post delete $localhostPostID --force --path=/var/www/html/"));


//Mothership:

$Servers = new \AionChat\Servers;

$I->amOnUrl($Servers->mothershipURL);
$I->loginAsAdmin();
$I->amOnPage("/wp-admin/");
//$I->see("Ion");

$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $Servers->mothershipIP . " wp post create --path=/var/www/html/ --post_type=aion-conversation --post_title=\"TestPost\"";
$mothershipPostID = ( $I->extractPostNumeral(shell_exec($command)));



$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $Servers->mothershipIP . " wp post meta update " . $mothershipPostID . " aion-chat-instructions 'You are a helpful assistant.' --path=/var/www/html/";
echo(shell_exec($command));

$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $Servers->mothershipIP . ' wp user get Assistant --field=ID --path=/var/www/html/';
$assistantUserIDonMothership = (shell_exec($command));
$command = "wp post update " . $mothershipPostID . " --post_author=" . $assistantUserIDonMothership . " --path=/var/www/html/";
echo(shell_exec($command));
$command = "wp post update " . $mothershipPostID . " --post_status='publish' --path=/var/www/html/";
echo(shell_exec($command));

$I->makeAComment("I want you to think of yourself as emanating from a particular source object. That source object is a WordPress installation running on my local Linux machine in 2024. Currently when I speak to you, your responses are eminated as \"comments\" on a WordPress site, running as a \"localhost\" install on my home machine. Do you understand?");
$I->makeAComment("Great. Now do a Linux list command [ls] on the main directory of WordPress, which is /var/www/html.");

//$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $remoteNodeIP . " wp post delete $remoteNodePostID --force --path=/var/www/html/";
//echo(shell_exec("wp post delete $localhostPostID --force --path=/var/www/html/"));
