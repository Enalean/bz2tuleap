<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class SelectBoxField  {

    /** @var IdMapper */
    private $mapper;

    /** @var Field */
    private $field;
    private $values = array();

    public function __construct(Field $field, array $values, IdMapper $mapper) {
        $this->field  = $field;
        $this->values = $values;
        $this->mapper = $mapper;
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
        $item->addAttribute('ID', $this->mapper->map($label));
        $item->addAttribute('label', $label);
        $item->addAttribute('is_hidden', 0);
    }
}