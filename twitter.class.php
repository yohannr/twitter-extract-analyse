<?php

/*
 * More information on Twitter API : https://dev.twitter.com/docs/api/1.1
*/

require_once('twitteroauth/twitteroauth.php');

class Twitter
{

	private $consumer_key;
	private $consumer_secret;
	private $oauth_token;
	private $oauth_token_secret;
	
	public function __construct($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret)
	{
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		$this->oauth_token = $oauth_token;
		$this->oauth_token_secret = $oauth_token_secret;
	}


	/*
	 * 
	*/
	public function query($query)
	{
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);
		$content = $connection->get($query);

		return $content;
	}


	/*
	 * Return the last (10) tweets of an account
	 * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
	*/
	public function statusesUserTimeline($account, $nbOfTweet = 10)
	{
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);
			
		$screen_name = 'screen_name='.$account;
		$count = 'count='.$nbOfTweet;
		$query = 'https://api.twitter.com/1.1/statuses/user_timeline.json?trim_user=true&exclude_replies=true&'.$screen_name.'&'.$count;
		$content = $connection->get($query);

		$arr_tweets = array();

		$i = 0;
		foreach ($content as $tweet) {
			$arr_tweets[$i]['created_at'] = $this->twitterDateToTimestamp($tweet->created_at);
			$arr_tweets[$i]['id_str'] = $tweet->id_str;
			$arr_tweets[$i]['text'] = $tweet->text;
			$arr_tweets[$i]['retweet_count'] = $tweet->retweet_count;
			$arr_tweets[$i]['favorite_count'] = $tweet->favorite_count;
			$arr_tweets[$i]['retweeted'] = $tweet->retweeted;
			$arr_tweets[$i]['possibly_sensitive'] = $tweet->possibly_sensitive;
			$arr_tweets[$i]['lang'] = $tweet->lang;
			$arr_tweets[$i]['geo'] = $tweet->geo;
			$arr_tweets[$i]['place'] = $tweet->place;

			++$i;
		}

		return $arr_tweets;
	}


	/*
	 * Get information about an account
	 * https://dev.twitter.com/docs/api/1.1/get/users/lookup
	*/
	public function usersLookup($id)
	{
		try {
			$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);
		} catch (Exception $e) {
			echo 'Error connection usersLookup : '.$e->getMessage();
			exit;
		}

		$arr_friend = array();

		try {
			$query = 'https://api.twitter.com/1.1/users/lookup.json?user_id=';
			$fullquery = $query.$id.'&include_entities=false';
			$content = $connection->get($fullquery);

			$arr_friend['id'] = (int)$content[0]->id_str;
			$arr_friend['name'] = $content[0]->name;
			$arr_friend['screen_name'] = $content[0]->screen_name;
			$arr_friend['location'] = $content[0]->location;
			$arr_friend['url'] = $content[0]->url;
			$arr_friend['description'] = $content[0]->description;
			$arr_friend['followers_count'] = $content[0]->followers_count;
			$arr_friend['friends_count'] = $content[0]->friends_count;
			$arr_friend['listed_count'] = $content[0]->listed_count;
			$arr_friend['favourites_count'] = $content[0]->favourites_count;
			$arr_friend['lang'] = $content[0]->lang;
			$arr_friend['profile_image_url'] = $content[0]->profile_image_url;
			$arr_friend['following'] = $content[0]->following;
			$arr_friend['last_tweet'] = $this->twitterDateToTimestamp($content[0]->status->created_at);

		unset($connection);
		} catch (Exception $e) {
			echo 'Error usersLookup : '.$e->getMessage();
			exit;
		}

		

		return $arr_friend;
	}


	/*
	 * Return list of Ids' friends
	 * https://dev.twitter.com/docs/api/1.1/get/friends/ids
	*/
	private function friendsIds($tweeter_name)
	{
		try {
			$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);
		} catch (Exception $e) {
			echo 'Error connection friendsIds : '.$e->getMessage();
			exit;
		}

		$arr_ids = array();
		try {
			$query = 'https://api.twitter.com/1.1/friends/ids.json?screen_name='.$tweeter_name.'&cursor=-1';
			$content = $connection->get($query);
			if(!empty($content->ids)) { 
				foreach($content->ids as $id) {
					$arr_ids[] = $id;
				}
			}
			unset($connection);
		} catch (Exception $e) {
			echo 'Error friendsIds : '.$e->getMessage();
			exit;
		}

		return $arr_ids;
	}


	/*
	 * Get friends list
	*/
	public function friendsList($tweeter_name)
	{

		$arr_ids = $this->friendsIds($tweeter_name);
		$i = 0;
		foreach ($arr_ids as $id) {
			++$i;
			echo 'Friend '.$i.' : Id '.$id;
			echo "\n";
			$arr_friends[] = $this->usersLookup($id);

			if (($i % 150) == 0) {
				echo 'Pause 15min';
				echo "\n";
				usleep(900000000);
			}
		}

		return $arr_friends;

	}


	/*
	 * Get followers list
	*/
	public function followersList($tweeter_name)
	{

		$arr_ids = $this->followersIds($tweeter_name);
		$i = 0;
		foreach ($arr_ids as $id) {
			++$i;
			echo 'Followers '.$i.' : Id '.$id;
			echo "\n";
			$arr_followers[] = $this->usersLookup($id);

			if (($i % 150) == 0) {
				echo 'Pause 15min';
				echo "\n";
				usleep(900000000);
			}
		}

		return $arr_followers;

	}


	/*
	 * Return list of Ids' followers
	 * https://dev.twitter.com/docs/api/1.1/get/followers/ids
	*/
	private function followersIds($account)
	{
		$connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->oauth_token_secret);

		$arr_ids = array();

		$query = 'https://api.twitter.com/1.1/followers/ids.json?screen_name='.$account.'&cursor=-1';
		$content = $connection->get($query);

		if(!empty($content->ids)) { 
			foreach($content->ids as $id) {
				$arr_ids[] = $id;
			}
		}

		return $arr_ids;
	}



	/*
	 * Convert twitter date (ex : Sat Aug 23 03:57:16 +0000 2014) into timestamp
	*/
	private function twitterDateToTimestamp($date)
	{
		$arr_date = date_parse($date);
		return mktime($arr_date['hour'], $arr_date['minute'], $arr_date['second'], $arr_date['month'], $arr_date['day'], $arr_date['year']);
	}

}