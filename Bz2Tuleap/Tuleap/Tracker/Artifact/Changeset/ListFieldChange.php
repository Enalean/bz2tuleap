<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

use SimpleXMLElement;

class ListFieldChange extends FieldChange {

    public function __construct($name, $value) {
        parent::__construct($name, 'list', $value);
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->getName());
        $field_change->addAttribute('type', 'list');
        $field_change->addAttribute('bind', 'static');
        $value = $field_change->addChild('value', $this->getValue());
        $value->addAttribute('format', 'id');
    }

    public function isValid(array $artifacts) {
        return $this->getValue() != '';
    }
}
