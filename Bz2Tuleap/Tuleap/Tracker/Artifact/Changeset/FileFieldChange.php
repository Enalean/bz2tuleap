<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

use SimpleXMLElement;

class FileFieldChange extends FieldChange {

    public function __construct($name, $file) {
        parent::__construct($name, 'file', $file);
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->getName());
        $field_change->addAttribute('type', 'file');
        $value = $field_change->addChild('value');
        $file = $this->getValue();
        $value->addAttribute('ref', $file['id']);
    }

    public function isValid(array $artifacts) {
        return true;
    }
}
