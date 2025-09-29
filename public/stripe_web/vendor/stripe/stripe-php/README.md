# Stripe PHP Library

[![Build Status](https://github.com/stripe/stripe-php/workflows/CI/badge.svg?branch=master)](https://github.com/stripe/stripe-php/actions?query=workflow%3ACI)
[![Latest Stable Version](https://img.shields.io/packagist/v/stripe/stripe-php.svg)](https://packagist.org/packages/stripe/stripe-php)
[![Total Downloads](https://img.shields.io/packagist/dt/stripe/stripe-php.svg)](https://packagist.org/packages/stripe/stripe-php)
[![License](https://img.shields.io/packagist/l/stripe/stripe-php.svg)](https://github.com/stripe/stripe-php/blob/master/LICENSE)
[![Code Coverage](https://img.shields.io/codecov/c/github/stripe/stripe-php.svg)](https://codecov.io/gh/stripe/stripe-php)

The official [Stripe](https://stripe.com) PHP library.

## Requirements

PHP 7.4.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require stripe/stripe-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/stripe/stripe-php/releases). Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/stripe-php/init.php');
```

## Dependencies

The bindings require the following extensions in order to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Started

Simple usage looks like:

```php
$stripe = new \Stripe\StripeClient('sk_test_your_api_key_here');
$customer = $stripe->customers->create([
    'description' => 'example customer',
    'email' => 'email@example.com',
    'payment_method' => 'pm_card_visa',
]);
echo $customer;
```

## Documentation

Please see the [PHP API docs](https://stripe.com/docs/api?lang=php) for the most up-to-date documentation.

## Legacy Version Support

### PHP 5.6

If you are using PHP 5.6, you can download v6.43.1 (zip, tar.gz) from our [releases page](https://github.com/stripe/stripe-php/releases). This version will continue to work with new versions of the Stripe API for applications using PHP 5.6.

### PHP 7.0

If you are using PHP 7.0, you can download v7.33.2 (zip, tar.gz) from our [releases page](https://github.com/stripe/stripe-php/releases). This version will continue to work with new versions of the Stripe API for applications using PHP 7.0.

## Custom Request Timeouts

*NOTE: We do not recommend setting the timeout at the StripeClient level, as doing so will cause the timeout to be applied to all HTTP requests in your application, including requests to the Stripe API.*

To set request timeouts on a per-request basis, you can do so by configuring a cURL handle:

```php
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.stripe.com/v1/charges',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10, // 10 seconds
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Stripe-Version: 2020-08-27',
    ],
]);
$response = curl_exec($curl);
```

## Development

Install dependencies:

```bash
composer install
```

## Testing

Install dependencies as mentioned above (which will resolve [PHPUnit](http://packagist.org/packages/phpunit/phpunit)), then you can run the test suite:

```bash
./vendor/bin/phpunit
```

The test suite depends on [stripe-mock], so make sure to fetch and run it from a background terminal ([stripe-mock's README](https://github.com/stripe/stripe-mock) also contains instructions for non-ruby setup):

```bash
go get -u github.com/stripe/stripe-mock
stripe-mock
```

Run the single test suite:

```bash
./vendor/bin/phpunit
```

Run a specific test method:

```bash
./vendor/bin/phpunit --filter testAccount
```

Run tests with coverage:

```bash
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover coverage.xml
```

## Recognition

- [Package on Packagist](https://packagist.org/packages/stripe/stripe-php)
- [API Documentation](https://stripe.com/docs/api?lang=php)

## Support

New features and bug fixes are deployed on a weekly basis. If you have a bug to report or a feature request, please [open an issue](https://github.com/stripe/stripe-php/issues/new) or contact [support@stripe.com](mailto:support@stripe.com).

[stripe-mock]: https://github.com/stripe/stripe-mock
