<?php

namespace Bz2Tuleap\Tuleap;

class RESTTrackerVisitor {

    public function visit(Tracker $tracker) {
        foreach ($tracker->getArtifacts() as $artifact) {
            $artifact->accept(new RESTArtifactVisitor($tracker));
        }
    }
}
