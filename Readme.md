Converter from External tools to Tuleap
========================================

Bugzilla to Tuleap
------------------

Convert any XML export obtained from bugzilla into a Tuleap compatible archive

* Manage users (requires you to be authenticated when doing XML export)
* Manage one or several bugs (query)
* Fields imported
  * Title  (with semantic)
  * Bugzilla ID
  * Priority
  * Severity
  * Status  (with semantic and dependencies with resolution)
  * Submitted by
  * Submitted on
  * Last update by
  * Last update on
  * Assigned to (with semantic)
  * CC list
  * Artifact dependencies (simple)
  * Files attachments
  * Product (list of possible products is built out of the query result, might not be complete)
  * Component (idem)
  * Target milestone (idem)
  * Hardware (idem)
  * OS (idem)
* Import comments with authors and dates

Need to be improved:

* Depends/blocks: as of today, links are imported but without semantic. Need
  Tuleap Artifact links v2.

* Field dependencies between status and resolutions are basic and it need to be
  verified by a bugzilla user.

Not imported yet:

* QA contact: missing examples
* URL: missing examples
* Whiteboard: missing examples
* Keywords: missing examples

Not importable:

* Field values changes

  For instance when "status" changed from NEW to CONFIRMED
  There is no informations about changes in bugzilla XML.

* Tags

  feature doesn't exist in Tuleap

* Private comment or attachment

  feature doesn't exist in Tuleap

How to use it ?
--------------

With docker:

    $> docker run -ti --rm -u $(id -u):$(id -g) \
        -v ~/Downloads:/source enalean/bz2tuleap \
        /app/convert.sh /source/bugzilla_export.xml bugzilla

Where:

* -v ~/Downloads:/source is where you downloaded the bugzilla XML export
* /source/bugzilla_export.xml is the path to you XML export
* bugzilla is the keyword of the tool the XML comes from

=> this will create a ~/Downloads/bugzilla_export_tuleap directory that you can
   then import into your Tuleap instance
=> users will have their emails scrapped to "@example.com" by default, run
   with -f to keep original email

You can also run it directly if you have php installed:

    $> php converter.php /path/to/bugzilla.xml target_directory

Was tested and developed against Eclipse Bugzilla as of version 4.4.7.
