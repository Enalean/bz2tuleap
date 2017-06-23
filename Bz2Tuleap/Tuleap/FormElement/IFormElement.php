<?php

namespace Bz2Tuleap\Tuleap\FormElement;

use SimpleXMLElement;

interface IFormElement
{
    public function toXml(SimpleXMLElement $parent, $rank);

    public function permissionsToXml(SimpleXMLElement $parent);
}
