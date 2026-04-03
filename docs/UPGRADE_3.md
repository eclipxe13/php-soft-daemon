# Upgrade guide to version 3.x

If you are just simply using this library it might be possible that you don't need to apply any changes.

## Changes on `SoftDaemon` class

The `SoftDaemon` class has several changes, most important, it changes the property names to camel case.

- `SoftDaemon#minwait` to `SoftDaemon#minWait`.
- `SoftDaemon#maxwait` to `SoftDaemon#maxWait`.
- `SoftDaemon#errorCount` to `SoftDaemon#errorCount`.
- `SoftDaemon#pcntlsignals` to `SoftDaemon#pcntlSignals`.
- `SoftDaemon#mainloop` to `SoftDaemon#mainLoop`.

The property `SoftDaemon#mainloop` has changed to a protectec constant `SoftDaemon#SIGNALS`.

The constructor no longer allows a `null` value for parameter `$sequencer`.
It is enforced to be an instance of the `Sequencer` interface.

## `PcntlSignals`

- The interface `PcntlSignals` has been introduced.
- The class `Internal\PcntlSignals` has been renamed to `PcntlSignals\PhpPcntlSignals`
  and it implements `PcntlSignals`.

## Full list of breaking changes

```text
REMOVED: Property Eclipxe\SoftDaemon\SoftDaemon#$minwait was removed
REMOVED: Property Eclipxe\SoftDaemon\SoftDaemon#$maxwait was removed
REMOVED: Property Eclipxe\SoftDaemon\SoftDaemon#$errorcount was removed
REMOVED: Property Eclipxe\SoftDaemon\SoftDaemon#$mainloop was removed
REMOVED: Property Eclipxe\SoftDaemon\SoftDaemon#$pcntlsignals was removed
REMOVED: Property Eclipxe\SoftDaemon\SoftDaemon#$signals was removed
CHANGED: Type of property Eclipxe\SoftDaemon\SoftDaemon#$executable changed from having no type to Eclipxe\SoftDaemon\Executable
CHANGED: Type of property Eclipxe\SoftDaemon\SoftDaemon#$sequencer changed from having no type to Eclipxe\SoftDaemon\Sequencer
CHANGED: Type of property Eclipxe\SoftDaemon\SoftDaemon#$pause changed from having no type to bool
SKIPPED: Unable to compile initializer in method Eclipxe\SoftDaemon\SoftDaemon::__construct() in file /src/SoftDaemon.php (line 47)
CHANGED: The parameter $sequencer of Eclipxe\SoftDaemon\SoftDaemon#__construct() changed from Eclipxe\SoftDaemon\Sequencer|null to a non-contravariant Eclipxe\SoftDaemon\Sequencer
CHANGED: The parameter $sequencer of Eclipxe\SoftDaemon\SoftDaemon#__construct() changed from Eclipxe\SoftDaemon\Sequencer|null to Eclipxe\SoftDaemon\Sequencer
CHANGED: Parameter 2 of Eclipxe\SoftDaemon\SoftDaemon#__construct() changed name from maxwait to maxWait
CHANGED: Parameter 3 of Eclipxe\SoftDaemon\SoftDaemon#__construct() changed name from minwait to minWait
CHANGED: Parameter 0 of Eclipxe\SoftDaemon\SoftDaemon#setMaxWait() changed name from maxwait to maxWait
CHANGED: Parameter 0 of Eclipxe\SoftDaemon\SoftDaemon#setMinWait() changed name from minwait to minWait
CHANGED: Type of property Eclipxe\SoftDaemon\Sequencers\Exponential#$base changed from having no type to int
```
