#!/bin/sh

phpab -b . -o autoload.php Bz2Tuleap

cd TuleapClient; phpab -o autoload.php .
