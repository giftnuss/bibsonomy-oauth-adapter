# OAuthAdapter for PUMA and BibSonomy #

OAuthAdapter is a service library which uses [Guzzle HTTP client](https://github.com/guzzle/guzzle) in order to 
get access to the [PUMA](http://www.academic-puma.de)/[BibSonomy](http://www.bibsonomy.org) API via OAuth1. In 
Addition, 
OAuthAdapter helps
 you to perform a token exchange.

## Installation ##

Use composer to add OAuthAdapter to your PHP project.

```
$ composer require academicpuma/oauthadapter
```

## How to use OAuthAdapter ##

1. Include the autoloader.

```
:::php
$projectPath = 'projectpath';
include $projectPath.'vendor/autoload.php';
```

2. Initialize the OAuthAdapter

```
:::php
use AcademicPuma\OAuth\OAuthAdapter;
$client = new OAuthAdapter([
    'consumerKey'       => CONSUMER_KEY,
    'consumerSecret'    => CONSUMER_SECRET,
    'callbackUrl'       => CALLBACK_URL,
    'baseUrl'           => BASE_URL
]);
```