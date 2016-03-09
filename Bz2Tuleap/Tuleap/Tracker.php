<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Tracker {

    private $users = array();
    private $field_id_counter = 0;
    private $fields_mapping = array();
    private $value_mapping = array();
    private $value_mapper;

    public function __construct() {
        $this->value_mapper = new IdMapper('V');
    }

    public function convert(SimpleXMLElement $bugzilla_xml, SimpleXMLElement $tuleap_xml) {
        $trackers = $tuleap_xml->addChild('trackers');
        $this->addOneTracker($bugzilla_xml, $trackers);
    }

    private function getNewFieldId($name) {
        $this->field_id_counter++;
        $this->fields_mapping[$name] = $this->field_id_counter;
        return $this->getFieldReference($name);
    }

    private function getFieldReference($name) {
        return 'F'.$this->fields_mapping[$name];
    }

    private function getFieldId($name) {
        return $this->fields_mapping[$name];
    }

    private function addOneTracker(SimpleXMLElement $bugzilla_xml, SimpleXMLElement $trackers) {
        $tracker = $trackers->addChild('tracker');
        $tracker->addAttribute('id', 'T1');
        $tracker->addAttribute('parent_id', '0');
        $tracker->addAttribute('instantiate_for_new_projects', '1');
        $this->addTrackerMetadata($tracker);
        $this->addArtifacts($bugzilla_xml, $tracker);
    }

    private function addTrackerMetadata(SimpleXMLElement $tracker) {
        $tracker->addChild('name', 'Bugs');
        $tracker->addChild('item_name', 'bug');
        $tracker->addChild('description', 'Bugs and requests');
        $tracker->addChild('color', 'inca_silver');
        $tracker->addChild('cannedResponses');
        $form_elements = $tracker->addChild('formElements');
        $this->addFields($form_elements);
        $this->addSemantics($tracker);
        $this->addReports($tracker);
        $this->addPermissions($tracker);
    }

    private function addFields(SimpleXMLElement $form_elements) {
        $c1 = $this->addStructureField('column', 'C1', 'C1', array(
            $this->addSimpleField('subby', 'submitted_by', 'Submitted by'),
            $this->addSimpleField('subon', 'submitted_on', 'Submitted on'),
        ));

        $c2 = $this->addStructureField('column', 'C2', 'C2', array(
            $this->addSimpleField('luby', 'last_update_by', 'Last update by'),
            $this->addSimpleField('lud', 'last_update_on', 'Last update on'),
        ));

        $field_set_details = $this->addStructureField('fieldset', 'details', 'Details', array(
            $this->addStructureField('column', 'C5', 'C5', array(
                $this->addSimpleField('string', 'summary', 'Summary')
            )),
            $this->addStructureField('column', 'C6', 'C6', array(
                $this->addSimpleField('int', 'bugzilla_id', 'Bugzilla id')
            )),
            $this->addSimpleField('text', 'description', 'Description'),
            $this->addStructureField('column', 'C3', 'C3', array(
                $this->addSelectBox('status', "Status", array(
                    'NEW',
                    'UNCONFIRMED',
                    'CONFIRMED',
                    'IN_PROGRESS',
                    'RESOLVED',
                    'VERIFIED',
                )),
                $this->addSelectBox('resolution', "Resolution", array(
                    'FIXED',
                    'INVALID',
                    'WONTFIX',
                    'DUPLICATE',
                    'WORKSFORME',
                )),
            )),
            $this->addStructureField('column', 'C4', 'C4', array(
                $this->addSelectBox('severity', "Severity", array(
                    'blocker',
                    'critical',
                    'major',
                    'normal',
                    'minor',
                    'trivial',
                    'enhancement',
                )),
                $this->addSelectBox('priority', "Priority", array(
                    'P1',
                    'P2',
                    'P3',
                    'P4',
                    'P5',
                )),
            )),
            $this->addStructureField('fieldset', 'links', 'Links', array(
                $this->addSimpleField('art_link', 'links', 'Links')
            )),
        ));

        $rank = 0;
        $c1->toXml($form_elements, $rank++);
        $c2->toXml($form_elements, $rank++);
        $field_set_details->toXml($form_elements, $rank++);
    }

    private function addPermissions(SimpleXMLElement $tracker) {
        $permissions = $tracker->addChild('permissions');
        $this->addPermissionOnTracker($permissions, 'PLUGIN_TRACKER_ACCESS_FULL', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('bugzilla_id'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('submitted_by'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('submitted_on'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('last_update_on'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('last_update_by'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');

        $this->addDefaultPermissions($permissions, $this->getFieldReference('summary'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('description'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('status'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('resolution'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('severity'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('priority'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('links'));
    }

    private function addStructureField($type, $name, $label, array $children) {
        return new StructureField(
            $this->addSimpleField($type, $name, $label),
            $children
        );
    }

    private function addSimpleField($type, $name, $label) {
        return new Field($this->getNewFieldId($name), $type, $name, $label);
    }

    private function addSelectBox($name, $label, $values) {
        return new SelectBoxField($this->addSimpleField('sb', $name, $label, 0), $values, $this->value_mapper);
    }

    private function addSemantics(SimpleXMLElement $tracker) {
        $semantics = $tracker->addChild('semantics');
        $title = $semantics->addChild('semantic');
        $title->addAttribute('type', 'title');
        $title->addChild('shortname', 'title');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', $this->getFieldReference('summary'));
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
        $this->addColumnToTableRenderer($columns, $this->getFieldReference('bugzilla_id'));
        $this->addColumnToTableRenderer($columns, $this->getFieldReference('summary'));
        $this->addColumnToTableRenderer($columns, $this->getFieldReference('submitted_by'));
        $this->addColumnToTableRenderer($columns, $this->getFieldReference('submitted_on'));
        $this->addColumnToTableRenderer($columns, $this->getFieldReference('last_update_by'));
    }

    private function addColumnToTableRenderer(SimpleXMLElement $columns, $name) {
        $field = $columns->addChild('field');
        $field->addAttribute('REF', $name);
    }

    private function addDefaultPermissions(SimpleXMLElement $permissions, $field_id) {
        $this->addPermissionOnField($permissions, $field_id, 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $field_id, 'PLUGIN_TRACKER_FIELD_SUBMIT', 'UGROUP_REGISTERED');
        $this->addPermissionOnField($permissions, $field_id, 'PLUGIN_TRACKER_FIELD_UPDATE', 'UGROUP_REGISTERED');
    }

    private function addPermissionOnTracker(SimpleXMLElement $permissions, $type, $ugroup) {
        $permission = $permissions->addChild('permission');
        $permission->addAttribute('scope', 'tracker');
        $permission->addAttribute('ugroup', $ugroup);
        $permission->addAttribute('type', $type);
    }

    private function addPermissionOnField(SimpleXMLElement $permissions, $field, $type, $ugroup) {
        $permission = $permissions->addChild('permission');
        $permission->addAttribute('scope', 'field');
        $permission->addAttribute('REF', $field);
        $permission->addAttribute('ugroup', $ugroup);
        $permission->addAttribute('type', $type);
    }

    private function addArtifacts(SimpleXMLElement $bugzilla_xml, SimpleXMLElement $tracker) {
        $tuleap_artifacts = $tracker->addChild('artifacts');
        foreach($bugzilla_xml as $bugzilla_bug) {
            $tuleap_artifact = $tuleap_artifacts->addChild('artifact');
            $this->addArtifact($bugzilla_bug, $tuleap_artifact);
        }
    }

    private function addArtifact(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_artifact) {
        $tuleap_artifact->addAttribute('id', (int) $bugzilla_bug->bug_id);
        $this->addChangesets($bugzilla_bug, $tuleap_artifact);
    }

    private function addChangesets(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_artifact) {
        $this->addInitialChangeset($bugzilla_bug, $tuleap_artifact);
        $this->addComments($bugzilla_bug, $tuleap_artifact);
    }

    private function addInitialChangeset(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_artifact) {
        $changeset = $this->createChangeset($tuleap_artifact, $bugzilla_bug->reporter, $bugzilla_bug->creation_ts);
        $changeset->addChild('comments');
        $this->addFieldsData($bugzilla_bug, $changeset);
    }

    private function createChangeset(SimpleXMLElement $tuleap_artifact, SimpleXMLElement $who_node, SimpleXMLElement $when_node) {
        $changeset = $tuleap_artifact->addChild('changeset');

        $this->addSubmittedInfo($changeset, $who_node, $when_node);

        return $changeset;
    }

    private function addSubmittedInfo(SimpleXMLElement $tuleap_node, SimpleXMLElement $who_node, SimpleXMLElement $when_node) {
        $username = $this->addUser($who_node);
        $submitted_by = $tuleap_node->addChild('submitted_by', $username);
        $submitted_by->addAttribute('format', 'username');
        $submitted_on = $tuleap_node->addChild('submitted_on', (string) $when_node);
        $submitted_on->addAttribute('format', 'ISO8601');
    }

    private function addComments(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_artifact) {
        foreach($bugzilla_bug->long_desc as $long_desc) {
            if ((int)$long_desc->comment_count === 0) {
                continue;
            }
            $changeset = $this->createChangeset($tuleap_artifact, $long_desc->who, $long_desc->bug_when);
            $comments = $changeset->addChild('comments');
            $comment = $comments->addChild('comment');
            $this->addSubmittedInfo($comment, $long_desc->who, $long_desc->bug_when);
            $body = $this->addChildWithCDataValue($comment, 'body', (string) $long_desc->thetext);
            $body->addAttribute('format', 'text');
        }
    }

    private function addFieldsData(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_changeset) {
        $this->addScalarData($tuleap_changeset, 'bugzilla_id', 'int', (int) $bugzilla_bug->bug_id);
        $this->addScalarData($tuleap_changeset, 'summary', 'string', (string) $bugzilla_bug->short_desc);
        // cannot add a link to a non existing target
        //$this->addScalarData($tuleap_changeset, 'links', 'art_link', (string) $bugzilla_bug->dependson);
        $this->addSelectBoxValue($tuleap_changeset, 'status', (string)$bugzilla_bug->bug_status);
        $this->addSelectBoxValue($tuleap_changeset, 'resolution', (string)$bugzilla_bug->resolution);
        $this->addSelectBoxValue($tuleap_changeset, 'severity', (string)$bugzilla_bug->bug_severity);
        $this->addSelectBoxValue($tuleap_changeset, 'priority', (string)$bugzilla_bug->priority);

        $first_long = $bugzilla_bug->long_desc[0];
        $this->addScalarData($tuleap_changeset, 'description', 'text', (string) $first_long->thetext);
    }

    private function addScalarData(SimpleXMLElement $tuleap_changeset, $field_name, $type, $bugzilla_value) {
        if ($bugzilla_value != "") {
            $field_change = $tuleap_changeset->addChild('field_change');
            $field_change->addAttribute('field_name', $field_name);
            $field_change->addAttribute('type', $type);
            $field_change->addChild('value', $bugzilla_value);
        }
    }

    private function addSelectBoxValue(SimpleXMLElement $tuleap_changeset, $field_name, $bugzilla_value) {
        if ($bugzilla_value != "") {
            $field_change = $tuleap_changeset->addChild('field_change');
            $field_change->addAttribute('field_name', $field_name);
            $field_change->addAttribute('type', 'list');
            $field_change->addAttribute('bind', 'static');
            $value = $field_change->addChild('value', $this->value_mapper->getId($bugzilla_value));
            $value->addAttribute('format', 'id');
        }
    }

    private function addUser(SimpleXMLElement $bugzilla_user_node) {
        $email = (string) $bugzilla_user_node;

        $at_place = strpos($email, '@');
        if ($at_place !== false) {
            $username = substr($email, 0, $at_place);
        } else {
            $username = $email;
        }

        if (! isset($this->users[$username])) {
            $this->users[$username] = array(
                'realname' => $bugzilla_user_node['name'],
            );
        }
        return $username;
    }



    public function getUsers() {
        return $this->users;
    }

    private function addChildWithCDataValue(SimpleXMLElement $parent_node, $node_name, $node_value) {
        $node     = $parent_node->addChild($node_name);
        $dom_node = dom_import_simplexml($node);
        $document = $dom_node->ownerDocument;
        $value    = SupportedXmlCharEncoding::getXMLCompatibleString($node_value);
        $cdata    = $document->createCDATASection($value);
        $dom_node->appendChild($cdata);
        return $node;
    }
}
