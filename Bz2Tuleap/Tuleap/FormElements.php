<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class FormElements implements IFormElement {

    private $children;

    public function __construct(array $children) {
        $this->children = $children;
    }

    public function toXml(SimpleXMLElement $parent, $rank = 0) {
        $form_elements = $parent->addChild('formElements');
        $sub_rank = 0;
        foreach($this->children as $child) {
            $child->toXml($form_elements, $sub_rank++);
        }
    }

    public function permissionsToXml(SimpleXMLElement $parent) {
        foreach($this->children as $child) {
            $child->permissionsToXml($parent);
        }
    }
}