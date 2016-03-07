<?php

namespace Bz2Tuleap;

class Converter {

    public function convert($source_file) {
        $bugzilla_xml = simplexml_load_file($source_file);

        $project = new Tuleap\Project();
        $xml_element = $project->convert($bugzilla_xml);

        $dom = dom_import_simplexml($xml_element)->ownerDocument;
        $dom->formatOutput = true;

        echo $dom->saveXML();
    }
}
