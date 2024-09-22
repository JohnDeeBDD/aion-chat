<?php

$command = "wp post delete $(wp post list --post_type='aion-conversation' --title='TestPost' --format=ids --path=/var/www/html) --force --path=/var/www/html";
echo(shell_exec($command));

$command = "wp post delete $(wp post list --post_type='aion-conversation' --title='ConversationA' --format=ids --path=/var/www/html) --force --path=/var/www/html";
echo(shell_exec($command));

$command = "wp post delete $(wp post list --post_type='aion-conversation' --title='ConversationB' --format=ids --path=/var/www/html) --force --path=/var/www/html";
echo(shell_exec($command));