<?php

/*
Effects of this script:
two servers will be spun up
their IPs will be stored in the file servers.json
*/

$dev1instance = "i-0db86a02d6cdfcec5";
$dev2instance = "i-081b346b183c0e4f3";

$mothershipPHPStormID = "60775156-80f2-488e-9a8d-392fdca99047";
$remoteNodePHPStormID = "2afbdedc-e9eb-4810-9a37-f2d09fc9919b";

$command = "aws ec2 start-instances --instance-ids $dev1instance --profile produser --region us-east-2";
echo ($command . PHP_EOL); shell_exec($command);

$command = "aws ec2 start-instances --instance-ids $dev2instance --profile produser --region us-east-2";
echo ($command . PHP_EOL); shell_exec($command);

sleep(120);

$command = "aws ec2 describe-instances --instance-ids $dev1instance --profile produser --region us-east-2";
echo ($command . PHP_EOL);$IP_RequestResponse = shell_exec($command);

$dev1IP = (((((json_decode($IP_RequestResponse))->Reservations)[0])->Instances)[0])->PublicIpAddress;
echo("Dev1 instance IP is $dev1IP" . PHP_EOL);

$command = "aws ec2 describe-instances --instance-ids $dev2instance --profile produser --region us-east-2";
echo ($command . PHP_EOL);

$IP_RequestResponse = shell_exec($command);
$dev2IP = (((((json_decode($IP_RequestResponse))->Reservations)[0])->Instances)[0])->PublicIpAddress;
echo("Dev2 instance IP is $dev2IP" . PHP_EOL);

$SSH_Commands = [
    //Mothership:
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . " /var/www/html/wp-content/plugins/WPbdd/startup.sh",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . " sudo chmod 777 -R /var/www",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . " wp config create --path=/var/www/html --dbname=wordpress --dbuser=wordpressuser --dbpass=password --force",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp core install --path=/var/www/html --url="http://' . $dev1IP . '" --title=Mothership --admin_name="Codeception" --admin_password="password" --admin_email="codeception@email.com" --skip-email',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp config set FS_METHOD direct --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . " wp rewrite structure '/%postname%/' --path=/var/www/html",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp option update uploads_use_yearmonth_folders 0 --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate aion-chat/aion-chat --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate aion-mother/AionChatMothership --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate classic-editor --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate email-log --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-mail-logging --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-test-email --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate user-switching --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-crontrol --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate disable-administration-email-verification-prompt --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate woocommerce --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate disable-welcome-messages-and-tips --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate buddypress --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate bp-better-messages --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate better-error-messages --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate chicken-chat --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate chicken-chat-mothership --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp user create Subscriberman subscriberman@email.com --role=subscriber --user_pass=password --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp config set WP_DEBUG true --path=/var/www/html',

    //Remote Node:
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " /var/www/html/wp-content/plugins/WPbdd/startup.sh",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " sudo chmod 777 -R /var/www",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " wp config create --path=/var/www/html --dbname=wordpress --dbuser=wordpressuser --dbpass=password --force",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp core install --path=/var/www/html --url="http://' . $dev2IP . '" --title=RemoteNode --admin_name="Codeception" --admin_password="password" --admin_email="codeception@email.com" --skip-email',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp config set FS_METHOD direct --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " wp rewrite structure '/%postname%/' --path=/var/www/html",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp option update uploads_use_yearmonth_folders 0 --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate aion-chat/aion-chat --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate classic-editor --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate email-log --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate user-switching --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-test-email --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-crontrol --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate disable-administration-email-verification-prompt --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate disable-welcome-messages-and-tips --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp user create Subscriberman subscriberman@email.com --role=subscriber --user_pass=password --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp user create AltAdmin altadmin@email.com --role=administrator --user_pass=password --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp config set WP_DEBUG true --path=/var/www/html',"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate better-error-messages --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-mail-logging --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-test-email --path=/var/www/html',
   // "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate user-switching --path=/var/www/html',
    ];

//execute the above commands, one by one.:
foreach($SSH_Commands as $command){
    echo ($command . PHP_EOL);
    shell_exec($command);
}

//Store the IP addresses in the file servers.json
$servers = [$dev1IP, $dev2IP];
$fp = fopen('/var/www/html/wp-content/plugins/aion-chat/servers.json', 'w');
fwrite($fp, json_encode($servers));
fclose($fp);

echo("Copying servers.json to remotes:" . PHP_EOL);
$command = "scp -i /home/johndee/ozempic.pem servers.json ubuntu@$dev1IP:/var/www/html/wp-content/plugins/aion-chat/servers.json";
echo ($command . PHP_EOL);shell_exec($command);
$command = "scp -i /home/johndee/ozempic.pem servers.json ubuntu@$dev2IP:/var/www/html/wp-content/plugins/aion-chat/servers.json";
echo ($command . PHP_EOL);shell_exec($command);

//Update the PHP storm files on the remotes, in case we want to push remote versions to git
//updateXMLIPField(".idea/sshConfigs.xml", $mothershipPHPStormID, $dev1IP);
//updateXMLIPField(".idea/sshConfigs.xml", $remoteNodePHPStormID, $dev2IP);

//Creating WooCommerce product and order
//$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . " php /var/www/html/wp-content/plugins/aion-chat/startupWooCommerce.php";
//echo ($command . PHP_EOL);shell_exec($command);

//$orderID = getOrderIDfromMothership($dev1IP);


//Update Constants.class.php
//$constantsFile = "/var/www/html/wp-content/plugins/aion-chat/src/EmailTunnel/Constants.class.php";
//$blurb = file_get_contents($constantsFile);
//$replaceWith = "http://$dev1IP";
//$blurb = replaceTextInBetweenSingleQuotes($blurb, $replaceWith);
//$blurb = changePropertyViaText($blurb, "CompleatedWooOrder", $orderID);
//f//ile_put_contents($constantsFile, $blurb);
//$command = "scp -i /home/johndee/ozempic.pem $constantsFile ubuntu@$dev1IP:$constantsFile";
//echo ($command . PHP_EOL);shell_exec($command);
//$command = "scp -i /home/johndee/ozempic.pem $constantsFile ubuntu@$dev2IP:$constantsFile";
//echo ($command . PHP_EOL);shell_exec($command);



function replaceTextInBetweenSingleQuotes($blurb, $replaceWith) {
    return preg_replace("/'(.*?)'/", "'$replaceWith'", $blurb);
}


/*
Starting Local Selenium
cd /var/www/html/wp-content/plugins/WPbdd
nohup xvfb-run java -Dwebdriver.chrome.driver=/var/www/html/wp-content/plugins/WPbdd/chromedriver -jar selenium.jar &>/dev/null &
*/
function updateXMLIPField($XML_file, $identifier, $hostIPaddress) {
  // Load the XML file
  $xml = simplexml_load_file($XML_file);

  // Loop through each sshConfig element
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

function getOrderIDfromMothership($mothershipIP){
    $command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $mothershipIP . " wp post list --post_type=shop_order --format=json --path=/var/www/html";
    $response = shell_exec($command);
    $response = json_decode($response)[0];
    echo(PHP_EOL . "the order ID is: " . ($response->ID) . PHP_EOL);
    return $response->ID;
}

function changePropertyViaText($file, $property, $newValue){

    //$file = file_get_contents($fileName);
    $p1 = strpos($file, $property, 0);
    $p2 = strpos($file, ";", ($p1 + strlen($property))  );
    return (substr($file, 0, $p1 + strlen($property) + 3)) . $newValue . substr($file, $p2);
}


//ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@18.224.25.197 wp user subscriberman subscriberman@email.com --role=subscriber --user_pass=password    --path=/var/www/html
