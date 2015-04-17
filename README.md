# PHP SoftDaemon Library

SoftDaemon provides a library to run continuously some code.

I create this library to execute procedures continuously and to manipulate the time between iterations. I also use to send signals to the processes in order to manipulate the behavior of the execution.

Do not reinvent the wheel, if cron jobs are suitable to you then use it.

## How it runs

You have to create an instance of SoftDaemon, it requires minimum an Executable object that implements SoftDaemon\Executable interface 

The SoftDaemon\Executable interface requires that you create two methods:

- signalHandler($signo): If you want to do something when a signal is received
- runOnce(): Method to run on each iteration

By example, you can use signalHandler($signo) to process SIGHUP.

Once you have instantiated a SoftDaemon object you can call the method run().
This method will enter into a loop and run Executable::runOnce() on every iteration.
At the end on every iteration the loop will wait

### Signals used

These signals are catched by SoftDaemon. All signals are passed to SoftDaemon\Executable::signalHandler($signo) before SoftDaemon do its own processing.

- *SIGHUP*: Reset the error counter to zero. Method: `SoftDaemon::resetErrorCounter()`
- *SIGUSR1*: Pause iterations. Method: `SoftDaemon::setPause(true)`
- *SIGUSR2*: Unpause iterations. Method: `SoftDaemon::setPause(false)`
- *SIGTERM*, *SIGINT*, *SIGQUIT*: Terminate the iterations. Method: `SoftDaemon::terminate()`


## How SoftDaemon knows how many time wait

The quantity of seconds to wait for signals and continue to the next iteration is determined by the pause state.

If the SoftDaemon is **on pause** then it will not call runOnce, it will only try to wait 1 second.

If **not on pause** then it will use the counter of errors and request the sequencer to determine the number of seconds to wait.

The number of errors is determined by the result of Executable::runOnce, if true the counter of errors is reset to zero, else the counter of errors will be increased by 1.

Anyhow, the number of seconds will be bounded to MinWait and MaxWait properties.

## About sequencers

A sequencer is an objects that implements SoftDaemon\Sequencer. It's propose is to receive the number of errors and return a quantity of seconds to wait. There are some predefined Sequencers already defined in the namespace SoftDaemon\Sequencer:

- Fixed: It always return the same quantity of seconds
- Linear: It return the quantity of seconds as the count of errors (0 -> 0, 1 -> 1, 2 -> 2, ...)
- Exponential: It return the quantity of seconds as the count of errors to an exponential minus 1, if the base is 2 then it will return the following numbers: (0 -> 0, 1 -> 1, 2 -> 3, 3 -> 7, 4 -> 15, ...)

You can create a sequencer with your own rules. Remember that the number of seconds returned by the sequencer is always bounded to MinWait and MaxWait.

## Contribute

Feel free to contribute using common github methods.
Some work is required, like:

* Include the project in Packagist archive and install instructions
* Include Code Climate, Code Climate, etc.
* Create documentation (github wiki?)
* Create some examples

## License

* License: http://www.opensource.org/licenses/mit-license.html MIT License
* Copyright 2015 The Authors

-- Carlos C Soto, email: eclipxe13@gmail.com, twitter: @eclipxoide