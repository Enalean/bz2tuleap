<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

interface ForeignParserInterface
{
    public function getTrackerFromXMLContent(SimpleXMLElement $bugzilla_xmlContent);
}