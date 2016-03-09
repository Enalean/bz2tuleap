<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class FieldSet  {

    private $label;

    /**
     * @var IdMapper
     */
    private $mapper;
    private $type;

    private $children = array();

    public function __construct(IdMapper $mapper, $label, array $childen) {
        $this->children = $childen;
        $this->type     = 'fieldset';
        $this->mapper   = $mapper;
        $this->label    = $label;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $field = new Field($this->mapper, $this->type, 'struct'.$rank, $this->label);
        $xml = $field->toXml($parent, $rank);
        $form_elements = $xml->addChild('formElements');
        $sub_rank = 0;
        foreach($this->children as $child) {
            $child->toXml($form_elements, $sub_rank++);
        }
    }
}