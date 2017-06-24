<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

interface ExternalParserInterface
{
    public function getTrackerFromXMLContent(SimpleXMLElement $xml_content);
}