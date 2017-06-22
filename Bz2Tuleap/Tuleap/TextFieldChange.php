<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class TextFieldChange extends ScalarFieldChange {

    const HTML = 'html';
    const TEXT = 'text';

    /**
     * @var
     */
    private $format;

    public function __construct($name, $type, $value, $format) {
        parent::__construct($name, $type, $value);
        $this->format = $format;
    }

    protected function toXmlValue(SimpleXMLElement $field_change) : SimpleXMLElement
    {
        $value_node = parent::toXmlValue($field_change);
        $value_node->addAttribute('format', $this->format);
        return $value_node;
    }
}
