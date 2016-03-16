<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class CCFieldChange extends FieldChange {

    public function __construct($name, $values) {
        parent::__construct($name, 'open_list', $values);
    }

    public function accept(IFieldChangeVisitor $visitor) {
        $visitor->visitCC($this);
    }

    public function isValid(array $artifacts) {
        return count($this->getValue()) > 0;
    }
}
