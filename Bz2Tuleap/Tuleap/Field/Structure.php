<?php

namespace Bz2Tuleap\Tuleap\Field
;

use SimpleXMLElement;

class Structure  {

    private $children;
    private $field;

    public function __construct($field, $children) {
        $this->field    = $field;
        $this->children = $children;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $xml = $this->field->toXml($parent, $rank);
        $form_elements = $xml->addChild('formElements');
        $sub_rank = 0;
        foreach($this->children as $child) {
            $child->toXml($form_elements, $sub_rank++);
        }
    }
}