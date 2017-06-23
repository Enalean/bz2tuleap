<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class OpenListField implements IField, IFormElement {

    /** @var Field */
    private $field;

    public function __construct(IdMapper $field_mapper, $name, $label, IPermissions $permissions) {
        $this->field = new Field($field_mapper, 'tbl', $name, $label, new NoProperties(), $permissions);
    }

    public function getReference() {
        return $this->field->getReference();
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $xml = $this->field->toXml($parent, $rank);
        $bind = $xml->addChild('bind');
        $bind->addAttribute('type', 'static');
        $bind->addAttribute('is_rank_alpha', '0');
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
        $this->field->permissionsToXml($parent);
    }
}