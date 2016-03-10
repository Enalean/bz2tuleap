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
  * Status  (with semantic)
  * Submitted by
  * Submitted on
  * Last update by
  * Last update on
  * Assigned to (with semantic)
  * CC list
  * Dependencies
* Import comments with authors and dates

Not covered:

* Import of changes

How to use it
-------------

    $> php converter.php /path/to/bugzilla.xml target_directory

Then you should import `target_directory` following Tuleap process.

Was tested and developed against Eclipse Bugzilla as of version 4.4.7.
