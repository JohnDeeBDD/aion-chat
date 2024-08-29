<?php

/*
Effects of this script:
two servers will be spun up
their IPs will be stored in the file servers.json
plugins will be activated
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
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate aion-mother/aion-mother --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate aion-chat/aion-chat --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate classic-editor --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate email-log --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-mail-logging --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-test-email --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate classic-widgets --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate duplicate-post --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate sql-buddy --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-rest-api-log --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate jsm-show-user-meta --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate jsm-show-comment-meta --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate auto-login/auto-login --path=/var/www/html',

    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate user-switching --path=/var/www/html',
    //"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate wp-crontrol --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate disable-administration-email-verification-prompt --path=/var/www/html',
   // "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate woocommerce --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp plugin activate disable-welcome-messages-and-tips --path=/var/www/html',
   // "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp user create Subscriberman subscriberman@email.com --role=subscriber --user_pass=password --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev1IP . ' wp config set WP_DEBUG true --path=/var/www/html',

    //Remote Node:
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " /var/www/html/wp-content/plugins/WPbdd/startup.sh",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " sudo chmod 777 -R /var/www",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " wp config create --path=/var/www/html --dbname=wordpress --dbuser=wordpressuser --dbpass=password --force",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp core install --path=/var/www/html --url="http://' . $dev2IP . '" --title=RemoteNode --admin_name="Codeception" --admin_password="password" --admin_email="codeception@email.com" --skip-email',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp config set FS_METHOD direct --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . " wp rewrite structure '/%postname%/' --path=/var/www/html",
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp option update uploads_use_yearmonth_folders 0 --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate classic-editor --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate email-log --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate user-switching --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate classic-widgets --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-test-email --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate disable-administration-email-verification-prompt --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate disable-welcome-messages-and-tips --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp config set WP_DEBUG true --path=/var/www/html',"ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate better-error-messages --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-mail-logging --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-test-email --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate user-switching --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate wp-rest-api-log --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate jsm-show-user-meta --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate jsm-show-comment-meta --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate lh-add-media-from-url --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate auto-login/auto-login --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate duplicate-post --path=/var/www/html',
    "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate sql-buddy --path=/var/www/html',
];

//$SSH_Commands = array_reverse($SSH_Commands);
//execute the above commands, one by one.:
foreach($SSH_Commands as $command){
    echo ($command . PHP_EOL);
    shell_exec($command);
}

//$command = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@" . $dev2IP . ' wp plugin activate aion-chat --path=/var/www/html';
//shell_exec($command);

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

syncOpenAIAPIKey($dev1IP, $dev2IP);


$command = "bin/codecept run acceptance startAionChatServersCept.php -vvv --html";
echo ($command . PHP_EOL); shell_exec($command);

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
/**
 * Sync the "openai-api-key" site option from localhost to mothership and remote_node.
 */
function syncOpenAIAPIKey($dev1IP, $dev2IP)
{
    // Path to WordPress installation
    $wpPath = "/var/www/html";

    // Command to get the "openai-api-key" from the localhost WordPress installation
    $getAPIKeyCommand = "wp option get openai-api-key --allow-root --path=$wpPath --format=json";

    // Execute the command on localhost to retrieve the API key
    $openaiAPIKey = shell_exec($getAPIKeyCommand);
    $openaiAPIKey = trim($openaiAPIKey, "\"\n");

    if (!$openaiAPIKey) {
        echo "Error: Could not retrieve 'openai-api-key' from localhost." . PHP_EOL;
        return;
    }

    echo "Retrieved 'openai-api-key' from localhost: $openaiAPIKey" . PHP_EOL;

    // Prepare SSH command to set the option on the mothership
    $setAPIKeyMothership = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@$dev1IP 'wp option update openai-api-key $openaiAPIKey --allow-root --path=$wpPath'";

    // Execute the command on the mothership
    echo ($setAPIKeyMothership . PHP_EOL);
    shell_exec($setAPIKeyMothership);

    // Prepare SSH command to set the option on the remote_node
    $setAPIKeyRemoteNode = "ssh -o StrictHostKeyChecking=no -i /home/johndee/ozempic.pem ubuntu@$dev2IP 'wp option update openai-api-key $openaiAPIKey --allow-root --path=$wpPath'";

    // Execute the command on the remote_node
    echo ($setAPIKeyRemoteNode . PHP_EOL);
    shell_exec($setAPIKeyRemoteNode);

    echo "'openai-api-key' has been synced to both mothership and remote_node." . PHP_EOL;
}

// Call the function after SSH setup and commands execution
