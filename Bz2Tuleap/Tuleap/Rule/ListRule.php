<?php

namespace Bz2Tuleap\Tuleap\Rule;

use Bz2Tuleap\Tuleap\Field;
use SimpleXMLElement;

class ListRule {

    private $target_value;
    private $source_value;

    /**
     * @var Field\SelectBox
     */
    private $target_field;

    /**
     * @var Field\SelectBox
     */
    private $source_field;

    public function __construct(Field\SelectBox $source_field, Field\SelectBox $target_field, $source_value, $target_value) {
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