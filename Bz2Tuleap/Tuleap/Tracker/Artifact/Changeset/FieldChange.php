<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

abstract class FieldChange {

    private $value;
    private $type;
    private $name;

    public function __construct($name, $type, $value) {
        $this->name  = $name;
        $this->type  = $type;
        $this->value = $value;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getValue() {
        return $this->value;
    }
}
