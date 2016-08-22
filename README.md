# Wrapper for Upsales API
For more information regarding the api, see https://api-docs.upsales.com/#/reference/gettingStarted/

## Dependencies
- php 7.0+
- composer

## Installation
Add a repository to your composer.json like so:
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/VinniaAB/upsales.git"
    }
  ]
}
```

And then require the package with composer:
```shell
composer require vinnia/upsales
```

## Usage
A complete html example is located in `example.php`.

```php
use Vinnia\Upsales\Client;

$client = Client::make('access_token');

$response = $client->getClients();

$data = json_decode((string) $response->getBody(), $assoc = true);
var_dump($data);
```

## Testing
Copy `env.sample.php` into `env.php` and enter a valid access token. Then run the tests:
```shell
vendor/bin/codecept run
```
