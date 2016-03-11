<?php

namespace Bz2Tuleap\Tuleap\Tracker\Field;

use SimpleXMLElement;

interface IFormElement {
    public function toXml(SimpleXMLElement $parent, $rank);

    public function permissionsToXml(SimpleXMLElement $parent);
}
