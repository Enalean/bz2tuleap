<?php

namespace Bz2Tuleap\Tuleap\Semantic;

use Bz2Tuleap\Tuleap\Field\SelectBoxField;
use SimpleXMLElement;

class StatusSemantic {

    /**
     * @var array
     */
    private $open_values;

    /**
     * @var SelectBoxField
     */
    private $field;

    public function __construct(SelectBoxField $field, array $open_values) {
        $this->field = $field;
        $this->open_values = $open_values;
    }

    public function toXml(SimpleXMLElement $parent) {
        $status = $parent->addChild('semantic');
        $status->addAttribute('type', 'status');
        $status->addChild('shortname', 'status');
        $status->addChild('label');
        $status->addChild('description');
        $field = $status->addChild('field');
        $field->addAttribute('REF', $this->field->getReference());
        $open_values = $status->addChild('open_values');
        foreach($this->open_values as $value) {
            $open_value = $open_values->addChild('open_value');
            $open_value->addAttribute('REF', $value);
        }
    }
}