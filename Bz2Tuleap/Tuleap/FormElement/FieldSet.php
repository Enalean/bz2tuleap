<?php

namespace Bz2Tuleap\Tuleap\FormElement;

use Bz2Tuleap\Tuleap\Field\Field;
use Bz2Tuleap\Tuleap\Field\NoFieldPermissions;
use Bz2Tuleap\Tuleap\Field\NoProperties;
use Bz2Tuleap\Tuleap\IdMapper;
use Bz2Tuleap\Tuleap\StructureField;
use SimpleXMLElement;

class FieldSet implements IFormElement {

    private $label;

    /**
     * @var IdMapper
     */
    private $mapper;
    private $type;

    private $children = array();

    public function __construct(IdMapper $mapper, $label, array $childen) {
        $this->children = $childen;
        $this->type     = 'fieldset';
        $this->mapper   = $mapper;
        $this->label    = $label;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $field = new StructureField(
            new Field($this->mapper, $this->type, 'struct'.$rank, $this->label, new NoProperties(), new NoFieldPermissions()),
            $this->children
        );
        $field->toXml($parent, $rank);
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
        foreach($this->children as $child) {
            $child->permissionsToXml($parent);
        }
    }
}