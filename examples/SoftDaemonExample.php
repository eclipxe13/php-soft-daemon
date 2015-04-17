<?php
/**
 * This is an example of SoftDaemon implementation
 *
 * @category examples
 * @package SoftDaemon
 * @author Carlos C Soto <eclipxe13@gmail.com>
 * @copyright (c) 2015, The authors
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 */

use SoftDaemon\Sequencers\Linear as LinearSequencer;
use SoftDaemon\SoftDaemon;

/* @var Composer\Autoload\ClassLoader $autoloader */
$loader = include_once __DIR__.'/../vendor/autoload.php';
$loader->add('', __DIR__);

$sequencer = new LinearSequencer();
$executable = new ExampleExecutable([true, true, false, false, false, true, true, true, true]);
$sd = new SoftDaemon($executable, $sequencer, 20);
$sd->run();
