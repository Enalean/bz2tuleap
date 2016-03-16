<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

use SimpleXMLElement;

class CCFieldChange extends FieldChange {

    public function __construct($name, $values) {
        parent::__construct($name, 'open_list', $values);
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->getName());
        $field_change->addAttribute('type', 'open_list');
        $field_change->addAttribute('bind', 'users');
        foreach ($this->getValue() as $value) {
            $value = $field_change->addChild('value', $value);
            $value->addAttribute('format', 'label');
        }
    }

    public function isValid(array $artifacts) {
        return count($this->getValue()) > 0;
    }
}
