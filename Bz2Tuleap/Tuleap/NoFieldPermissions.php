<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class NoFieldPermissions implements IPermissions {

    public function toXml(SimpleXMLElement $permissions, IField $field) {
    }
}