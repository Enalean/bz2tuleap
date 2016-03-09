<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class ScalarFieldChange {

    private $value;
    private $type;
    private $name;

    public function __construct($name, $type, $value) {
        $this->name  = $name;
        $this->type  = $type;
        $this->value = $value;
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->name);
        $field_change->addAttribute('type', $this->type);
        $field_change->addChild('value', $this->value);
    }
}
