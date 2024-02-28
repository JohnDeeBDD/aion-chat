<?php

//This script zips the production version

$version = readline('Version to create: ');


shell_exec("sudo rm -fr /var/www/html/wp-content/plugins/aion-chat/aion-chat");
shell_exec("sudo mkdir /var/www/html/wp-content/plugins/aion-chat/aion-chat");

copy("/var/www/html/wp-content/plugins/aion-chat/aion-chat.php", "/var/www/html/wp-content/plugins/aion-chat/aion-chat/aion-chat.php");
copy("/var/www/html/wp-content/plugins/aion-chat/servers.json", "/var/www/html/wp-content/plugins/aion-chat/aion-chat/servers.json");


shell_exec("sudo rsync -r --exclude src/AionChatMothership src aion-chat");

shell_exec("sudo zip -r aion-chat-$version.zip aion-chat");
shell_exec("sudo rm aion-chat.zip");
shell_exec("sudo cp aion-chat-$version.zip aion-chat.zip");