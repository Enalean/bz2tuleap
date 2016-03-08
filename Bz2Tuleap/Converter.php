<?php

namespace Bz2Tuleap;

use SimpleXMLElement;

class Converter {

    public function convert($source_file, $output_dir) {
        $bugzilla_xml = simplexml_load_file($source_file);

        $project = new Tuleap\Project();
        list($project_xml, $users_xml) = $project->convert($bugzilla_xml);

        $this->saveTo($project_xml, $output_dir.'/project.xml');
        $this->saveTo($users_xml, $output_dir.'/users.xml');
    }

    private function saveTo(SimpleXMLElement $xml, $path) {
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        file_put_contents($path, $dom->saveXML());
    }
}
