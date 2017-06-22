<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

interface ForeignParserInterface
{
    public function getTrackerFromBugzilla(SimpleXMLElement $bugzilla_xml);
}