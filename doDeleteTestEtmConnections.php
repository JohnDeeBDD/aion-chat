<?php

$command = "wp post delete $(wp post list --post_type='aion-conversation' --format=ids --title='TestPost' --path=/var/www/html) --force --path=/var/www/html";

echo(shell_exec($command));