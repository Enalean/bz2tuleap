<?php

require_once 'vendor/autoload.php';

$converter = new Bz2Tuleap\Converter();

$converter->convert($argv[1], $argv[2], $argv[3]);
