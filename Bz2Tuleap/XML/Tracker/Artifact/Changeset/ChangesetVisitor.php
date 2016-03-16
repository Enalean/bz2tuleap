<?php

namespace Bz2Tuleap\XML\Tracker\Artifact\Changeset;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class ChangesetVisitor implements Changeset\IChangesetVisitor {

    /**
     * @var SimpleXMLElement
     */
    private $artifact_xml;

    public function __construct(SimpleXMLElement $artifact_xml) {
        $this->artifact_xml = $artifact_xml;
    }

    public function visit(Changeset\Changeset $changeset) {
        $changeset_xml = $this->artifact_xml->addChild('changeset');
        $this->addSubmittedInfo($changeset_xml, $changeset->getSubmittedBy(), $changeset->getSubmittedOn());
        $changeset->getComment()->toXml($changeset_xml);
        foreach($changeset->getFieldChanges() as $change) {
            $field_change_visitor = new FieldChangeVisitor($changeset_xml);
            $change->accept($field_change_visitor);
        }
    }

    private function addSubmittedInfo(SimpleXMLElement $node, $who, $when) {
        $submitted_by = $node->addChild('submitted_by', $who);
        $submitted_by->addAttribute('format', 'username');
        $submitted_on = $node->addChild('submitted_on', $when);
        $submitted_on->addAttribute('format', 'ISO8601');
    }

}
