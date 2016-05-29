<?php

// AutoTweet.php (ver 2.02, 02-22-2015). Script to auto-post tweets from a database. Supports multiple users. Run from cron. 
// by Paul R. Wright PRW. pablo.wright@gmail.com Please email or tweet me if you find this useful or have 
// suggestions/modifications.
// Requires MySQL, php and the twitteroauth library by Abraham Williams. https://github.com/abraham/twitteroauth

// Get rid of "?" in some Web browsers. maybe?:
header('Content-Type: text/html; charset=utf-8');

// Use twitteroauth library:
require_once('../twitteroauth/twitteroauth/twitteroauth.php');

// Set error reporting level. "E_ALL" is good for debugging.
error_reporting(E_ALL);

// Date info.:
date_default_timezone_set('America/New_York');
$tweetContentDate = date('m/d/Y h:i:s a', time());


// Get userID from script call:
// If called from http:
// $userID = $_GET["UID"];
// Or better yet, get UID and check to see if it is an integer:
$id = ( isset( $_GET['UID'] ) && is_numeric( $_GET['UID'] ) ) ? intval( $_GET['UID'] ) : 0;
if ( $id != 0 ){
    // id is an int != 0
$userID = $id;
}
else {
error_log('User does not exist '.$id. "\n", 3, "tweeterErrors.log"); 
  exit("No user by this id. Let's go listen to Science Friday.");
}

// Connect to DB; Execute Query:
include 'db-conn.php';
$link = mysqli_connect("$server","$user","$pass","$database") or die("Error " . mysqli_error($link));

// ============================== Set character set to utf8 ==================================
// $link = mysqli_connect('localhost', 'my_user', 'my_password', 'test');

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/* change character set to utf8 */

if (!mysqli_set_charset($link, "utf8")) {
    printf("Error loading character set utf8: %s\n", mysqli_error($link));
} else {
    printf("Current character set: %s\n", mysqli_character_set_name($link));
}

// ==========================================================================================================
$query = "SELECT id, posts, posted FROM tweets where userID='$userID' and posted = 0 ORDER by RAND() LIMIT 0,1" or die("Error in the consult.." . mysqli_error($link));
$result = mysqli_query($link, $query);

//Check results for enpty set:
if(mysqli_num_rows($result)==0){
  $emptySetError = "{$userID}  {$tweetContentDate}";
  error_log('No tweets found for user '.$emptySetError. "\n", 3, "tweeterErrors.log"); 
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

// Just in case the text is too long, trim to 140 characters:
// $theTweet = mb_strimwidth("$tweetStr", 0, 137, "...");
$theTweet = mb_strimwidth("$tweetStr", 0, 140);


// Post Tweet:
$connection->post('statuses/update', array('status' => $theTweet));

// Free result set
mysqli_free_result($result);

// Log script results:
$statusCode = $connection->http_code;
$statusSuccess = "{$tweetContentDate}  TwitterUser {$userID} tweeted {$theTweet}";
$statusError = "{$statusCode}  {$tweetContentDate} TwitterUser {$userID}";

if ($connection->http_code == 200) {
 		error_log('Success ' .$statusSuccess."\n", 3, "tweeterErrors.log");
		} else {
		error_log('Error posting to twitter: '.$statusError."\n", 3, "tweeterErrors.log");
	}

echo "<br />";		
echo "$theTweet";		

?>