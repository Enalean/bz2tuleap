<?php

namespace Bz2Tuleap;

use Bz2Tuleap\Jira\JiraParser;
use Bz2Tuleap\Jira\JiraUserMapper;
use SimpleXMLElement;

class Converter {

    public function convert($source_file, $output_dir) {
        $xml_content = simplexml_load_file($source_file);

        $data_dir = $output_dir.'/data';

        if (! is_dir($data_dir)) {
            mkdir($data_dir);
        }

        $user_mapper = new JiraUserMapper();
        $parser      = new JiraParser($user_mapper);

        $project = new Tuleap\Project($user_mapper, $parser);

        list($project_xml, $users_xml) = $project->convert($xml_content, $data_dir);

        $this->saveTo($project_xml, $output_dir.'/project.xml');
        $this->saveTo($users_xml, $output_dir.'/users.xml');
    }

    private function saveTo(SimpleXMLElement $xml, $path) {
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        file_put_contents($path, $dom->saveXML());
    }
}
