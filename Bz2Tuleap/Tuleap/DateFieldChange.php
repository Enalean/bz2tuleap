<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class DateFieldChange {

    private $value;
    private $name;

    public function __construct($name, $value) {
        $this->name  = $name;
        $this->value = $value;
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->name);
        $field_change->addAttribute('type', 'date');
        $value_node = CData::addChild($field_change, 'value', $this->value);
        $value_node->addAttribute('format', 'ISO8601');
    }

    public function isValid(array $artifacts) {
        return $this->value != '';
    }
}
