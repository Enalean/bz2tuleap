<?php

namespace Bz2Tuleap\Tuleap;

use Bz2Tuleap\Tuleap\Field\SelectBoxField;
use SimpleXMLElement;

class ListRule {

    private $target_value;
    private $source_value;

    /**
     * @var SelectBoxField
     */
    private $target_field;

    /**
     * @var SelectBoxField
     */
    private $source_field;

    public function __construct(SelectBoxField $source_field, SelectBoxField $target_field, $source_value, $target_value) {
        $this->source_field = $source_field;
        $this->target_field = $target_field;
        $this->source_value = $source_value;
        $this->target_value = $target_value;
    }

    public function toXml(SimpleXMLElement $parent) {
        $rule = $parent->addChild('rule');
        $source_field = $rule->addChild('source_field');
        $source_field->addAttribute('REF', $this->source_field->getReference());
        $target_field = $rule->addChild('target_field');
        $target_field->addAttribute('REF', $this->target_field->getReference());
        
        $source_value = $rule->addChild('source_value');
        $this->addValueRef($source_value, $this->source_value);
        $target_value = $rule->addChild('target_value');
        $this->addValueRef($target_value, $this->target_value);
    }

    private function addValueRef(SimpleXMLElement $value_node, $ref) {
        if ($ref === null) {
            $value_node->addAttribute('is_none', 1);
        } else {
            $value_node->addAttribute('REF', $ref);
        }
    }
}