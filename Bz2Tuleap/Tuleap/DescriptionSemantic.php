<?php

namespace Bz2Tuleap\Tuleap;

use Bz2Tuleap\Tuleap\Field\Field;
use SimpleXMLElement;

class DescriptionSemantic {

    /**
     * @var Field
     */
    private $field;

    public function __construct(Field $field) {
        $this->field = $field;
    }

    public function toXml(SimpleXMLElement $parent) {
        $title = $parent->addChild('semantic');
        $title->addAttribute('type', 'description');
        $title->addChild('shortname', 'description');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', $this->field->getReference());
    }
}