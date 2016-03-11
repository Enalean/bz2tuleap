<?php

namespace Bz2Tuleap\XML;

use SimpleXMLElement;

class CData {

    public static function addChild(SimpleXMLElement $parent_node, $node_name, $node_value) {
        $node     = $parent_node->addChild($node_name);
        $dom_node = dom_import_simplexml($node);
        $document = $dom_node->ownerDocument;
        $value    = SupportedEncoding::getXMLCompatibleString($node_value);
        $cdata    = $document->createCDATASection($value);
        $dom_node->appendChild($cdata);
        return $node;
    }
}
