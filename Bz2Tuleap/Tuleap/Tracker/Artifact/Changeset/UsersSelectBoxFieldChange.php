<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class UsersSelectBoxFieldChange extends ListFieldChange {

    public function accept(IFieldChangeVisitor $visitor) {
        $visitor->visitUserList($this);
    }

    public function isValid(array $artifacts) {
        return $this->getValue() != '';
    }
}
