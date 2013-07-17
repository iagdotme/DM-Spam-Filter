<?php

// An Array of phrases for spam filter to delete direct messages
$spamFilter = array(
    "Brilliant ✓ Come here first ➜",
    "but checkout my videos:)",
    "check for fake followers, and more",
    "funny picture of you",
    "Get a chance to win",
    "Help Yourself To This 100% Proven $30k Per Month",
    "Hey someperson has been saying",
    "I started using this new app for my Twitter",
    "join and follow me",
    "like us on facebook",
    "please like my facebook page",
    "please like my FB page",
    "rumour going around about you",
    "somebody is saying horrible things about you",
    "Someone is making up a horrible",
    "Thank you for following",
    "Thank you for the follow",
    "Thanks 4 following",
    "Thanks 4 the follow",
    "Thanks again for the follow",
    "Thanks for connecting",
    "Thanks for following",
    "Thanks for the follow",
    "Thanks for the the follow",
    "Thanks so much for following",
    "Thanks so much for the follow",
    "Thx 4 the follow",
    "Thx for follow",
    "uses TrueTwit validation service",
    "Welcome  #my Friend#",
    "You can auto follow back",
    "You;ve GOTTA See this!"
	);


/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token'        => "",
    'oauth_access_token_secret' => "",
    'consumer_key'              => "",
    'consumer_secret'           => ""
);


// That's it for editing folks! Don't edit below this line unless you know what you're doing!
// ------------------------------------------------------------------------------------------

// Include the PHP Wrapper
require_once('TwitterAPIExchange.php');

// We need this 'multineedle_stripos' function for the spam filter...
function multineedle_stripos($haystack, $needles) {
    foreach($needles as $needle) {
        if(stripos($haystack, $needle)) {$found = 1; }
    }
	if($found != 1) {$found = false;}
    return $found;
}

// Let's get the URL for the direct messages in the Twitter API
$url = 'https://api.twitter.com/1.1/direct_messages.json';

// Let's scan the first 50 dms (you can change this if you like)
$getfield = '?count=50';

// Because we only need Read only access, we only need to do a GET request (POST requests are needed for writing- eg. creating a tweet)
$requestMethod = 'GET';

// Let's make that API Request. Are you ready?! The returned data from Twitter will be in JSON format. We're going to convert that into XML using the json_decode function.
$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(),$assoc = TRUE);

echo "<h3>Deleting Spam...</h3>";			
// Now we're going to cycle through each direct message and scan them for spam			 
foreach($string as $items)
 {
	// If the direct message contains text from our spam array, let's delete it
  	if(multineedle_stripos($items['text'], $spamFilter) !== false)
		{
			// Start our counter to check if there has been any spam deleted
			$i++; 
  			// Let's get the URL for the Twitter API for deleting a direct message
			$url = 'https://api.twitter.com/1.1/direct_messages/destroy.json';
			// Because we're deleting a direct message, we need to do a POST request...
  			$requestMethod = 'POST';
			// Let's get the id of the direct message we want to delete...
  			$postfields = array('id' => $items['id']);
			// Let's make that API request to delete the direct message. No going back now!
  			$twitter = new TwitterAPIExchange($API_settings);
			$do = $twitter->buildOauth($url, $requestMethod)
        			      ->setPostfields($postfields)
           	 			  ->performRequest();
			// Output the HTML to say we've deleted that tweet.
			echo "<strong>DELETE:</strong> ".$items['text']."<br />";
		}
 }
if($i<1) {echo "<p>No spam in direct messages found.</p>";}
?>