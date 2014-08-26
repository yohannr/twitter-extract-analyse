# Twitter-extract-analyse

Twitter processing library : 
- extract data from an account (friends, followers...)
- analyze (ex : Among friends, which are those who did not tweet for a while ?)

## External Libraries

- [Twitter OAuth by Abraham Williams](http://github.com/abraham/twitteroauth) (Included)

## TODO

- before running process, look at the rate limit
- extract data into MongoDB regarding next reviews of the dedicated account
- analyse data from MongoDB

## Notes

- because of twitter rate limit, once 150 requests the process is paused.
For this reason, this script is not recommended for account with high number of friends/followers and will be likely execute throught PHP CLI

- Define in a config file all the twitter keys (CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_TOKEN_SECRET) and the account name (TWITTER_NAME)