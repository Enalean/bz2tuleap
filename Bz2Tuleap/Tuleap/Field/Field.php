<?php

namespace Bz2Tuleap\Tuleap\Field;

use Bz2Tuleap\Tuleap\IdMapper;
use Bz2Tuleap\Tuleap\IFormElement;
use SimpleXMLElement;

class Field implements IField, IFormElement {

    /**
     * @var IProperties
     */
    private $properties;
    private $permissions;
    private $label;
    private $name;
    private $type;
    private $field_id;
    private $reference;

    public function __construct(IdMapper $mapper, $type, $name, $label, IProperties $properties, IPermissions $permissions) {
        $this->field_id    = $mapper->map($name);
        $this->reference   = $mapper->getReference($name);
        $this->name        = $name;
        $this->type        = $type;
        $this->label       = $label;
        $this->properties  = $properties;
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
        $field->addChild('name', $this->name);
        $field->addChild('label', $this->label);
        $this->properties->toXml($field);
        return $field;
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
        $this->permissions->toXml($parent, $this);
    }
}
