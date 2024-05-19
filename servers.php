<?php

$file = file_get_contents("servers.json");
$array = json_decode($file);
echo ("<a href = 'http://" . $array[0] . "'/>Mothership</a><br />");
echo ("<a href = 'http://" . $array[1] . "'/>Remote</a><br />");
