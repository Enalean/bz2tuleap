<?php

namespace Bz2Tuleap\Tuleap\Semantic;

use Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

class Title {

    /**
     * @var Field
     */
    private $field;

    public function __construct(Field\Field $field) {
        $this->field = $field;
    }

    public function toXml(SimpleXMLElement $parent) {
        $title = $parent->addChild('semantic');
        $title->addAttribute('type', 'title');
        $title->addChild('shortname', 'title');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', $this->field->getReference());
    }
}