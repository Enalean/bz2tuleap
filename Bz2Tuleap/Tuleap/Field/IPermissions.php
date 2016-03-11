<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

interface IPermissions {
    public function toXml(SimpleXMLElement $permissions, IField $field);
}