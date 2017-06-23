<?php

namespace Bz2Tuleap\Tuleap;

use Bz2Tuleap\Tuleap\Field\Field;
use Bz2Tuleap\Tuleap\Field\NoFieldPermissions;
use Bz2Tuleap\Tuleap\Field\NoProperties;
use SimpleXMLElement;

class Column implements IFormElement {

    /**
     * @var IdMapper
     */
    private $mapper;
    private $type;

    private $children = array();

    public function __construct(IdMapper $mapper, array $childen) {
        $this->children = $childen;
        $this->type     = 'column';
        $this->mapper   = $mapper;
    }

    public function toXml(SimpleXMLElement $parent, $rank) {
        $field = new StructureField(
            new Field($this->mapper, $this->type, 'struct'.$rank, 'struct'.$rank, new NoProperties(), new NoFieldPermissions()),
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