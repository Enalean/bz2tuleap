<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class SelectBoxField  {

    /** @var IdMapper */
    private $value_mapper;

    /** @var Field */
    private $field;
    private $values = array();

    public function __construct(IdMapper $field_mapper, IdMapper $value_mapper, $name, $label, array $values) {
        $this->field        = new Field($field_mapper, 'sb', $name, $label);
        $this->values       = $values;
        $this->value_mapper = $value_mapper;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $xml = $this->field->toXml($parent, $rank);
        $bind = $xml->addChild('bind');
        $bind->addAttribute('type', 'static');
        $bind->addAttribute('is_rank_alpha', '0');
        $items = $bind->addChild('items');
        foreach ($this->values as $value) {
            $this->addBindItem($items, $value);
        }
    }

    private function addBindItem(SimpleXMLElement $items, $label) {
        $item = $items->addChild('item');
        $item->addAttribute('ID', $this->value_mapper->map($label));
        $item->addAttribute('label', $label);
        $item->addAttribute('is_hidden', 0);
    }
}