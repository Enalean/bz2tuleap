<?php

namespace Bz2Tuleap\XML\Tracker\Artifact;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap\Tracker\Artifact;

class ArtifactVisitor implements Artifact\IArtifactVisitor {

    /**
     * @var SimpleXMLElement
     */
    private $tracker_xml;

    public function __construct(SimpleXMLElement $tracker_xml) {
        $this->tracker_xml = $tracker_xml;
    }

    public function visit(Artifact\Artifact $artifact) {
        $xml = $this->tracker_xml->addChild('artifact');
        $xml->addAttribute('id', $artifact->getId());
        foreach ($artifact->getChangesets() as $changeset) {
            $changeset_visitor = new Changeset\ChangesetVisitor($xml);
            $changeset->accept($changeset_visitor);
        }
        $artifact->getFiles()->toXml($xml);
    }
}
