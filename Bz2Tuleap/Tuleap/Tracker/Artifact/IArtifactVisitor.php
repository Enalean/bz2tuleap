<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact;

interface IArtifactVisitor {

    public function visit(Artifact $tracker);
}
