<?php

use IonChat\Prompt;

include("/var/www/html/wp-content/plugins/ion-chat/src/IonChat/autoloader.php");
include("/var/www/html/wp-content/plugins/ion-chat/tests/instantiate_mock_prompt.php");

$I = new ApiTester($scenario);

$servers = json_decode(file_get_contents("/var/www/html/wp-content/plugins/ion-chat/servers.json"));

$Prompt = instantiate_dummy();

$data = [
    'prompt' => serialize($Prompt)
];
$I->sendPOST("http://" . $servers[0] .  "/wp-json/ion-chat/v1/ion-prompt", $data);
