<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

interface IFormElement
{
    public function toXml(SimpleXMLElement $parent, $rank);

    public function permissionsToXml(SimpleXMLElement $parent);
}
