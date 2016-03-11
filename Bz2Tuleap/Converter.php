<?php

namespace Bz2Tuleap;

use SimpleXMLElement;

class Converter {

    public function convert($source_file, $output_dir) {
        $converter = new Bugzilla\Converter($this->getDataDir($output_dir));
        $this->serialize(
            $converter->getProject(simplexml_load_file($source_file)),
            $output_dir
        );
    }

    private function getDataDir($output_dir) {
        $data_dir = $output_dir.'/data';
        if (! is_dir($data_dir)) {
            mkdir($data_dir);
        }
        return $data_dir;
    }

    private function serialize(Tuleap\Project $project, $output_dir) {
        list($project_xml, $users_xml) = $project->toXml();

        $this->saveTo($project_xml, $output_dir.'/project.xml');
        $this->saveTo($users_xml, $output_dir.'/users.xml');
    }

    private function saveTo(SimpleXMLElement $xml, $path) {
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        file_put_contents($path, $dom->saveXML());
    }
}
