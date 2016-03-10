#!/bin/sh

set -e

# Find the path of this directory
if [ -f "$0" ]; then
    mydir=$(dirname $(readlink -f $0))
else
    mydir=$(dirname $(readlink -f $(which $0)))
fi

if [ -f $1 ]; then
    target_directory=$(dirname $1)
    name=$(basename $1 .xml)
    final_dir="${target_directory}/${name}_tuleap"
    mkdir -p $final_dir
    php $mydir/converter.php $1 $final_dir
else
    echo "$1 is not a file"
fi
