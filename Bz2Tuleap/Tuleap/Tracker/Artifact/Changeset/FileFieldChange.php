<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class FileFieldChange extends FieldChange {

    public function __construct($name, $file) {
        parent::__construct($name, 'file', $file);
    }

    public function accept(IFieldChangeVisitor $visitor) {
        $visitor->visitFile($this);
    }

    public function isValid(array $artifacts) {
        return true;
    }
}
