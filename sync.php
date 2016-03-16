<?php

require_once 'autoload.php';
require_once 'TuleapClient/autoload.php';
require_once 'vendor/autoload.php';

$synchronizer = new Bz2Tuleap\Synchronizer();
$synchronizer->sync($argv[1], $argv[2]);
