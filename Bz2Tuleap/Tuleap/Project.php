<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Project {

    public function convert(SimpleXMLElement $bugzilla_xml) {
        $tuleap_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                             <project />');

        $this->projectAttributes($tuleap_xml);
        $this->addServices($tuleap_xml);
        $tracker = new Tracker();
        $tracker->convert($bugzilla_xml, $tuleap_xml);

        return $tuleap_xml;
    }

    private function projectAttributes(SimpleXMLElement $tuleap_xml) {
        $tuleap_xml->addAttribute('access', 'public');
        $tuleap_xml->addAttribute('description', '');
        $tuleap_xml->addAttribute('full-name', 'Bugzilla import');
        $tuleap_xml->addAttribute('unix-name', 'bz-import');

        $tuleap_xml->addChild('long-description', '');
    }

    private function addServices(SimpleXMLElement $tuleap_xml) {
        $services = $tuleap_xml->addChild('services');
        $service = $services->addChild('service');
        $service->addAttribute('shortname', 'plugin_tracker');
        $service->addAttribute('enabled', 'true');
    }
}
