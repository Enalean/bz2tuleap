<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class ListFieldChange extends FieldChange {

    public function __construct($name, $value) {
        parent::__construct($name, 'list', $value);
    }

    public function accept(IFieldChangeVisitor $visitor) {
        $visitor->visitList($this);
    }

    public function isValid(array $artifacts) {
        return $this->getValue() != '';
    }
}
