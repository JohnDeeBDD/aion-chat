<?php

//This script zips the production version

$version = readline('Version to create: ');


shell_exec("sudo rm -fr /var/www/html/wp-content/plugins/ion-chat/ion-chat");
shell_exec("sudo mkdir /var/www/html/wp-content/plugins/ion-chat/ion-chat");

copy("/var/www/html/wp-content/plugins/ion-chat/ion-chat.php", "/var/www/html/wp-content/plugins/ion-chat/ion-chat/ion-chat.php");
//shell_exec("cp -r /var/www/html/wp-content/plugins/email-tunnel/src /var/www/html/wp-content/plugins/email-tunnel/email-tunnel/src");

shell_exec("sudo rsync -r --exclude src/IonChat src ion-chat");

shell_exec("sudo zip -r ion-chat-$version.zip ion-chat");
shell_exec("sudo rm ion-chat.zip");
shell_exec("sudo cp ion-chat-$version.zip ion-chat.zip");