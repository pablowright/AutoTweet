AutoTweet
=========

PHP script to auto-post tweets from a database. Supports multiple users. Runs from cron.

=========
AutoTweet.php (ver 1.03, 12-30-2014). by Paul R. Wright: PRW.  pablo(dot)wright(at)gmail(dot)com
Script to auto-post tweets from a database. Supports multiple users. Run from cron. 
Uses the Twitter API, MySQL and cron to automatically post updates to Twitter.

If you find this script useful, have modifications or suggestions please send me an email or a tweet:
pablo(dot)wright(at)gmail(dot)com or twitter: pablowright

++++++++++++++++++++++++++++++++++++++++++++++++++
Plans for version two include allowing users to login and manage their 
autotweets via twitter app login. If you know how do this ( by storing 
twitter tokens in mySQL) and would like to offer suggestions, please do 
so. It will be greatly appreciated. 
+++++++++++++++++++++++++++++++++++++++++++++++++++

zip file contains:
- readme.txt (this file)
- db-conn.php
- sql.txt
- AutoTweet.php


REQUIREMENTS (all free)
1. A Twitter account that will post your updates.
		The script connects to a Twitter application that is tied to this account.
2. A server running PHP (ideally PHP5).
3. A server running cron (this can be the same server as the PHP server).
		Unix-based servers have cron installed by default. Windows servers may have 
		scheduling software as well.
4. A Twitter application. Go to http://dev.twitter.com/
5. The OAuth.php and twitteroauth.php libraries.
		Updated versions are available at the project's source repository: 
		https://github.com/abraham/twitteroauth/tree/master/twitteroauth

INSTALLATION
1. Use the sql.txt file to create your MySQL database. 
2. Add some tweets/users to the database. 
                You should only need to fill in userID and posts in the 'tweets' table.
		Note that your tweets do not exceed the 140-characters.
		Add at least one Twitter user to your database. See requirement 1 & 4, above. You will need
		the twitter secrets/tokens from your twitter app.
3. Edit db-conn.php with your database credentials.
4. Edit AutoTweet.php to set the path to your twitteroauth.php library and db-conn.php file.
                Also note whether you will run via http or command line.
5. Upload the db-conn.php, AutoTweet.php, OAuth.php, and twitteroauth.php to your webspace.
6. Open a browser and navigate to AutoTweet.php to check for errors. Call script with UID arg: ex. AutoTewwt.php?UID=1
		Go to your Twitter user page to check that the post appears in your timeline.
7. If the script is working, create a cron job that uses cURL, wget (or command-line "/usr/bin/php")
 		to run AutoTweetr.php on a regular schedule.
		

NOTES
1. AutoTweet.php logs activity to a file located in its home directory. If your tweets are not appearing, check AutoTweet.log.
2. For security, you might consider protecting the AutoTweet directory via .htaccess, or move the program out of your webspace and run it from the command line, e.g. "/usr/bin/php -f /path/to/AutoTweet.php 1"

