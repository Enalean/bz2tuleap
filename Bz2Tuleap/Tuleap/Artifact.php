<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Artifact {

    /**
     * @var FilesData
     */
    private $files;

    /**
     * @var Changeset[]
     */
    private $changesets;
    private $id;

    public function __construct($id, array $changesets, FilesData $files) {
        $this->id         = $id;
        $this->changesets = $changesets;
        $this->files      = $files;
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
        $this->files->toXml($xml);
    }

    public function cleanUp(array $artifacts) {
        array_walk($this->changesets, function ($changeset) use ($artifacts) {
            $changeset->cleanUp($artifacts);
        });
    }
}
