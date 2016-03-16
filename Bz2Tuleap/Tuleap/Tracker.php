<?php

namespace Bz2Tuleap\Tuleap;

use Bz2Tuleap\Tuleap\Tracker\Field;
use Bz2Tuleap\Tuleap\Tracker\Rule;

use SimpleXMLElement;

class Tracker {

    /**
     * @var Rules
     */
    private $rules;

    /**
     * @var array
     */
    private $semantics;

    /**
     * @var FormElements
     */
    private $fields;
    private $artifacts;
    private $report_columns;

    public function __construct(Field\FormElements $fields, array $report_columns, array $semantics, Rule\Rules $rules, array $artifacts) {
        $this->fields         = $fields;
        $this->report_columns = $report_columns;
        $this->semantics      = $semantics;
        $this->artifacts      = $artifacts;
        $this->rules = $rules;
    }

    public function getField() {
        return $this->fields;
    }

    /**
     * @return Bz2Tuleap\Tuleap\Tracker\Artifact\Artifact[]
     */
    public function getArtifacts() {
        return $this->artifacts;
    }

    public function accept($visitor) {
        $visitor->visit($this);
    }

    public function toXml(SimpleXMLElement $tuleap_xml) {
        $trackers = $tuleap_xml->addChild('trackers');
        $this->addOneTracker($trackers);
    }

    private function addOneTracker(SimpleXMLElement $trackers) {
        $tracker = $trackers->addChild('tracker');
        $tracker->addAttribute('id', 'T1');
        $tracker->addAttribute('parent_id', '0');
        $tracker->addAttribute('instantiate_for_new_projects', '1');
        $this->addTrackerMetadata($tracker);
        $artifacts = $tracker->addChild('artifacts');
        foreach ($this->artifacts as $artifact) {
            $artifact->toXml($artifacts);
        }
    }

    private function addTrackerMetadata(SimpleXMLElement $tracker) {
        $tracker->addChild('name', 'Bugs');
        $tracker->addChild('item_name', 'bug');
        $tracker->addChild('description', 'Bugs and requests');
        $tracker->addChild('color', 'inca_silver');
        $tracker->addChild('cannedResponses');
        $this->fields->toXml($tracker);
        $this->addSemantics($tracker);
        $this->rules->toXml($tracker);
        $this->addReports($tracker);
        $this->addPermissions($tracker, $this->fields);
    }

    private function addPermissions(SimpleXMLElement $tracker, $fields) {
        $permissions = $tracker->addChild('permissions');
        $this->addPermissionOnTracker($permissions, 'PLUGIN_TRACKER_ACCESS_FULL', 'UGROUP_ANONYMOUS');

        $fields->permissionsToXml($permissions);
    }

    private function addSemantics(SimpleXMLElement $tracker) {
        $semantics = $tracker->addChild('semantics');
        foreach ($this->semantics as $semantic) {
            $semantic->toXml($semantics);
        }
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
        foreach ($this->report_columns as $column) {
            $this->addColumnToTableRenderer($columns, $column->getReference());
        }
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
}
