<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class CData {

    public static function addChild(SimpleXMLElement $parent_node, $node_name, $node_value) {
        $node     = $parent_node->addChild($node_name);
        $dom_node = dom_import_simplexml($node);
        $document = $dom_node->ownerDocument;
        $value    = SupportedXmlCharEncoding::getXMLCompatibleString($node_value);
        $cdata    = $document->createCDATASection($value);
        $dom_node->appendChild($cdata);
        return $node;
    }
}
