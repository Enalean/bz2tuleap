<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

interface IFieldChangeVisitor {

    public function visitScalar(FieldChange $field_change);

    public function visitList(FieldChange $field_change);

    public function visitUserList(FieldChange $field_change);

    public function visitFile(FieldChange $field_change);

    public function visitCC(FieldChange $field_change);
}
