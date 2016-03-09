<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Tracker {

    private $users = array();
    private $value_mapper;
    private $field_mapper;
    private $user_mapper;

    public function __construct() {
        $this->value_mapper = new IdMapper('V');
        $this->field_mapper = new IdMapper('F');
        $this->user_mapper  = new UserMapper();
    }

    public function convert(SimpleXMLElement $bugzilla_xml, SimpleXMLElement $tuleap_xml) {
        $trackers = $tuleap_xml->addChild('trackers');
        $this->addOneTracker($bugzilla_xml, $trackers);
    }

    private function addOneTracker(SimpleXMLElement $bugzilla_xml, SimpleXMLElement $trackers) {
        $tracker = $trackers->addChild('tracker');
        $tracker->addAttribute('id', 'T1');
        $tracker->addAttribute('parent_id', '0');
        $tracker->addAttribute('instantiate_for_new_projects', '1');
        $this->addTrackerMetadata($tracker);
        foreach ($this->getArtifacts($bugzilla_xml, $tracker) as $artifact) {
            $artifact->toXml($tracker);
        }
    }

    private function addTrackerMetadata(SimpleXMLElement $tracker) {
        $tracker->addChild('name', 'Bugs');
        $tracker->addChild('item_name', 'bug');
        $tracker->addChild('description', 'Bugs and requests');
        $tracker->addChild('color', 'inca_silver');
        $tracker->addChild('cannedResponses');
        $fields = $this->getFields($tracker);
        $fields->toXml($tracker);
        $this->addSemantics($tracker);
        $this->addReports($tracker);
        $this->addPermissions($tracker, $fields);
    }

    private function getFields() {
        return new FormElements(array(
            new Column($this->field_mapper, array(
                new Field($this->field_mapper, 'subby', 'submitted_by', 'Submitted by', new ReadOnlyFieldPermissions()),
                new Field($this->field_mapper, 'subon', 'submitted_on', 'Submitted on', new ReadOnlyFieldPermissions()),
            )),

            new Column($this->field_mapper, array(
                new Field($this->field_mapper, 'luby', 'last_update_by', 'Last update by', new ReadOnlyFieldPermissions()),
                new Field($this->field_mapper, 'lud', 'last_update_on', 'Last update on', new ReadOnlyFieldPermissions()),
            )),

            new FieldSet($this->field_mapper, 'Details', array(
                new Column($this->field_mapper, array(
                    new Field($this->field_mapper, 'string', 'summary', 'Summary', new DefaultFieldPermissions())
                )),
                new Column($this->field_mapper, array(
                    new Field($this->field_mapper, 'int', 'bugzilla_id', 'Bugzilla id', new ReadOnlyFieldPermissions())
                )),
                new Field($this->field_mapper, 'text', 'description', 'Description', new DefaultFieldPermissions()),
                new Column($this->field_mapper, array(
                    new SelectBoxField($this->field_mapper, $this->value_mapper, 'status', "Status", array(
                        'NEW',
                        'UNCONFIRMED',
                        'CONFIRMED',
                        'IN_PROGRESS',
                        'RESOLVED',
                        'VERIFIED',
                    ), new DefaultFieldPermissions()),
                    new SelectBoxField($this->field_mapper, $this->value_mapper, 'resolution', "Resolution", array(
                        'FIXED',
                        'INVALID',
                        'WONTFIX',
                        'DUPLICATE',
                        'WORKSFORME',
                    ), new DefaultFieldPermissions()),
                )),
                new Column($this->field_mapper, array(
                    new SelectBoxField($this->field_mapper, $this->value_mapper, 'severity', "Severity", array(
                        'blocker',
                        'critical',
                        'major',
                        'normal',
                        'minor',
                        'trivial',
                        'enhancement',
                    ), new DefaultFieldPermissions()),
                    new SelectBoxField($this->field_mapper, $this->value_mapper, 'priority', "Priority", array(
                        'P1',
                        'P2',
                        'P3',
                        'P4',
                        'P5',
                    ), new DefaultFieldPermissions()),
                )),
            )),

            new FieldSet($this->field_mapper, 'Links', array(
                new Field($this->field_mapper, 'art_link', 'links', 'Links', new DefaultFieldPermissions()),
            )),
        ));
    }

    private function addPermissions(SimpleXMLElement $tracker, $fields) {
        $permissions = $tracker->addChild('permissions');
        $this->addPermissionOnTracker($permissions, 'PLUGIN_TRACKER_ACCESS_FULL', 'UGROUP_ANONYMOUS');

        $fields->permissionsToXml($permissions);
    }

    private function addSemantics(SimpleXMLElement $tracker) {
        $semantics = $tracker->addChild('semantics');
        $title = $semantics->addChild('semantic');
        $title->addAttribute('type', 'title');
        $title->addChild('shortname', 'title');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', $this->field_mapper->getReference('summary'));
    }

    private function addReports(SimpleXMLElement $tracker) {
        $reports = $tracker->addChild('reports');
        $report  = $reports->addChild('report');
        $report->addAttribute('is_default', 1);
        $report->addChild('name', 'Default');
        $report->addChild('description', 'Default report');
        $report->addChild('criterias');
        $renderers = $report->addChild('renderers');
        $renderer = $renderers->addChild('renderer');
        $renderer->addAttribute('type', 'table');
        $renderer->addAttribute('rank', '0');
        $renderer->addAttribute('chunksz', '50');
        $renderer->addChild('name', 'Results');
        $columns = $renderer->addChild('columns');
        $this->addColumnToTableRenderer($columns, $this->field_mapper->getReference('bugzilla_id'));
        $this->addColumnToTableRenderer($columns, $this->field_mapper->getReference('summary'));
        $this->addColumnToTableRenderer($columns, $this->field_mapper->getReference('submitted_by'));
        $this->addColumnToTableRenderer($columns, $this->field_mapper->getReference('submitted_on'));
        $this->addColumnToTableRenderer($columns, $this->field_mapper->getReference('last_update_by'));
    }

    private function addColumnToTableRenderer(SimpleXMLElement $columns, $name) {
        $field = $columns->addChild('field');
        $field->addAttribute('REF', $name);
    }

    private function addPermissionOnTracker(SimpleXMLElement $permissions, $type, $ugroup) {
        $permission = $permissions->addChild('permission');
        $permission->addAttribute('scope', 'tracker');
        $permission->addAttribute('ugroup', $ugroup);
        $permission->addAttribute('type', $type);
    }

    private function getArtifacts(SimpleXMLElement $bugzilla_xml) {
        $artifacts = array();
        foreach($bugzilla_xml as $bugzilla_bug) {
            $artifacts[] = new Artifact(
                (int) $bugzilla_bug->bug_id,
                $this->getChangesets($bugzilla_bug)
            );
        }
        return $artifacts;
    }

    private function getChangesets(SimpleXMLElement $bugzilla_bug) {
        $changeset = array($this->getInitialChangeset($bugzilla_bug));
        return array_merge($changeset, $this->getChangesetComments($bugzilla_bug));
    }

    private function getInitialChangeset(SimpleXMLElement $bugzilla_bug) {
        return new Changeset(
            (string)$bugzilla_bug->creation_ts,
            (string)$this->user_mapper->getUser($bugzilla_bug->reporter),
            '',
            $this->getFieldsData($bugzilla_bug)
        );
    }

    private function getChangesetComments(SimpleXMLElement $bugzilla_bug) {
        $changesets = array();
        foreach($bugzilla_bug->long_desc as $long_desc) {
            if ((int)$long_desc->comment_count === 0) {
                continue;
            }
            $changesets[] = new Changeset(
                (string)$long_desc->bug_when,
                $this->user_mapper->getUser($long_desc->who),
                (string) $long_desc->thetext,
                array()
            );
        }
        return $changesets;
    }

    private function getFieldsData(SimpleXMLElement $bugzilla_bug) {
        return array_filter(array(
            $this->getScalarData('bugzilla_id', 'int', (int) $bugzilla_bug->bug_id),
            $this->getScalarData('summary', 'string', (string) $bugzilla_bug->short_desc),
            // cannot add a link to a non existing target
            //$this->addScalarData($tuleap_changeset, 'links', 'art_link', (string) $bugzilla_bug->dependson);
            $this->getSelectBoxValue('status', (string)$bugzilla_bug->bug_status),
            $this->getSelectBoxValue('resolution', (string)$bugzilla_bug->resolution),
            $this->getSelectBoxValue('severity', (string)$bugzilla_bug->bug_severity),
            $this->getSelectBoxValue('priority', (string)$bugzilla_bug->priority),

            $this->getScalarData('description', 'text', (string) $bugzilla_bug->long_desc[0]->thetext),
        ));
    }

    private function getScalarData($field_name, $type, $bugzilla_value) {
        if ($bugzilla_value != "") {
            return new ScalarFieldChange($field_name, $type, $bugzilla_value);
        }
        return null;
    }

    private function getSelectBoxValue($field_name, $bugzilla_value) {
        if ($bugzilla_value != "") {
            return new ListFieldChange($field_name, $this->value_mapper->getId($bugzilla_value));
        }
        return null;
    }

    public function getUsers() {
        return $this->user_mapper->getUsers();
    }
}
