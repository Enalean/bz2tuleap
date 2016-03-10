<?php

require_once 'autoload.php';

$converter = new Bz2Tuleap\Converter();
$converter->convert($argv[1], $argv[2]);
