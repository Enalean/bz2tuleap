<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

use SimpleXMLElement;

class UsersSelectBoxFieldChange extends ListFieldChange {

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->getName());
        $field_change->addAttribute('type', 'list');
        $field_change->addAttribute('bind', 'users');
        $value = $field_change->addChild('value', $this->getValue());
        $value->addAttribute('format', 'username');
    }

    public function isValid(array $artifacts) {
        return $this->getValue() != '';
    }
}
