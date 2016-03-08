<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Tracker {

    private $users = array();

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
    }

    private function addBugzillaId(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'int');
        $field->addAttribute('ID', 'F1');
        $field->addAttribute('rank', '0');
        $field->addChild('name', 'bugzilla_id');
        $field->addChild('label', 'Bugzilla Id');
    }


    private function addSubmittedBy(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'subby');
        $field->addAttribute('ID', 'F2');
        $field->addAttribute('rank', '1');
        $field->addChild('name', 'submitted_by');
        $field->addChild('label', 'Submitted by');
    }

    private function addSubmittedOn(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'subon');
        $field->addAttribute('ID', 'F3');
        $field->addAttribute('rank', '2');
        $field->addChild('name', 'submitted_on');
        $field->addChild('label', 'Submitted on');
    }

    private function addTitle(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'string');
        $field->addAttribute('ID', 'F4');
        $field->addAttribute('rank', '3');
        $field->addChild('name', 'summary');
        $field->addChild('label', 'Summary');
    }

    private function addLastUpdateBy(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'luby');
        $field->addAttribute('ID', 'F5');
        $field->addAttribute('rank', '5');
        $field->addChild('name', 'last_update_by');
        $field->addChild('label', 'Last update by');
    }

    private function addLastUpdateOn(SimpleXMLElement $form_elements) {
        $field = $form_elements->addChild('formElement');
        $field->addAttribute('type', 'lud');
        $field->addAttribute('ID', 'F6');
        $field->addAttribute('rank', '6');
        $field->addChild('name', 'last_update_on');
        $field->addChild('label', 'Last update on');
    }

    private function addSemantics(SimpleXMLElement $tracker) {
        $semantics = $tracker->addChild('semantics');
        $title = $semantics->addChild('semantic');
        $title->addAttribute('type', 'title');
        $title->addChild('shortname', 'title');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', 'F4');
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
        $this->addColumnToTableRenderer($columns, 'F1');
        $this->addColumnToTableRenderer($columns, 'F2');
        $this->addColumnToTableRenderer($columns, 'F3');
        $this->addColumnToTableRenderer($columns, 'F4');
        $this->addColumnToTableRenderer($columns, 'F6');
    }

    private function addColumnToTableRenderer(SimpleXMLElement $columns, $name) {
        $field = $columns->addChild('field');
        $field->addAttribute('REF', $name);
    }

    private function addPermissions(SimpleXMLElement $tracker) {
        $permissions = $tracker->addChild('permissions');
        $this->addPermissionOnTracker($permissions, 'PLUGIN_TRACKER_ACCESS_FULL', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, 'F1', 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, 'F2', 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, 'F3', 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, 'F5', 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, 'F6', 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');

        $this->addPermissionOnField($permissions, 'F4', 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, 'F4', 'PLUGIN_TRACKER_FIELD_SUBMIT', 'UGROUP_REGISTERED');
        $this->addPermissionOnField($permissions, 'F4', 'PLUGIN_TRACKER_FIELD_UPDATE', 'UGROUP_REGISTERED');
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
        $this->addBugzillaIdData($bugzilla_bug, $tuleap_changeset);
        $this->addTitleData($bugzilla_bug, $tuleap_changeset);
    }

    private function addBugzillaIdData(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_changeset) {
        $field_change = $tuleap_changeset->addChild('field_change');
        $field_change->addAttribute('field_name', 'bugzilla_id');
        $field_change->addAttribute('type', 'int');
        $field_change->addChild('value', (int) $bugzilla_bug->bug_id);
    }

    private function addTitleData(SimpleXMLElement $bugzilla_bug, SimpleXMLElement $tuleap_changeset) {
        $field_change = $tuleap_changeset->addChild('field_change');
        $field_change->addAttribute('field_name', 'summary');
        $field_change->addAttribute('type', 'string');
        $field_change->addChild('value', (string) $bugzilla_bug->short_desc);
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
