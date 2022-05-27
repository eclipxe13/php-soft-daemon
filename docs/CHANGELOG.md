# CHANGELOG

## About SemVer

In summary, [SemVer](https://semver.org/) can be viewed as `[ Breaking ].[ Feature ].[ Fix ]`, where:

- Breaking version = includes incompatible changes to the API
- Feature version = adds new feature(s) in a backwards-compatible manner
- Fix version = includes backwards-compatible bug fixes

**Version `0.x.x` doesn't have to apply any of the SemVer rules**

## Unreleased 2022-05-27

This is a maintenance update. There are no changes to source code.

- Fix PSalm configuration: use attribute `errorLevel` instead of `totallyTyped`.
- Update license year to 2022.
- Move development tools management from `develop/install-development-tools` to `phive`.
- Update code style to PSR-12.

## Unreleased 2021-09-26

Development changes:

- Improve code coverage.
- Run CI on PHP 8.0.
- Move code coverage creation to Scrutinizer.
- Build directory should be empty.

## Version 2.0.0

Project refactory. Main changes:

- Namespace `Eclipxe\SoftDaemon`.
- Add argument and return strict types.
- Bump PHP version to 7.3.
- Add support for PHP 8.0.
- Upgrade all development environment.

## Version 1.0.0

Initial release.
