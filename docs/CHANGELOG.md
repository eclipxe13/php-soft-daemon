# CHANGELOG

## About SemVer

In summary, [SemVer](https://semver.org/) can be viewed as `[ Breaking ].[ Feature ].[ Fix ]`, where:

- Breaking version = includes incompatible changes to the API
- Feature version = adds new feature(s) in a backwards-compatible manner
- Fix version = includes backwards-compatible bug fixes

**Version `0.x.x` doesn't have to apply any of the SemVer rules**

## Version 3.0.0

Please, read the [Upgrade guide to version 3.x](UPGRADE_3.md) for relevant breaking changes.

Major changes:

- Drop compatibility for PHP 7.3, PHP 7.4, PHP 8.0 and PHP 8.1.
- Add compatibility for PHP 8.4 and PHP 8.5.
- Introduces interface `PcntlSignals`.
- Rename `Internal\PcntlSignals` to `PcntlSignals\PhpPcntlSignals`, remove `@internal` and make it final.
- Rename properties to use camel case.
- Enforce PHP types.

Minor changes:

- Update license year to 2026.

Development changes:

- Add `composer-normalize` as a development tool.
- Remove *Psalm* tool.
- Update coding standards rules.
- Set up scrutinizer to receive code coverage instead of creating it.
- On GitHub workflows:
  - Run jobs using PHP 8.5.
  - Add PHP 8.4 and PHP 8.5 to `phpunit` job matrix.
  - Rename matrix variable name `php-versions` to `php-version`.
  - Introduce `scrutinizer` job to create the code coverage and upload it.
  - Upgrade actions versions.
- Update development tools.

## Maintenance 2023-02-07

- Refactor test because method `expectWarning()` has been deprecated.
- Remove `/tests/_files` from GitHub language analysis.
- Use PHP8.2 on GitHub workflows.
- Update license year. Happy 2023!
- Update development tools.
- Fix build badge on `README.md`.

## Version 2.0.1

Extract `SoftDaemon::mainloop` read to a protected method `SoftDaemon::continueOnMainLoop()`.
This change fix the issue detected by `psalm:^5.x`.

Minor changes:

- Upgrade `psalm` version from `4.x` to `5.x`.
- Update development tools.
- Update code standard.
- Remove `@package` annotations.
- Update GH Workflow:
  - Replace deprecated `echo ::set-output` instruction.
  - Add PHP 8.2 to compatibility matrix.
  - Remove `composer` where it is not required.
- Set up `filter.dependency_paths` setting Scrutinizer-CI.

The following are changes made previously but not released.

### Unreleased 2022-05-27

This is a maintenance update. There are no changes to source code.

- Fix PSalm configuration: use attribute `errorLevel` instead of `totallyTyped`.
- Update license year to 2022.
- Move development tools management from `develop/install-development-tools` to `phive`.
- Update code style to PSR-12.

### Unreleased 2021-09-26

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
