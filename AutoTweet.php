<?php

// AutoTweet.php (ver 1.02, 12-30-2014). Script to auto-post tweets from a database. Supports multiple users. Run from cron. 
// by Paul R. Wright PRW. pablo.wright@gmail.com. Please email or tweet me if you find this useful or have 
// suggestions/modifications.
// Requires MySQL, php and the twitteroauth library by Abraham Williams. https://github.com/abraham/twitteroauth and of course,
// Twitter app.

// Use twitteroauth library:
require_once('EDIT PATH/TO/twitteroauth.php');

// Set error reporting level. "E_ALL" is good for debugging.
error_reporting(E_ALL);

// Date info.:
date_default_timezone_set('America/New_York');
$tweetContentDate = date('m/d/Y h:i:s a', time());


//Get userID from script call:
// If called from http:
// $userID = $_GET["UID"];
// Or better yet, get UID and check to see if it is an integer:
$id = ( isset( $_GET['UID'] ) && is_numeric( $_GET['UID'] ) ) ? intval( $_GET['UID'] ) : 0;
if ( $id != 0 ){
    // id is an int != 0
$userID = $id;
}
else {
error_log('User does not exist '.$id. "\n", 3, "AutoTweet.log"); 
  exit("No user by this id. Let's go listen to Science Friday instead.");
}

// If called from command line:
// $userID = $argv[1];
// we'll skip the interger check.

// Connect to DB; Execute Query:
include 'db-conn.php';
$link = mysqli_connect("$server","$user","$pass","$database") or die("Error " . mysqli_error($link));
$query = "SELECT id, posts, posted FROM tweets where userID='$userID' and posted = 0 ORDER by RAND() LIMIT 0,1" or die("Error in the consult.." . mysqli_error($link));
$result = mysqli_query($link, $query);

//Check results for enpty set:
if(mysqli_num_rows($result)==0){
  $emptySetError = "{$userID}  {$tweetContentDate}";
  error_log('No tweets found for user '.$emptySetError. "\n", 3, "AutoTweet.log"); 
  exit("No tweets for this user.");
}
else {  

while($row = mysqli_fetch_array($result)) {

$tweetStr = $row["posts"];
$ID = $row["id"];

mysqli_query($link, "UPDATE tweets SET posted = 1 WHERE ID = '$ID'");

}
}
// Free result set:
mysqli_free_result($result);

// Select User to tweet as:
$link = mysqli_connect("$server","$user","$pass","$database") or die("Error " . mysqli_error($link));
$query = "SELECT ConKey, ConSec, AccTok, AccTokSec FROM users WHERE userID='$userID'" or die("Error in the consult.." . mysqli_error($link));
$result = mysqli_query($link, $query);

while ($row = mysqli_fetch_assoc($result)){
$connection = new TwitterOAuth("$row[ConKey]", "$row[ConSec]", "$row[AccTok]", "$row[AccTokSec]");

}

// Post Tweet:
$connection->post('statuses/update', array('status' => $tweetStr));

// Free result set
mysqli_free_result($result);

// Log script results:
$statusCode = $connection->http_code;
$statusSuccess = "{$tweetContentDate}  TwitterUser {$userID} tweeted {$tweetStr}";
$statusError = "{$statusCode}  {$tweetContentDate} TwitterUser {$userID}";

if ($connection->http_code == 200) {
 		error_log('Success ' .$statusSuccess."\n", 3, "AutoTweet.log");
		} else {
		error_log('Error posting to twitter: '.$statusError."\n", 3, "AutoTweet.log");
	}
		
echo "$tweetStr";		

?>
