<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

interface IProperties
{
    public function toXml(SimpleXMLElement $field);
}