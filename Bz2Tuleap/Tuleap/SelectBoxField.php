<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class SelectBoxField implements IField, IFormElement {

    /** @var Field */
    private $field;
    private $values = array();
    private $value_ids = array();

    public function __construct(IdMapper $field_mapper, IdMapper $value_mapper, $name, $label, array $values, IPermissions $permissions) {
        $this->field = new Field($field_mapper, 'sb', $name, $label, new NoProperties(), $permissions);
        foreach ($values as $value) {
            $value_mapper->map($label);
            $this->values[$value]    = $value_mapper->getReference($label);
            $this->value_ids[$value] = $value_mapper->getId($label);
        }
    }

    public function getReference() {
        return $this->field->getReference();
    }

    public function getValueId($label) {
        if ($label == null) {
            return null;
        }
        return $this->value_ids[$label];
    }

    public function getValueReference($label) {
        if ($label === null) {
            return null;
        }
        return $this->values[$label];
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $xml = $this->field->toXml($parent, $rank);
        $bind = $xml->addChild('bind');
        $bind->addAttribute('type', 'static');
        $bind->addAttribute('is_rank_alpha', '0');
        $items = $bind->addChild('items');
        foreach ($this->values as $label => $id) {
            $this->addBindItem($items, $label, $id);
        }
    }
    
    private function addBindItem(SimpleXMLElement $items, $label, $id) {
        $item = $items->addChild('item');
        $item->addAttribute('ID', $id);
        $item->addAttribute('label', $label);
        $item->addAttribute('is_hidden', 0);
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
        $this->field->permissionsToXml($parent);
    }
}