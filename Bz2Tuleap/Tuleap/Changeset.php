<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

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

    public function toXml(SimpleXMLElement $parent) {
        $changeset = $parent->addChild('changeset');
        $this->addSubmittedInfo($changeset, $this->submitted_by, $this->submitted_on);
        $this->comment->toXml($changeset);
        foreach($this->field_changes as $change) {
            $change->toXml($changeset);
        }
    }

    private function addSubmittedInfo(SimpleXMLElement $node, $who, $when) {
        $submitted_by = $node->addChild('submitted_by', $who);
        $submitted_by->addAttribute('format', 'username');
        $submitted_on = $node->addChild('submitted_on', $when);
        $submitted_on->addAttribute('format', 'ISO8601');
    }

    public function cleanUp(array $artifacts) {
        $this->field_changes = array_filter($this->field_changes, function ($change) use ($artifacts) {
            return $change->isValid($artifacts);
        });
    }
}
