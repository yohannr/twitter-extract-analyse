<?php

require_once('twitter.class.php');
require_once('config.php');

$twitter = new Twitter(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET);

$arr_friends = array();
$twitter_name = TWITTER_NAME;

$arr_friends = $twitter->friendsList($twitter_name);


// Among friends, which are those who did not tweet for a while (ex : 1 month ago), cf. 'last_tweet'
$arr_removeFriends = array();
$delay = 30;
foreach ($arr_friends as $friend) {
	$duration = time() - $friend['last_tweet'];
	$duration = round($duration/3600/24);

	if ($duration > 30) {
		$arr_remove[] = $friend['screen_name'];
	}
}

echo "\n";
print_r($arr_remove);
echo "\n";