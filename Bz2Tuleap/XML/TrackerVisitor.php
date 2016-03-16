<?php

namespace Bz2Tuleap\XML;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap;

class TrackerVisitor implements Tuleap\ITrackerVisitor {

    /**
     * @var SimpleXMLElement
     */
    private $project_xml;

    public function __construct(SimpleXMLElement $project_xml) {
        $this->project_xml = $project_xml;
    }

    public function visit(Tuleap\Tracker $tracker) {
        $trackers_xml = $this->project_xml->addChild('trackers');
        $this->addOneTracker($trackers_xml, $tracker);
    }

    private function addOneTracker(SimpleXMLElement $trackers_xml, Tuleap\Tracker $tracker) {
        $tracker_xml = $trackers_xml->addChild('tracker');
        $tracker_xml->addAttribute('id', 'T1');
        $tracker_xml->addAttribute('parent_id', '0');
        $tracker_xml->addAttribute('instantiate_for_new_projects', '1');
        $this->addTrackerStructure($tracker_xml, $tracker);
        $artifacts = $tracker_xml->addChild('artifacts');
        foreach ($tracker->getArtifacts() as $artifact) {
            $artifact->toXml($artifacts);
        }
    }

    private function addTrackerStructure(SimpleXMLElement $tracker_xml, Tuleap\Tracker $tracker) {
        $tracker_xml->addChild('name', 'Bugs');
        $tracker_xml->addChild('item_name', 'bug');
        $tracker_xml->addChild('description', 'Bugs and requests');
        $tracker_xml->addChild('color', 'inca_silver');
        $tracker_xml->addChild('cannedResponses');
        $tracker->getField()->toXml($tracker_xml);
        $this->addSemantics($tracker_xml, $tracker);
        $tracker->getRules()->toXml($tracker_xml);
        $this->addReports($tracker_xml, $tracker);
        $this->addPermissions($tracker_xml, $tracker->getField());
    }

    private function addPermissions(SimpleXMLElement $tracker_xml, $fields) {
        $permissions = $tracker_xml->addChild('permissions');
        $this->addPermissionOnTracker($permissions, 'PLUGIN_TRACKER_ACCESS_FULL', 'UGROUP_ANONYMOUS');

        $fields->permissionsToXml($permissions);
    }

    private function addPermissionOnTracker(SimpleXMLElement $permissions, $type, $ugroup) {
        $permission = $permissions->addChild('permission');
        $permission->addAttribute('scope', 'tracker');
        $permission->addAttribute('ugroup', $ugroup);
        $permission->addAttribute('type', $type);
    }

    private function addSemantics(SimpleXMLElement $tracker_xml, Tuleap\Tracker $tracker) {
        $semantics = $tracker_xml->addChild('semantics');
        foreach ($tracker->getSemantics() as $semantic) {
            $semantic->toXml($semantics);
        }
    }

    private function addReports(SimpleXMLElement $tracker_xml, Tuleap\Tracker $tracker) {
        $reports = $tracker_xml->addChild('reports');
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
        foreach ($tracker->getReportColumns() as $column) {
            $this->addColumnToTableRenderer($columns, $column->getReference());
        }
    }

    private function addColumnToTableRenderer(SimpleXMLElement $columns, $name) {
        $field = $columns->addChild('field');
        $field->addAttribute('REF', $name);
    }
}
