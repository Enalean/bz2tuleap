<?php

namespace Bz2Tuleap\XML;

use SimpleXMLElement;
use Bz2Tuleap\Tuleap;

class ProjectVisitor implements Tuleap\IProjectVisitor {

    private $users_xml;
    private $project_xml;

    public function visit(Tuleap\Project $project) {
        $this->project_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                             <project />');

        $this->projectAttributes($this->project_xml);
        $this->addUserGroups($this->project_xml, $project->getUsers()   );
        $this->addServices($this->project_xml);

        $project->getTracker()->toXml($this->project_xml);

        $this->users_xml = $this->getUsers($project->getUsers());
    }

    public function getProjectXML() {
        return $this->project_xml;
    }

    public function getUsersXML() {
        return $this->users_xml;
    }

    private function projectAttributes(SimpleXMLElement $tuleap_xml) {
        $tuleap_xml->addAttribute('access', 'public');
        $tuleap_xml->addAttribute('description', '');
        $tuleap_xml->addAttribute('full-name', 'Bugzilla import');
        $tuleap_xml->addAttribute('unix-name', 'bz-import');

        $tuleap_xml->addChild('long-description', '');
    }

    private function addUserGroups(SimpleXMLElement $tuleap_xml, array $users) {
        $ugroups = $tuleap_xml->addChild('ugroups');
        $ugroup  = $ugroups->addChild('ugroup');
        $ugroup->addAttribute('name', 'project_members');
        $ugroup->addAttribute('description', '');
        $members = $ugroup->addChild('members');
        foreach ($users as $user) {
            $member = $members->addChild('member', $user['username']);
            $member->addAttribute('format', 'username');
        }
    }

    private function addServices(SimpleXMLElement $tuleap_xml) {
        $services = $tuleap_xml->addChild('services');
        $service = $services->addChild('service');
        $service->addAttribute('shortname', 'plugin_tracker');
        $service->addAttribute('enabled', 'true');
    }

    private function getUsers(array $users) {
        $users_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>
                                       <users />');
        foreach ($users as $user_details) {
            $user = $users_xml->addChild('user');
            $user->addChild('id',       $user_details['id']);
            $user->addChild('username', $user_details['username']);
            $user->addChild('realname', $user_details['realname']);
            $user->addChild('email',    $user_details['email']);
            $user->addChild('ldapid', '');
        }
        return $users_xml;
    }
}
