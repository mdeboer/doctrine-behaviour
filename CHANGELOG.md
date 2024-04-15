# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [4.1.1] - 2024-04-15

### Changed

- Updated dependencies

## [4.1.0] - 2024-04-11

### Changed

- Optimised `translate()` in [TranslatableTrait](src/TranslatableTrait.php)

## [4.0.0] - 2023-09-12

Please see [UPGRADING.md](UPGRADING.md) for upgrade instructions.

### Added

- [Upgrading](UPGRADING.md) document

### Changed

- Use event listeners instead of subscribers as they
  are [deprecated in Symfony 6.3](https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-listeners).
  Please see the updated documentation for [Timestampable](docs/Timestampable.md)
  and [Translatable](docs/Translatable.md) or see [UPGRADING](UPGRADING.md).
- Restructured and improved the tests

### Fixed

- Fixed expirable filter not always using correct column name
- Fixed soft-delete filter not always using correct column name

## [3.0.0] - 2023-07-30

This release is API compatible with v2.1.0, nothing has changed but the namespace, package name and the repository
where it is maintained. It is still maintained by me.

### Changed

- Updated namespace from `Cloudstek\DoctrineBehaviour\` to `mdeboer\DoctrineBehaviour`
- Update package name from `cloudstek/doctrine-behaviour` to `mdeboer/doctrine-behaviour`

## [2.1.0] - 2023-05-24

### Changed

- Allow DateTimeInterface in setters instead of just DateTime and DateTimeImmutable
- Update to PHPUnit 10.1

## [2.0.2] - 2022-06-29

### Added

- More documentation and examples
- Changelog

### Fixed

- Translation not being properly removed with setTranslations().

## [2.0.1] - 2022-06-29

### Added

- Translatable behaviour
- More tests
- Github action to automatically run tests

## [2.0.0] - 2022-06-15

### Changed

- Timestampable columns createdAt and updatedAt are no longer nullable.

## [1.0.1] - 2022-05-18

### Added

- Timestampable subscriber to automatically add entity listener to timestampable entities.

[Unreleased]: https://github.com/mdeboer/doctrine-behaviour/compare/v4.1.1...develop

[4.1.1]: https://github.com/mdeboer/doctrine-behaviour/compare/v4.1.0...v4.1.1

[4.1.0]: https://github.com/mdeboer/doctrine-behaviour/compare/v4.0.0...v4.1.0

[4.0.0]: https://github.com/mdeboer/doctrine-behaviour/compare/v3.0.0...v4.0.0

[3.0.0]: https://github.com/mdeboer/doctrine-behaviour/compare/v2.1.0...v3.0.0

[2.1.0]: https://github.com/Cloudstek/doctrine-behaviour/compare/v2.0.2...v2.1.0

[2.0.2]: https://github.com/Cloudstek/doctrine-behaviour/compare/v2.0.1...v2.0.2

[2.0.1]: https://github.com/Cloudstek/doctrine-behaviour/compare/v2.0.0...v2.0.1

[2.0.0]: https://github.com/Cloudstek/doctrine-behaviour/compare/v1.0.1...v2.0.0

[1.0.1]: https://github.com/Cloudstek/doctrine-behaviour/compare/v1.0.0...v1.0.1

[1.0.0]: https://github.com/Cloudstek/doctrine-behaviour/releases/tag/v1.0.0
