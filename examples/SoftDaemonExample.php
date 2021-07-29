<?php

declare(strict_types=1);
/**
 * This is an example of SoftDaemon implementation
 */

use Eclipxe\SoftDaemon\Sequencers\Linear as LinearSequencer;
use Eclipxe\SoftDaemon\SoftDaemon;

/* @var Composer\Autoload\ClassLoader $autoloader */
$loader = include_once __DIR__ . '/../vendor/autoload.php';
$loader->add('', __DIR__);

$sequencer = new LinearSequencer();
$executable = new ExampleExecutable([true, true, false, false, false, true, true, true, true]);
$sd = new SoftDaemon($executable, $sequencer, 20);
$sd->run();
