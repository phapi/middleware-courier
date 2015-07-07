# Courier Middleware
Courier is a HTTP Middleware responsible for sending a response to the client. The middleware relies on PSR-7 compatible request and response objects.

## Installation
This middleware is by default included in the [Phapi Framework](https://github.com/phapi/phapi-framework) but if you need to install it it's available to install via [Packagist](https://packagist.org) and [Composer](https://getcomposer.org).

```shell
$ php composer.phar require phapi/middleware-courier:1.*
```

## Configuration
The middleware itself does not have any configuration options.

See the [configuration documentation](http://phapi.github.io/docs/started/configuration/) for more information about how to configure the integration with the Phapi Framework.

## Usage
Populate the response object and Courier take care of sending the headers and body.

## Phapi
This middleware is a Phapi package used by the [Phapi Framework](https://github.com/phapi/phapi-framework). The middleware are also [PSR-7](https://github.com/php-fig/http-message) compliant and implements the [Phapi Middleware Contract](https://github.com/phapi/contract).

## License
Courier Middleware is licensed under the MIT License - see the [license.md](https://github.com/phapi/middleware-courier/blob/master/license.md) file for details

## Contribute
Contribution, bug fixes etc are [always welcome](https://github.com/phapi/middleware-courier/issues/new).
