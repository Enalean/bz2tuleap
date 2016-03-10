#!/bin/sh

set -e

# Find the path of this directory
if [ -f "$0" ]; then
    mydir=$(dirname $(readlink -f $0))
else
    mydir=$(dirname $(readlink -f $(which $0)))
fi

debug=true
file=$2
if [ "$1" == "-f" ]; then
    debug=false
elif [ -f "$1" ]; then
    file=$1
fi

if [ -f "$file" ]; then
    target_directory=$(dirname $file)
    name=$(basename $file .xml)
    final_dir="${target_directory}/${name}_tuleap"
    mkdir -p $final_dir
    php $mydir/converter.php $file $final_dir
    if [ "$debug" == "true" ]; then 
	sed -i 's/@[^<]*/@example.com/g' $final_dir/users.xml
    fi
else
    echo "$file is not a file"
fi
