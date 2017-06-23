<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class OpenListFieldChange {

    private $values;
    private $name;

    public function __construct($name, array $values) {
        $this->name   = $name;
        $this->values = $values;
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->name);
        $field_change->addAttribute('type', 'open_list');
        $field_change->addAttribute('bind', 'static');

        foreach ($this->values as $value) {
            $value = $field_change->addChild('value', $value);
            $value->addAttribute('format', 'label');
        }
    }

    public function isValid(array $artifacts) {
        return $this->value != '';
    }
}
