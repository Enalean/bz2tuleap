<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class UsersSelectBoxFieldChange {

    private $value;
    private $name;

    public function __construct($name, $value) {
        $this->name  = $name;
        $this->value = $value;
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->name);
        $field_change->addAttribute('type', 'list');
        $field_change->addAttribute('bind', 'users');
        $value = $field_change->addChild('value', $this->value);
        $value->addAttribute('format', 'username');
    }

    public function isValid(array $artifacts) {
        return $this->value != '';
    }
}
