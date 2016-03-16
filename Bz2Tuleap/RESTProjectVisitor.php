<?php

namespace Bz2Tuleap\Tuleap;

class RESTProjectVisitor {

    public function visit(Project $project) {
        $tracker = $project->getTracker();
        $tracker->accept(new RESTTrackerVisitor());
    }
}
