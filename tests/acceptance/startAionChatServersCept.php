<?php

/*
Effects of this script:
two servers will be spun up
their IPs will be stored in the file servers.json
*/

$AWS_MothershipInstanceID = "i-0e5a0a96d03dc711c";
$AWS_RemoteNodeInstanceID = "i-00021f9572af5111c";

$PHP_Storm_MothershipID = "e084db73-71ee-4f6b-be2a-0d282438da4b";
$PHP_Storm_RemoteNodeID = "7dddac4c-398a-4e26-b322-7c880a02f49d";

$command = "aws ec2 start-instances --instance-ids $AWS_MothershipInstanceID --profile produser --region us-east-2";
echo ($command . PHP_EOL); shell_exec($command);

$command = "aws ec2 start-instances --instance-ids $AWS_RemoteNodeInstanceID --profile produser --region us-east-2";
echo ($command . PHP_EOL); shell_exec($command);

sleep(120);

$command = "aws ec2 describe-instances --instance-ids $AWS_MothershipInstanceID --profile produser --region us-east-2";
echo ($command . PHP_EOL);$IP_RequestResponse = shell_exec($command);

$MothershipIP = (((((json_decode($IP_RequestResponse))->Reservations)[0])->Instances)[0])->PublicIpAddress;
echo("Dev1 instance IP is $MothershipIP" . PHP_EOL);

$command = "aws ec2 describe-instances --instance-ids $AWS_RemoteNodeInstanceID --profile produser --region us-east-2";
echo ($command . PHP_EOL);

$IP_RequestResponse = shell_exec($command);
$RemoteNodeIP = (((((json_decode($IP_RequestResponse))->Reservations)[0])->Instances)[0])->PublicIpAddress;
echo("Dev2 instance IP is $RemoteNodeIP" . PHP_EOL);

$SSH_Commands = [
    //Mothership:
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . " /var/www/html/wp-content/plugins/WPbdd/startup.sh",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . " sudo chmod 777 -R /var/www",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . " wp config create --path=/var/www/html --dbname=wordpress --dbuser=wordpressuser --dbpass=password --force",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp core install --path=/var/www/html --url="http://' . $MothershipIP . '" --title=Mothership --admin_name="Codeception" --admin_password="password" --admin_email="codeception@email.com" --skip-email',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp config set FS_METHOD direct --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp config set WP_DEBUG true --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp config set WP_DEBUG_LOG true --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp option update uploads_use_yearmonth_folders 0 --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate classic-editor --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate wp-mail-catcher --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate user-switching --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate disable-administration-email-verification-prompt --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate woocommerce --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate disable-welcome-messages-and-tips --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate better-error-messages --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $dev1IP . ' wp plugin activate aion-chat --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp plugin activate wp-data-access --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . " wp rewrite structure '/%postname%/' --path=/var/www/html",
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp user create Subscriberman subscriberman@email.com --role=subscriber --user_pass=password --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $MothershipIP . ' wp user create Ion ion@ioncity.ai --role=administrator --user_pass=password --path=/var/www/html',

    //Remote Node:
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . " /var/www/html/wp-content/plugins/WPbdd/startup.sh",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . " sudo chmod 777 -R /var/www",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . " wp config create --path=/var/www/html --dbname=wordpress --dbuser=wordpressuser --dbpass=password --force",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp core install --path=/var/www/html --url="http://' . $RemoteNodeIP . '" --title=RemoteNode --admin_name="Codeception" --admin_password="password" --admin_email="codeception@email.com" --skip-email',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp config set FS_METHOD direct --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp config set WP_DEBUG true --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp config set WP_DEBUG_LOG true --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp option update uploads_use_yearmonth_folders 0 --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate classic-editor --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate wp-mail-catcher --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate user-switching --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate disable-administration-email-verification-prompt --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate woocommerce --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate disable-welcome-messages-and-tips --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate better-error-messages --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $dev2IP . ' wp plugin activate aion-chat --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp plugin activate wp-data-access --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . " wp rewrite structure '/%postname%/' --path=/var/www/html",
   // "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp user create Subscriberman subscriberman@email.com --role=subscriber --user_pass=password --path=/var/www/html',
   // "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp user create RemoteSubsriber remotesub@email.com --role=subscriber --user_pass=password --path=/var/www/html',
   // "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $RemoteNodeIP . ' wp user create Ion ion@ioncity.ai --role=administrator --user_pass=password --path=/var/www/html',
    ];

//execute the above commands, one by one.:
foreach($SSH_Commands as $command){
    echo ($command . PHP_EOL);
    shell_exec($command);
}

//Store the IP addresses in the file servers.json
$servers = [$MothershipIP, $RemoteNodeIP];
$fp = fopen('/var/www/html/wp-content/plugins/aion-chat/servers.json', 'w');
fwrite($fp, json_encode($servers));
fclose($fp);


echo("Copying servers.json to remotes:" . PHP_EOL);
$command = "scp -i /home/johndee/sportsman.pem servers.json ubuntu@$MothershipIP:/var/www/html/wp-content/plugins/aion-chat/servers.json";
echo ($command . PHP_EOL);shell_exec($command);
$command = "scp -i /home/johndee/sportsman.pem servers.json ubuntu@$RemoteNodeIP:/var/www/html/wp-content/plugins/aion-chat/servers.json";
echo ($command . PHP_EOL);shell_exec($command);



//Update the PHP storm files on the remotes, in case we want to push remote versions to git
updateXMLIPField(".idea/sshConfigs.xml", $PHP_Storm_MothershipID, $MothershipIP);
updateXMLIPField(".idea/sshConfigs.xml", $PHP_Storm_RemoteNodeID, $RemoteNodeIP);

//Setting up chat plugins:
$command = "cd /var/www/html/wp-content/plugins/aion-chat";
echo ($command . PHP_EOL); shell_exec($command);

$I = new AcceptanceTester($scenario);
global $testSiteURLs;
$testSiteURLs = $I->getSiteUrls();
$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[0])]);
$I->loginAsAdmin();
$I->see("WordPress");
//$I->click(".fs-close");
$I->click(".welcome-panel-close");
$I->click(".woocommerce-message-close");
$I->clickWithLeftButton(".fs-close");
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-aion-chat");
$I->see("Plugin activated.");
$I->click("#activate-aion-chat-mothership");

$I->see("Plugin activated.");


$I->reconfigureThisVariable(["url" => ('http://' . $testSiteURLs[1])]);
$I->loginAsAdmin();
$I->see("WordPress");
try {
    $I->click(".woocommerce-message-close");
} catch (Exception $e) {
    return true;
}
$I->clickWithLeftButton(".fs-close");
$I->amOnPage("/wp-admin/plugins.php");
$I->click("#activate-aion-chat");




////Creating WooCommerce product and order
//$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/sportsman.pem ubuntu@" . $dev1IP . " php /var/www/html/wp-content/plugins/aion-chat/startupWooCommerce.php";
//echo ($command . PHP_EOL);shell_exec($command);

//$orderID = getOrderIDfromMothership($dev1IP);

/*
//Update Constants.class.php
$constantsFile = "/var/www/html/wp-content/plugins/aion-chat/src/EmailTunnel/Constants.class.php";
$blurb = file_get_contents($constantsFile);
$replaceWith = "http://$dev1IP";
$blurb = replaceTextInBetweenSingleQuotes($blurb, $replaceWith);
$blurb = changePropertyViaText($blurb, "CompleatedWooOrder", $orderID);
file_put_contents($constantsFile, $blurb);
$command = "scp -i /home/johndee/sportsman.pem $constantsFile ubuntu@$dev1IP:$constantsFile";
echo ($command . PHP_EOL);shell_exec($command);
$command = "scp -i /home/johndee/sportsman.pem $constantsFile ubuntu@$dev2IP:$constantsFile";
echo ($command . PHP_EOL);shell_exec($command);
*/
/*
Starting Local Selenium
cd /var/www/html/wp-content/plugins/WPbdd
nohup xvfb-run java -Dwebdriver.chrome.driver=/var/www/html/wp-content/plugins/WPbdd/chromedriver -jar selenium.jar &>/dev/null &
*/

function replaceTextInBetweenSingleQuotes($blurb, $replaceWith) {
    return preg_replace("/'(.*?)'/", "'$replaceWith'", $blurb);
}

function updateXMLIPField($XML_file, $identifier, $hostIPaddress) {
  $xml = simplexml_load_file($XML_file);
  foreach ($xml->component->configs->sshConfig as $sshConfig) {
    // Check if the id attribute matches the identifier parameter
    if ((string)$sshConfig['id'] === $identifier) {
      // Update the host attribute with the new host IP address
      $sshConfig['host'] = $hostIPaddress;
    }
  }
  // Save the updated XML file
  $xml->asXML($XML_file);
  // Return the updated XML file as a string
    return file_get_contents($XML_file);
}