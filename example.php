<?php

// Require the Authority Labs library
require_once("authlabs.php");

// Instantiate a new Authority Labs library class
$authlabs = new Authoritylabspartner();

// Set your Auth Token (redacted here)
$auth_token = "XXXXXX";

// Set the URL of your callback to receive the response from Authority Labs
$callback_url = "http://requestb.in/15qp2lp1";


// Make a request to authority labs asking for a priority keyword result and asking them to send the result back to the defined callback URL
$result = $authlabs->priorityPartnerKeyword("vuurr", 'vuurr.com', $auth_token, "google", "en-US", "false", $callback_url);

// You can also do a normal request that will not return in a matter of minutes (commented out below)
//$result = $authlabs->partnerKeyword("vuurr", 'vuurr.com', $auth_token, "google", "en-US", "false", $callback_url);

// dump the results to the page so you can see the array returned - you should get a response code of 200.

echo "<pre>";
print_r($result);
echo "</pre>";

// Authority Labs will post your result back to the callback URL defined above.
// You can create a requestbin and use that as the place to see what Authority Labs posts back to the callback URL.
// Visit requestbin at http://requestb.in/

/////////////////////////////////////////////

// Now let's suppose you wanted to actually find what you rank for (which you do!)
// Set the $url to the posted json_callback value Authority Labs sent back to your requestbin
// NOTE: if you were doing this in production, you'd simply set $url = $_POST['json_callback'];
// For this example, we're just going to set it manually

$url = 'http://api.authoritylabs.com/keywords/get.json?keyword=vuurr&rank_date=2013-01-09&locale=en-us&engine=google&pages_from=false&lang_only=false&geo=&autocorrect=';
$result = $authlabs->parseRanks('vuurr.com', $url, $auth_token);

echo "<pre>";
print_r($result);
echo "</pre>";

// The above result has the array key set to the actual rank, and the array value equal to the URL that's showing for that SERP

/////////////////////////////////////////////

// Now let's suppose you want to go back and get the latest result for the same keyword and domain
// Simply make a request to getPartnerKeyword with the usual parameters, and it will return exactly as the parseRanks function does
$result = $authlabs->getPartnerKeyword("vuurr", 'vuurr.com', $auth_token, "google", "en-US", "false");

echo "<pre>";
print_r($result);
echo "</pre>";