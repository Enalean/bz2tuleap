<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

interface IChangesetVisitor {

    public function visit(Changeset $changeset);
}
