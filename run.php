<?php

require_once('twitter.class.php');
require_once('config.php');

$twitter = new Twitter(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

$arr_friends = array();
$tweeter_name = 'twamingetc';

$arr_friends = $twitter->friendsList($tweeter_name);

echo "\n";
print_r($arr_friends[0]);
echo "\n";
