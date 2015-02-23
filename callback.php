<?php

/*
 * THIS IS AN EXAMPLE FOR A OAUTH CALLBACK
 */

include('vendor/autoload.php');
include('config.php');

use Academicpuma\OAuth\OAuthAdapter;

session_start(); //resume session

// Get the RequestToken from session
$requestToken = unserialize($_SESSION['REQUEST_TOKEN']); 

/**
 * @var Academicpuma\OAuth\OAuthAdapter
 */
$client = new OAuthAdapter([
    'consumerKey'       => CONSUMER_KEY,
    'consumerSecret'    => CONSUMER_SECRET,
    'callbackUrl'       => 'http://guzzle.local/callback.php',
    'baseUrl'           => BASE_URL
]);

echo "<h1>RequestToken:</h1>";
var_dump($requestToken); //dump out requestToken

$accessToken = $client->getAccessToken($requestToken); //get AccessToken

$_SESSION['ACCESS_TOKEN'] = serialize($accessToken);

echo "<h1>AccessToken</h1>";
var_dump($accessToken); //dump out accessToken

?>
<p><a href="list.php">Click here for see an example</a></p>