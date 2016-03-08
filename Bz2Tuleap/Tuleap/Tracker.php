<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Tracker {

    private $users = array();
    private $field_id_counter = 0;
    private $fields_mapping = array();
    private $value_id_counter = 0;
    private $value_mapping = array();

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
        $this->addBugzillaId($form_elements);
        $this->addSubmittedBy($form_elements);
        $this->addSubmittedOn($form_elements);
        $this->addTitle($form_elements);
        $this->addLastUpdateBy($form_elements);
        $this->addLastUpdateOn($form_elements);
        $this->addSelectBox($form_elements, 'status', "Status", array(
            'NEW',
            'UNCONFIRMED',
            'CONFIRMED',
            'IN_PROGRESS',
            'RESOLVED',
            'VERIFIED',
        ));
        $this->addSelectBox($form_elements, 'resolution', "Resolution", array(
            'FIXED',
            'INVALID',
            'WONTFIX',
            'DUPLICATE',
            'WORKSFORME',
        ));
    }

    private function addPermissions(SimpleXMLElement $tracker) {
        $permissions = $tracker->addChild('permissions');
        $this->addPermissionOnTracker($permissions, 'PLUGIN_TRACKER_ACCESS_FULL', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('bugzilla_id'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('submitted_by'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('submitted_on'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('last_update_on'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $this->getFieldReference('last_update_by'), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');

        $this->addDefaultPermissions($permissions, $this->getFieldReference('title'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('status'));
        $this->addDefaultPermissions($permissions, $this->getFieldReference('resolution'));
    }

    private function addBugzillaId(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'int');
        $field->addAttribute('ID', $this->getNewFieldId('bugzilla_id'));
        $field->addAttribute('rank', '0');
        $field->addChild('name', 'bugzilla_id');
        $field->addChild('label', 'Bugzilla Id');
    }


    private function addSubmittedBy(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'subby');
        $field->addAttribute('ID', $this->getNewFieldId('submitted_by'));
        $field->addAttribute('rank', '1');
        $field->addChild('name', 'submitted_by');
        $field->addChild('label', 'Submitted by');
    }

    private function addSubmittedOn(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'subon');
        $field->addAttribute('ID', $this->getNewFieldId('submitted_on'));
        $field->addAttribute('rank', '2');
        $field->addChild('name', 'submitted_on');
        $field->addChild('label', 'Submitted on');
    }

    private function addTitle(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'string');
        $field->addAttribute('ID', $this->getNewFieldId('title'));
        $field->addAttribute('rank', '3');
        $field->addChild('name', 'summary');
        $field->addChild('label', 'Summary');
    }

    private function addLastUpdateBy(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'luby');
        $field->addAttribute('ID', $this->getNewFieldId('last_update_by'));
        $field->addAttribute('rank', '5');
        $field->addChild('name', 'last_update_by');
        $field->addChild('label', 'Last update by');
    }

    private function addLastUpdateOn(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'lud');
        $field->addAttribute('ID', $this->getNewFieldId('last_update_on'));
        $field->addAttribute('rank', '6');
        $field->addChild('name', 'last_update_on');
        $field->addChild('label', 'Last update on');
    }

    private function addSelectBox(SimpleXMLElement $form_elements, $name, $label, $values) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'sb');
        $field->addAttribute('ID', $this->getNewFieldId($name));
        $field->addAttribute('rank', $this->field_id_counter);
        $field->addChild('name', $name);
        $field->addChild('label', $label);

        $bind = $field->addChild('bind');
        $bind->addAttribute('type', 'static');
        $bind->addAttribute('is_rank_alpha', '0');
        $items = $bind->addChild('items');
        foreach ($values as $value) {
            $this->addBindItem($items, $value);
        }
    }

    private function addBindItem(SimpleXMLElement $items, $label) {
        $this->value_id_counter++;
        $this->value_mapping[$label] = $this->value_id_counter;
        $item = $items->addChild('item');
        $item->addAttribute('ID', 'V'.$this->value_id_counter);
        $item->addAttribute('label', $label);
        $item->addAttribute('is_hidden', 0);
    }

    private function addSemantics(SimpleXMLElement $tracker) {
        $semantics = $tracker->addChild('semantics');
        $title = $semantics->addChild('semantic');
        $title->addAttribute('type', 'title');
        $title->addChild('shortname', 'title');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', $this->getFieldReference('title'));
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
        $this->addColumnToTableRenderer($columns, $this->getFieldReference('title'));
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
        $this->addUser($who_node);
        $submitted_by = $tuleap_node->addChild('submitted_by', $who_node);
        $submitted_by->addAttribute('format', 'username');
        $submitted_on = $tuleap_node->addChild('submitted_on', (string) $when_node);
        $submitted_on->addAttribute('format', 'ISO8601');
    }

    private function addComments(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_artifact) {
        foreach($bugzilla_bug->long_desc as $long_desc) {
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
        $this->addScalarData($tuleap_changeset, 'title', 'string', (string) $bugzilla_bug->short_desc);
        $this->addSelectBoxValue($tuleap_changeset, 'status', (string)$bugzilla_bug->bug_status);
        $this->addSelectBoxValue($tuleap_changeset, 'resolution', (string)$bugzilla_bug->resolution);
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
            $value = $field_change->addChild('value', $this->value_mapping[$bugzilla_value]);
            $value->addAttribute('format', 'id');
        }
    }

    private function addUser(SimpleXMLElement $bugzilla_user_node) {
        $user_name = (string) $bugzilla_user_node;
        if (! isset($this->users[$user_name])) {
            $this->users[$user_name] = array(
                'realname' => $bugzilla_user_node['name'],
            );
        }
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
