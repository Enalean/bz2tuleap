<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact;

class Artifact {

    /**
     * @var FilesData
     */
    private $files;

    /**
     * @var Artifact\Changeset[]
     */
    private $changesets;
    private $id;

    public function __construct($id, array $changesets, Changeset\FilesData $files) {
        $this->id         = $id;
        $this->changesets = $changesets;
        $this->files      = $files;
    }

    public function getId() {
        return $this->id;
    }

    public function getChangesets() {
        return $this->changesets;
    }

    public function getFiles() {
        return $this->files;
    }

    public function accept($visitor) {
        $visitor->visit($this);
    }

    public function cleanUp(array $artifacts) {
        array_walk($this->changesets, function ($changeset) use ($artifacts) {
            $changeset->cleanUp($artifacts);
        });
    }
}
