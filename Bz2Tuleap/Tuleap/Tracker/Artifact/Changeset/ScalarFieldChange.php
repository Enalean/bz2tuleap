<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

use Bz2Tuleap\XML\CData;

use SimpleXMLElement;

class ScalarFieldChange extends FieldChange {

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->getName());
        $field_change->addAttribute('type', $this->getType());
        CData::addChild($field_change, 'value', $this->getValue());
    }

    public function isValid(array $artifacts) {
        if ($this->getType() == 'art_link') {
            foreach ($artifacts as $artifact) {
                if ($artifact->getId() == $this->getValue()) {
                    return true;
                }
            }
            return false;
        } else {
            return $this->getValue() != '';
        }
    }
}
