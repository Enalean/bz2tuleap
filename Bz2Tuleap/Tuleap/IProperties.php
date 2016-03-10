<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

interface IProperties {
    public function toXml(SimpleXMLElement $field);
}