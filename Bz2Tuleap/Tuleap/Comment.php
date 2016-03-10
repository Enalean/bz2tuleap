<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Comment {

    private $body;
    private $submitted_by;
    private $submitted_on;

    public function __construct($submitted_on, $submitted_by, $body) {
        $this->submitted_on = $submitted_on;
        $this->submitted_by = $submitted_by;
        $this->body         = $body;
    }

    public function toXml(SimpleXMLElement $parent) {
        $comments = $parent->addChild('comments');
        if ($this->body != '') {
            $comment = $comments->addChild('comment');
            $this->addSubmittedInfo($comment, $this->submitted_by, $this->submitted_on);
            $body = CData::addChild($comment, 'body', $this->body);
            $body->addAttribute('format', 'text');
        }
    }

    private function addSubmittedInfo(SimpleXMLElement $node, $who, $when) {
        $submitted_by = $node->addChild('submitted_by', $who);
        $submitted_by->addAttribute('format', 'username');
        $submitted_on = $node->addChild('submitted_on', $when);
        $submitted_on->addAttribute('format', 'ISO8601');
    }
}
