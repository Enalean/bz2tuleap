<?php

namespace Bz2Tuleap\Tuleap\Field;

use Bz2Tuleap\Tuleap\FormElement\IFormElement;
use Bz2Tuleap\Tuleap\IdMapper;
use SimpleXMLElement;

class CCField implements IField, IFormElement {
    
    private $permissions;
    private $label;
    private $name;
    private $type;
    private $field_id;
    private $reference;

    public function __construct(IdMapper $mapper, $name, $label, IPermissions $permissions) {
        $this->field_id    = $mapper->map($name);
        $this->reference   = $mapper->getReference($name);
        $this->name        = $name;
        $this->type        = 'tbl';
        $this->label       = $label;
        $this->permissions = $permissions;
    }

    public function getReference() {
        return $this->reference;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $field = $parent->addChild('formElement');
        $field->addAttribute('type', $this->type);
        $field->addAttribute('ID', $this->field_id);
        $field->addAttribute('rank', $rank);
        $field->addAttribute('notifications', 1);
        $field->addChild('name', $this->name);
        $field->addChild('label', $this->label);
        $this->addBindOnRegisteredUsers($field);
        return $field;
    }

    private function addBindOnRegisteredUsers($field) {
        $bind = $field->addChild('bind');
        $bind->addAttribute('type', 'users');
        $items = $bind->addChild('items');
        $item = $items->addChild('item');
        $item->addAttribute('label', 'ugroup_2');
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
        $this->permissions->toXml($parent, $this);
    }
}