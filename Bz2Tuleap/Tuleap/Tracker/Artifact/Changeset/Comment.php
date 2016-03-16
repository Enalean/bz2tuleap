<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

class Comment {

    private $body;
    private $submitted_by;
    private $submitted_on;

    public function __construct($submitted_on, $submitted_by, $body) {
        $this->submitted_on = $submitted_on;
        $this->submitted_by = $submitted_by;
        $this->body         = $body;
    }

    public function getSubmittedOn() {
        return $this->submitted_on;
    }

    public function getSubmittedBy() {
        return $this->submitted_by;
    }

    public function getBody() {
        return $this->body;
    }

    public function accept(ICommentVisitor $visitor) {
        $visitor->visit($this);
    }
}
