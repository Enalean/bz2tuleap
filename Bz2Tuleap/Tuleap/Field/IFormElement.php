<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

interface IFormElement {
    public function toXml(SimpleXMLElement $parent, $rank);

    public function permissionsToXml(SimpleXMLElement $parent);
}
