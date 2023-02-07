# eclipxe/php-soft-daemon

[![Source Code][badge-source]][source]
[![Packagist PHP Version Support][badge-php-version]][php-version]
[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![Build Status][badge-build]][build]
[![Scrutinizer][badge-quality]][quality]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

## PHP SoftDaemon Library

SoftDaemon provides a library to run continuously some code.

I create this library to execute procedures continuously and to manipulate the time between iterations. I also use it to send signals to the processes in order to manipulate the behavior of the execution.

Do not reinvent the wheel, if cron jobs are suitable to you then use them.

## How it runs

You have to create an instance of `SoftDaemon`, it requires minimum an `Executable` object that implements `SoftDaemon\Executable` interface 

The `SoftDaemon\Executable` interface requires that you create two methods:

- Will call `signalHandler(int $signo): void` to optionally do something with the signal.
- Will call `runOnce(): bool` on each iteration.

For example, you can use `signalHandler($signo)` to process `SIGHUP`.

Once you have instantiated a `SoftDaemon` object you can call the method `run()`.
This method will enter into a loop and run `Executable::runOnce()` on every iteration.
At the end on every iteration the loop will wait.

### Signals used

These signals are catched by SoftDaemon. All signals pass to `Executable::signalHandler($signo)` before `SoftDaemon do its own processing.

- *SIGHUP*: Reset the error counter to zero. Method: `SoftDaemon::resetErrorCounter()`
- *SIGUSR1*: Pause iterations. Method: `SoftDaemon::setPause(true)`
- *SIGUSR2*: Unpause iterations. Method: `SoftDaemon::setPause(false)`
- *SIGTERM*, *SIGINT*, *SIGQUIT*: Terminate the iterations. Method: `SoftDaemon::terminate()`

## How SoftDaemon knows how many seconds will wait

The pause state determines the quantity of seconds to wait for signals and continue to the next iteration.

If the SoftDaemon is **on pause** then it will not call `runOnce`, it will only try to wait 1 second.

If **not on pause** then it will use the counter of errors and request the sequencer to determine the number of seconds to wait.

The result of `Executable::runOnce(): bool` determines the number of errors. It is reset to zero when `runOnce()` returns `true`. It is increased by 1 when `runOnce()` returns `false`.

Anyhow, the number of seconds will be bounded to `MinWait` and `MaxWait` properties.

## About sequencers

A sequencer is an objects that implements `SoftDaemon\Sequencer`. Its purpose is to receive the number of errors and return a quantity of seconds to wait. There are some predefined Sequencers already defined in the namespace `SoftDaemon\Sequencer`:

- *Fixed*: It always returns the same quantity of seconds.
- *Linear*: It returns the quantity of seconds as the count of errors `(0 -> 0, 1 -> 1, 2 -> 2, ...)`.
- *Exponential*: It returns the quantity of seconds as the count of errors to an exponential minus 1, if the base is 2 then it will return the following numbers: `(0 -> 0, 1 -> 1, 2 -> 3, 3 -> 7, 4 -> 15, ...)`.

You can create a sequencer with your own rules. The `MinWait` and `MaxWait` limit the boundaries of seconds returned by the sequencer.

## PHP Support

This library is compatible with at least the oldest [PHP Supported Version](http://php.net/supported-versions.php)
with **active** support. Please, try to use PHP full potential.

We adhere to [Semantic Versioning](https://semver.org/).
We will not introduce any compatibility backwards change on major versions.

Internal classes (using `@internal` annotation) are not part of this agreement
as they must only exist inside this project. Do not use them in your project.

## Contributing

Contributions are welcome! Please read [CONTRIBUTING][] for details
and don't forget to take a look the [TODO][] and [CHANGELOG][] files.

## Copyright and License

The `eclipxe/php-soft-daemon` library is copyright © [Carlos C Soto](http://eclipxe.com.mx/)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for more information.

[contributing]: https://github.com/eclipxe13/php-soft-daemon/blob/main/CONTRIBUTING.md
[changelog]: https://github.com/eclipxe13/php-soft-daemon/blob/main/docs/CHANGELOG.md
[todo]: https://github.com/eclipxe13/php-soft-daemon/blob/main/docs/TODO.md

[source]: https://github.com/eclipxe13/php-soft-daemon
[php-version]: https://packagist.org/packages/eclipxe13/php-soft-daemon
[release]: https://github.com/eclipxe13/php-soft-daemon/releases
[license]: https://github.com/eclipxe13/php-soft-daemon/blob/main/LICENSE
[build]: https://github.com/eclipxe13/php-soft-daemon/actions/workflows/build.yml?query=branch:main
[quality]: https://scrutinizer-ci.com/g/eclipxe13/php-soft-daemon/
[coverage]: https://scrutinizer-ci.com/g/eclipxe13/php-soft-daemon/code-structure/main/code-coverage
[downloads]: https://packagist.org/packages/eclipxe/php-soft-daemon

[badge-source]: https://img.shields.io/badge/source-eclipxe/php--soft--daemon-blue?style=flat-square
[badge-php-version]: https://img.shields.io/packagist/php-v/eclipxe/php-soft-daemon?style=flat-square
[badge-release]: https://img.shields.io/github/release/eclipxe13/php-soft-daemon?style=flat-square
[badge-license]: https://img.shields.io/github/license/eclipxe13/php-soft-daemon?style=flat-square
[badge-build]: https://img.shields.io/github/actions/workflow/status/eclipxe13/php-soft-daemon/build.yml?branch=main&style=flat-square
[badge-quality]: https://img.shields.io/scrutinizer/g/eclipxe13/php-soft-daemon/main?style=flat-square
[badge-coverage]: https://img.shields.io/scrutinizer/coverage/g/eclipxe13/php-soft-daemon/main?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/eclipxe/php-soft-daemon?style=flat-square
