<?php

namespace Bz2Tuleap\Tuleap\Rule;

use SimpleXMLElement;

class ListRules {

    /**
     * @var ListRule[]
     */
    private $list_rules;

    public function __construct(array $list_rules) {
        $this->list_rules = $list_rules;
    }

    public function toXml(SimpleXMLElement $parent) {
        $list_rules = $parent->addChild('list_rules');
        foreach ($this->list_rules as $rule) {
            $rule->toXml($list_rules);
        }
    }
}