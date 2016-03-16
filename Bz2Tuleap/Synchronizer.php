<?php

namespace Bz2Tuleap;

class Synchronizer {

    public function sync($source_file, $tmp_dir) {
        $converter = new Bugzilla\Converter($tmp_dir);
        $project = $converter->getProject(simplexml_load_file($source_file));
        $rest_visitor = new Tuleap\RESTProjectVisitor();

        $project->accept($rest_visitor);
    }
}
