<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Field {

    private $label;
    private $name;
    private $type;
    private $field_id;

    public function __construct($field_id, $type, $name, $label) {
        $this->field_id = $field_id;
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $field = $parent->addChild('formElement');
        $field->addAttribute('type', $this->type);
        $field->addAttribute('ID', $this->field_id);
        $field->addAttribute('rank', $rank);
        $field->addChild('name', $this->name);
        $field->addChild('label', $this->label);
        return $field;
    }
}
