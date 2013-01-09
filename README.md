# Authority Labs PHP Wrapper
Provides a quick few functions to get you up and running with the Authority Labs partner API

## Usage
Simply require authlabs.php in your code, and create a new Authoritylabspartner object. You'll also want to set your auth token (your API key)

	require_once("authlabs.php");
	$authlabs = new Authoritylabspartner();
	$auth_token = "XXXXXX";

You can now start making request. To request SERPs for a keyword immediately, use the priorityPartnerKeyword() method.

	$authlabs->priorityPartnerKeyword("vuurr", 'vuurr.com', $auth_token, "google", "en-US", "false", $callback_url);

You can use the partnerKeyword() method to request a standard report

To parse the returned $_POST data from Authority Labs, use the parseRanks() method.

	$authlabs->parseRanks('vuurr.com', $_POST['json_callback'], $auth_token); 

The above result has the array key set to the actual rank, and the array value equal to the URL that's showing for that SERP.

Now let's suppose you want to go back and get the latest result for the same keyword and domain. Simply make a request to getPartnerKeyword() with the usual parameters, and it will return exactly as the parseRanks() method does.

	$authlabs->getPartnerKeyword("vuurr", 'vuurr.com', $auth_token, "google", "en-US", "false");

See example.php for these function in action.