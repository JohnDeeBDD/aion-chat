#!/bin/bash

bin/codecept clean
bin/codecept build
./syncDev.sh
bin/codecept run acceptance Dialectic.feature -vv --html
#bin/codecept run acceptance IntelligentResponseCept.php -vv --html