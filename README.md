# Doctrine Behaviour

[![Build Status](https://img.shields.io/github/actions/workflow/status/mdeboer/doctrine-behaviour/phpunit.yml)](https://github.com/mdeboer/doctrine-behaviour/actions) [![issues](https://img.shields.io/github/issues/mdeboer/doctrine-behaviour)](https://github.com/mdeboer/doctrine-behaviour/issues) [![license](https://img.shields.io/github/license/mdeboer/doctrine-behaviour)](https://github.com/mdeboer/doctrine-behaviour/blob/main/LICENSE) [![dependencies](https://img.shields.io/librariesio/github/mdeboer/doctrine-behaviour)](https://libraries.io/packagist/mdeboer%2Fdoctrine-behaviour) [![downloads](https://img.shields.io/packagist/dt/mdeboer/doctrine-behaviour)](https://packagist.org/packages/mdeboer/doctrine-behaviour)

> Library for different Doctrine entity behaviours (timestampable, expirable etc.)

## Requirements

- PHP 8.1+
- Doctrine ORM 2
- intl extension

## Installation

```shell
$ composer require mdeboer/doctrine-behaviour
```

## Running tests

```
$ vendor/bin/phpunit
```

## Behaviours

- [Timestampable](docs/Timestampable.md) - For automatically timestamping entities (created at, updated at)
- [SoftDeletable](docs/SoftDeletable.md) - For soft-deleting entities
- [Expirable](docs/Expirable.md) - For entities that can have an expiration date
- [Translatable](docs/Translatable.md) - For (partially) translatable entities

## License

MIT

## Changelog

See [CHANGELOG](./CHANGELOG.md)
