<?php

namespace Bz2Tuleap\Tuleap\Tracker\Field;

use SimpleXMLElement;

interface IProperties {
    public function toXml(SimpleXMLElement $field);
}