<?php

namespace Bz2Tuleap\Tuleap\Semantic;

use Bz2Tuleap\Tuleap\Field\UsersSelectBoxField;
use SimpleXMLElement;

class AssignedToSemantic {

    /**
     * @var UsersSelectBoxField
     */
    private $field;

    public function __construct(UsersSelectBoxField $field) {
        $this->field = $field;
    }

    public function toXml(SimpleXMLElement $parent) {
        $title = $parent->addChild('semantic');
        $title->addAttribute('type', 'contributor');
        $title->addChild('shortname', 'contributor');
        $title->addChild('label');
        $title->addChild('description');
        $field = $title->addChild('field');
        $field->addAttribute('REF', $this->field->getReference());
    }
}