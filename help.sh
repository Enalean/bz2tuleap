#/bin/sh

cat <<EOF
Welcome to Bugzilla to Tuleap converter

How to run it ?

    docker run -ti --rm -u $(id -u):$(id -g) \
       -v ~/Downloads:/source enalean/bugzilla-to-tuleap \
       /app/convert.sh /source/bugzilla_export.xml

Where:
* -v ~/Downloads:/source is where you downloaded the bugzilla XML export
* /source/bugzilla_export.xml is the path to you XML export

=> this will create a ~/Downloads/bugzilla_export_tuleap directory that you can
   then import into your Tuleap instance
EOF
