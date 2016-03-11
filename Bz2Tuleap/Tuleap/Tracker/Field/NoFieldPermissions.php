<?php

namespace Bz2Tuleap\Tuleap\Tracker\Field;

use SimpleXMLElement;

class NoFieldPermissions implements IPermissions {

    public function toXml(SimpleXMLElement $permissions, IField $field) {
    }
}