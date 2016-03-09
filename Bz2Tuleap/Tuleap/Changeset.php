<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Changeset {

    private $submitted_by;
    private $submitted_on;
    /** @var Comment */
    private $comment;

    public function __construct($submitted_on, $submitted_by, $comment) {
        $this->submitted_on = $submitted_on;
        $this->submitted_by = $submitted_by;
        $this->comment = new Comment($submitted_on, $submitted_by, $comment);
    }

    public function toXml(SimpleXMLElement $parent) {
        $changeset = $parent->addChild('changeset');
        $this->addSubmittedInfo($changeset, $this->submitted_by, $this->submitted_on);
        $this->comment->toXml($changeset);
    }

    private function addSubmittedInfo(SimpleXMLElement $node, $who, $when) {
        $submitted_by = $node->addChild('submitted_by', $who);
        $submitted_by->addAttribute('format', 'username');
        $submitted_on = $node->addChild('submitted_on', $when);
        $submitted_on->addAttribute('format', 'ISO8601');
    }
}
