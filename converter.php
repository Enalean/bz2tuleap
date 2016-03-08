<?php

require_once 'autoload.php';

try  {
    $converter = new Bz2Tuleap\Converter();

    $converter->convert($argv[1], $argv[2]);
} catch(Exception $exception) {

}
