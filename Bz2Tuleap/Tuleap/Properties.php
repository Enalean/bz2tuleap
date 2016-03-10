<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Properties implements IProperties {

    /**
     * @var array
     */
    private $properties;

    public function __construct(array $properties) {
        $this->properties = $properties;
    }

    public function toXml(SimpleXMLElement $parent) {
        $properties = $parent->addChild('properties');
        foreach ($this->properties as $key => $value) {
            $properties->addAttribute($key, $value);
        }
    }
}