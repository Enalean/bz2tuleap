<?php

namespace Bz2Tuleap;

use Bz2Tuleap\Bugzilla\BugzillaUserMapper;
use Bz2Tuleap\Bugzilla\BugzillaParser;
use Bz2Tuleap\Jira\JiraParser;
use Bz2Tuleap\Jira\JiraUserMapper;
use SimpleXMLElement;

class Converter {

    const JIRA     = 'jira';
    const BUGZILLA = 'bugzilla';

    private $tool_names = array(
        self::BUGZILLA,
        self::JIRA
    );

    public function convert($source_file, $output_dir, $tool_name) {
        $this->checkToolName($tool_name);

        $xml_content = simplexml_load_file($source_file);
        $data_dir    = $output_dir.'/data';

        if (! is_dir($data_dir)) {
            mkdir($data_dir);
        }

        if ($tool_name === self::JIRA) {
            $user_mapper = new JiraUserMapper();
            $parser      = new JiraParser($user_mapper);
        } else {
            $user_mapper = new BugzillaUserMapper();
            $parser      = new BugzillaParser($user_mapper, $data_dir);
        }

        $project = new Tuleap\Project($user_mapper, $parser);

        list($project_xml, $users_xml) = $project->convert($xml_content);

        $this->saveTo($project_xml, $output_dir.'/project.xml');
        $this->saveTo($users_xml, $output_dir.'/users.xml');
    }

    private function checkToolName($tool_name)
    {
        if (! in_array($tool_name, $this->tool_names)) {
            echo 'The tool you want to import is not yet supported';
            echo PHP_EOL;
            echo 'The tool currently supported are: ' . implode(',', $this->tool_names);
            echo PHP_EOL;
            exit(1);
        }
    }

    private function saveTo(SimpleXMLElement $xml, $path)
    {
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        file_put_contents($path, $dom->saveXML());
    }
}
