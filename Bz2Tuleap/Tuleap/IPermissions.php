<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

interface IPermissions {
    public function toXml(SimpleXMLElement $permissions, IField $field);
}