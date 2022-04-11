# This is my package shared

[![PHP](https://img.shields.io/packagist/php-v/spiral/shared.svg?style=flat-square)](https://packagist.org/packages/spiral/shared)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/spiral/shared.svg?style=flat-square)](https://packagist.org/packages/spiral/shared)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spiral/shared/run-tests?label=tests&style=flat-square)](https://github.com/spiral/shared/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spiral/shared.svg?style=flat-square)](https://packagist.org/packages/spiral/shared)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.


## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.0+
- Spiral framework 2.9+



## Installation

You can install the package via composer:

```bash
composer require spiral/shared
```

After package install you need to register bootloader from the package.

```php
protected const LOAD = [
    // ...
    \Spiral\Shared\Bootloader\SharedBootloader::class,
];
```

> Note: if you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [butschster](https://github.com/spiral)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
