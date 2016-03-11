<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

use Bz2Tuleap\XML\CData;

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
        CData::addChild($field_change, 'value', $this->value);
    }

    public function isValid(array $artifacts) {
        if ($this->type == 'art_link') {
            foreach ($artifacts as $artifact) {
                if ($artifact->getId() == $this->value) {
                    return true;
                }
            }
            return false;
        } else {
            return $this->value != '';
        }
    }
}
