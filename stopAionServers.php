<?php

$dev1instance = "i-0db86a02d6cdfcec5";
$dev2instance = "i-081b346b183c0e4f3";

shell_exec("aws ec2 stop-instances --instance-ids $dev1instance --profile produser --region us-east-2");
sleep(1);
shell_exec("aws ec2 stop-instances --instance-ids $dev2instance --profile produser --region us-east-2");