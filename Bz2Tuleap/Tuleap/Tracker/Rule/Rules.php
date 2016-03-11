<?php

namespace Bz2Tuleap\Tuleap\Tracker\Rule;

use SimpleXMLElement;

class Rules {

    /**
     * @var ListRules
     */
    private $list_rules;

    public function __construct(ListRules $list_rules) {
        $this->list_rules = $list_rules;
    }

    public function toXml(SimpleXMLElement $parent) {
        $rules = $parent->addChild('rules');
        $this->list_rules->toXml($rules);
    }
}