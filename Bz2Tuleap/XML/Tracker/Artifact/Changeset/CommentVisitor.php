<?php

namespace Bz2Tuleap\XML\Tracker\Artifact\Changeset;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;
use Bz2Tuleap\XML\CData;

class CommentVisitor implements Changeset\ICommentVisitor {
    
    /**
     * @var SimpleXMLElement
     */
    private $changeset_xml;

    public function __construct(SimpleXMLElement $changeset_xml) {
        $this->changeset_xml = $changeset_xml;
    }
    
    public function visit(Changeset\Comment $changeset) {
        $comments = $this->changeset_xml->addChild('comments');
        if ($changeset->getBody() != '') {
            $comment = $comments->addChild('comment');
            $this->addSubmittedInfo($comment, $changeset->getSubmittedBy(), $changeset->getSubmittedOn());
            $body = CData::addChild($comment, 'body', $changeset->getBody());
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
