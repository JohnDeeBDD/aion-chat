#!/bin/bash
./syncDevServers.sh
clear
bin/codecept run acceptance IntelligentResponseCept.php -v --html
bin/codecept run unit -v --html
