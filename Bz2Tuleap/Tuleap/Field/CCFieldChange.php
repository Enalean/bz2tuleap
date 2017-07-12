<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

class CCFieldChange {

    private $values;
    private $name;

    public function __construct($name, $values) {
        $this->name  = $name;
        $this->values = $values;
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->name);
        $field_change->addAttribute('type', 'open_list');
        $field_change->addAttribute('bind', 'users');
        foreach ($this->values as $value) {
            $value = $field_change->addChild('value', $value);
            $value->addAttribute('format', 'label');
        }
    }

    public function isValid(array $artifacts) {
        return count($this->values) > 0;
    }
}
