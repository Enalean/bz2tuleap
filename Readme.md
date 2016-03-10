Converter from Bugzilla to tuleap
=================================

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
  * Files attachments (/!\ hardcoded with bugzilla URL)
  * Product (list of possible products is built out of the query result, might not be complete)
  * Component (list of possible components is built out of the query result, might not be complete)
* Import comments with authors and dates

Need to be improved:

* Depends/blocks: as of today, links are imported but without semantic. Need
  Tuleap Artifact links v2.

Not imported yet:

* Version
* Target milestone
* Hardware

* QA contact: missing examples
* URL: missing examples
* Whiteboard: missing examples
* Keywords: missing examples
* Tags (feature doesn't exist in Tuleap)

Not importable:

* Field values changes

  For instance when "status" changed from NEW to CONFIRMED
  There is no informations about changes in bugzilla XML.

How to use it
-------------

    $> php converter.php /path/to/bugzilla.xml target_directory

Then you should import `target_directory` following Tuleap process.

Was tested and developed against Eclipse Bugzilla as of version 4.4.7.
