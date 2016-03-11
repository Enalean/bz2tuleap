<?php

namespace Bz2Tuleap\Tuleap\Field;

use SimpleXMLElement;

class DefaultPermissions implements IPermissions {

    public function toXml(SimpleXMLElement $permissions, IField $field) {
        $this->addPermissionOnField($permissions, $field->getReference(), 'PLUGIN_TRACKER_FIELD_READ', 'UGROUP_ANONYMOUS');
        $this->addPermissionOnField($permissions, $field->getReference(), 'PLUGIN_TRACKER_FIELD_SUBMIT', 'UGROUP_REGISTERED');
        $this->addPermissionOnField($permissions, $field->getReference(), 'PLUGIN_TRACKER_FIELD_UPDATE', 'UGROUP_REGISTERED');
    }

    private function addPermissionOnField(SimpleXMLElement $permissions, $field, $type, $ugroup) {
        $permission = $permissions->addChild('permission');
        $permission->addAttribute('scope', 'field');
        $permission->addAttribute('REF', $field);
        $permission->addAttribute('ugroup', $ugroup);
        $permission->addAttribute('type', $type);
    }
}