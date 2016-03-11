<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

class UsersSelectBox implements IField, IFormElement {

    private $permissions;
    private $label;
    private $name;
    private $field_id;
    private $reference;

    public function __construct(IdMapper $field_mapper, $name, $label, IPermissions $permissions) {
        $this->field_id    = $field_mapper->map($name);
        $this->reference   = $field_mapper->getReference($name);
        $this->name        = $name;
        $this->label       = $label;
        $this->permissions = $permissions;
    }

    public function getReference() {
        return $this->reference;
    }
    
    public function toXml(SimpleXMLElement $parent, $rank) {
        $field = $parent->addChild('formElement');
        $field->addAttribute('type', 'sb');
        $field->addAttribute('ID', $this->field_id);
        $field->addAttribute('rank', $rank);
        $field->addAttribute('notifications', 1);
        $field->addChild('name', $this->name);
        $field->addChild('label', $this->label);
        $bind = $field->addChild('bind');
        $bind->addAttribute('type', 'users');
        $items = $bind->addChild('items');
        $item  = $items->addChild('item');
        $item->addAttribute('label','group_members');
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
         $this->permissions->toXml($parent, $this);
    }
}