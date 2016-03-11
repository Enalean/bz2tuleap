<?php

namespace Bz2Tuleap\Tuleap\Artifact\Changeset;

use SimpleXMLElement;

class FileFieldChange {

    private $file;
    private $name;

    public function __construct($name, $file) {
        $this->name  = $name;
        $this->file = $file;
    }

    public function toXml(SimpleXMLElement $parent) {
        $field_change = $parent->addChild('field_change');
        $field_change->addAttribute('field_name', $this->name);
        $field_change->addAttribute('type', 'file');
        $value = $field_change->addChild('value');
        $value->addAttribute('ref', $this->file['id']);
    }

    public function isValid(array $artifacts) {
        return true;
    }
}
