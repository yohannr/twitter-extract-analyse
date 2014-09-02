<?php

require_once('twitter.class.php');
require_once('config.php');

$twitter = new Twitter(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

// Among friends, which are those who don't follow me, return the first tweets of those people to say if I will unfollow or not them
// Compare $arr_friends with $arr_followers ==> array_intersect($tab1,$tab2)
$arr_remove = array();
$arr_remove = $twitter->getFriendsWhoDontFollow(TWITTER_NAME);


echo "\n";
print_r($arr_remove);
echo "\n";