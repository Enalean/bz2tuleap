<?php

namespace Bz2Tuleap\Tuleap;

use SimpleXMLElement;

class Project {

    public function convert(SimpleXMLElement $bugzilla_xml) {
        $project_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                             <project />');

        $this->projectAttributes($project_xml);
        $this->addServices($project_xml);
        $tracker = new Tracker();
        $tracker->convert($bugzilla_xml, $project_xml);

        $users_xml = $this->getUsers($tracker);

        return array($project_xml, $users_xml);
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

    private function getUsers(Tracker $tracker) {
        $users = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                             <users />');
        $user_id = 101;
        foreach ($tracker->getUsers() as $email => $user_details) {
            $user = $users->addChild('user');
            $user->addChild('id', $user_id++);
            $at_place = strpos($email, '@');
            if ($at_place !== false) {
                $username = substr($email, 0, ($at_place - 1));
            } else {
                $username = $email;
            }
            $user->addChild('username', $username);
            $user->addChild('realname', $user_details['realname']);
            $user->addChild('email', $email.'@example.com');
            $user->addChild('ldapid', '');
        }
        return $users;
    }
}
