<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class Changeset {

    private $field_changes;
    private $submitted_by;
    private $submitted_on;
    /** @var Comment */
    private $comment;

    public function __construct($submitted_on, $submitted_by, $comment, array $field_changes) {
        $this->submitted_on  = $submitted_on;
        $this->submitted_by  = $submitted_by;
        $this->comment       = new Comment($submitted_on, $submitted_by, $comment);
        $this->field_changes = $field_changes;
    }

    public function getSubmittedOn() {
        return $this->submitted_on;
    }

    public function getSubmittedBy() {
        return $this->submitted_by;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getFieldChanges() {
        return $this->field_changes;
    }

    public function accept(IChangesetVisitor $visitor) {
        $visitor->visit($this);
    }

    public function cleanUp(array $artifacts) {
        $this->field_changes = array_filter($this->field_changes, function ($change) use ($artifacts) {
            return $change->isValid($artifacts);
        });
    }
}
