<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Artifact {

    /**
     * @var Changeset[]
     */
    private $changesets;
    private $id;

    public function __construct($id, array $changesets) {
        $this->id = $id;
        $this->changesets = $changesets;
    }

    public function getId() {
        return $this->id;
    }

    public function toXml(SimpleXMLElement $parent) {
        $xml = $parent->addChild('artifact');
        $xml->addAttribute('id', $this->id);
        foreach ($this->changesets as $changeset) {
            $changeset->toXml($xml);
        }
    }

    public function cleanUp(array $artifacts) {
        array_walk($this->changesets, function ($changeset) use ($artifacts) {
            $changeset->cleanUp($artifacts);
        });
    }
}
