<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class ScalarFieldChange extends FieldChange {

    public function accept(IFieldChangeVisitor $visitor) {
        $visitor->visitScalar($this);
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
