<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class StructureField  {

    /** @var Field */
    private $field;
    private $children = array();

    public function __construct(Field $field, array $childen) {
        $this->field = $field;
        $this->children = $childen;
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