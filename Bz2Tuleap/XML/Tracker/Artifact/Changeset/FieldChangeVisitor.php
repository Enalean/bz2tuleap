<?php

namespace Bz2Tuleap\XML\Tracker\Artifact\Changeset;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;
use Bz2Tuleap\XML\CData;

class FieldChangeVisitor implements Changeset\IFieldChangeVisitor {

    /**
     * @var SimpleXMLElement
     */
    private $changeset_xml;

    public function __construct(SimpleXMLElement $changeset_xml) {
        $this->changeset_xml = $changeset_xml;
    }

    private function getChangeXML(Changeset\FieldChange $field_change) {
        $field_change_xml = $this->changeset_xml->addChild('field_change');
        $field_change_xml->addAttribute('field_name', $field_change->getName());
        $field_change_xml->addAttribute('type', $field_change->getType());
        return $field_change_xml;
    }

    public function visitScalar(Changeset\FieldChange $field_change) {
        $field_change_xml = $this->getChangeXML($field_change);
        CData::addChild($field_change_xml, 'value', $field_change->getValue());
    }

    public function visitCC(Changeset\FieldChange $field_change) {
        $field_change_xml = $this->getChangeXML($field_change);
        $field_change_xml->addAttribute('bind', 'users');
        foreach ($field_change->getValue() as $value) {
            $value = $field_change_xml->addChild('value', $value);
            $value->addAttribute('format', 'label');
        }
    }

    public function visitFile(Changeset\FieldChange $field_change) {
        $field_change_xml = $this->getChangeXML($field_change);
        $value = $field_change_xml->addChild('value');
        $file = $field_change->getValue();
        $value->addAttribute('ref', $file['id']);
    }

    public function visitList(Changeset\FieldChange $field_change) {
        $field_change_xml = $this->getChangeXML($field_change);
        $field_change_xml->addAttribute('bind', 'static');
        $value = $field_change_xml->addChild('value', $field_change->getValue());
        $value->addAttribute('format', 'id');
    }

    public function visitUserList(Changeset\FieldChange $field_change) {
        $field_change_xml = $this->getChangeXML($field_change);
        $field_change_xml->addAttribute('bind', 'users');
        $value = $field_change_xml->addChild('value', $field_change->getValue());
        $value->addAttribute('format', 'username');
    }
}

