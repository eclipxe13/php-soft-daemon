<?php

/**
 * This is an example of SoftDaemon implementation
 */

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Examples;

use Eclipxe\SoftDaemon\Sequencers\Linear as LinearSequencer;
use Eclipxe\SoftDaemon\SoftDaemon;

require __DIR__ . '/../vendor/autoload.php';

$executable = new ExampleExecutable([true, true, false, false, false, true, true, true, true]);
$sequencer = new LinearSequencer();
$maxWait = 20;
$sd = new SoftDaemon($executable, $sequencer, $maxWait);
$sd->run();

/* OUTPUT:
    Try to run 0 time: TRUE
    Try to run 1 time: TRUE
    Try to run 2 time: FALSE
    Try to run 3 time: FALSE
    Try to run 4 time: FALSE
    Try to run 5 time: TRUE
    Try to run 6 time: TRUE
    Try to run 7 time: TRUE
    Try to run 8 time: TRUE
    Try to run 9 time: FALSE
    Try to run 10 time: FALSE
    ...
    Try to run 15 time: FALSE
    ^C
    ExampleExecutable process 2
 */
