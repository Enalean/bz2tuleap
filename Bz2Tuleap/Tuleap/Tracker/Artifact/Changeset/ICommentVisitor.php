<?php

namespace Bz2Tuleap\Tuleap\Tracker\Artifact\Changeset;

interface ICommentVisitor {

    public function visit(Comment $changeset);
}
