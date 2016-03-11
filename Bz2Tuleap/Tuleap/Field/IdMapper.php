<?php

namespace Bz2Tuleap\Tuleap\Field;

class IdMapper {
    private $id_counter = 1;
    private $map        = array();
    private $prefix;

    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    public function getReference($value) {
        return $this->prefix.$this->getId($value);
    }

    public function getId($value) {
        if (isset($this->map[$value])) {
            return $this->map[$value];
        }
        return null;
    }

    public function map($value) {
        $this->map[$value] = $this->id_counter++;
        return $this->getReference($value);
    }
}
